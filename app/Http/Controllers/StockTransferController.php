<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['fromStore', 'toStore', 'user'])->latest()->paginate(10);
        return view('stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $stores = Store::where('active', true)->get();
        $products = Product::where('active', true)->get();
        return view('stock-transfers.create', compact('stores', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $transfer = StockTransfer::create([
                'transfer_no' => 'TRF-' . strtoupper(uniqid()),
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'date' => $request->date,
                'status' => 'pending',
                'notes' => $request->notes,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('stock-transfers.index')->with('success', 'Stock transfer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating stock transfer: ' . $e->getMessage());
        }
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromStore', 'toStore', 'user', 'items.product']);
        return view('stock-transfers.show', compact('stockTransfer'));
    }

    public function updateStatus(Request $request, StockTransfer $stockTransfer)
    {
        $request->validate([
            'status' => 'required|in:sent,received,cancelled',
        ]);

        if ($stockTransfer->status === 'received' || $stockTransfer->status === 'cancelled') {
            return back()->with('error', 'Transfer already completed or cancelled.');
        }

        try {
            DB::beginTransaction();

            $stockTransfer->update(['status' => $request->status]);

            if ($request->status === 'received') {
                foreach ($stockTransfer->items as $item) {
                    // Deduct from source store
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'store_id' => $stockTransfer->from_store_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'notes' => 'Stock Transfer: ' . $stockTransfer->transfer_no,
                        'user_id' => Auth::id(),
                        'reference_id' => $stockTransfer->id,
                        'reference_type' => StockTransfer::class,
                    ]);

                    // Add to destination store
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'store_id' => $stockTransfer->to_store_id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'notes' => 'Stock Transfer: ' . $stockTransfer->transfer_no,
                        'user_id' => Auth::id(),
                        'reference_id' => $stockTransfer->id,
                        'reference_type' => StockTransfer::class,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('stock-transfers.show', $stockTransfer)->with('success', 'Transfer status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating transfer status: ' . $e->getMessage());
        }
    }
}
