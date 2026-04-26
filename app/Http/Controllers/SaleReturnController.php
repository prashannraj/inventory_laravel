<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleReturnController extends Controller
{
    public function index()
    {
        $returns = SaleReturn::with(['sale', 'user'])->latest()->paginate(10);
        return view('sale-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $sales = Sale::where('status', 'completed')->latest()->get();
        $selectedSale = null;
        
        if ($request->has('sale_id')) {
            $selectedSale = Sale::with(['items.product', 'customer', 'store'])->findOrFail($request->sale_id);
        }
        
        return view('sale-returns.create', compact('sales', 'selectedSale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($request->sale_id);
            
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $return = SaleReturn::create([
                'return_no' => 'SRT-' . strtoupper(uniqid()),
                'sale_id' => $sale->id,
                'date' => $request->date,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                // Stock Reversal (Add back to store)
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'store_id' => $sale->store_id,
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'notes' => 'Sales Return: ' . $return->return_no,
                    'user_id' => Auth::id(),
                    'reference_id' => $return->id,
                    'reference_type' => SaleReturn::class,
                ]);
            }

            // Update sale status if needed
            $sale->update(['status' => 'returned']);

            DB::commit();
            return redirect()->route('sale-returns.index')->with('success', 'Sales return processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing sales return: ' . $e->getMessage());
        }
    }

    public function show(SaleReturn $saleReturn)
    {
        $saleReturn->load(['sale.customer', 'sale.store', 'user', 'items.product']);
        return view('sale-returns.show', compact('saleReturn'));
    }
}
