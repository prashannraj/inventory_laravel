<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with(['store', 'user'])->latest()->paginate(10);
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $stores = Store::where('active', true)->get();

        if ($stores->isEmpty()) {
            return redirect()->route('stores.create')
                ->with('error', 'You must create at least one active store before making stock adjustments.');
        }

        $products = Product::where('active', true)->get();
        return view('stock-adjustments.create', compact('stores', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'date' => 'required|date',
            'reason' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $adjustment = StockAdjustment::create([
                'adjustment_no' => 'ADJ-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'store_id' => $data['store_id'],
                'date' => $data['date'],
                'reason' => $data['reason'],
                'user_id' => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                $adjustment->items()->create($item);

                $product = Product::find($item['product_id']);
                
                // Update product quantity (quantity can be positive or negative)
                $product->increment('qty', $item['quantity']);

                // Log stock movement
                $product->stockMovements()->create([
                    'store_id' => $adjustment->store_id,
                    'type' => $item['quantity'] > 0 ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($item['quantity']),
                    'unit_cost' => $product->buying_price,
                    'user_id' => Auth::id(),
                    'reference_id' => $adjustment->id,
                    'reference_type' => StockAdjustment::class,
                    'notes' => 'Stock adjustment: ' . $adjustment->adjustment_no . ' (' . $adjustment->reason . ')',
                ]);
            }

            DB::commit();
            return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjustment processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing adjustment: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['store', 'user', 'items.product']);
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }
}
