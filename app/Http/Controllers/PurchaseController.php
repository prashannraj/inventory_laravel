<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Store;
use App\Http\Requests\StorePurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user', 'store'])->latest()->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('active', true)->get();
        $stores = Store::where('active', true)->get();

        if ($stores->isEmpty()) {
            return redirect()->route('stores.create')
                ->with('error', 'You must create at least one active store before recording a purchase.');
        }

        $products = Product::where('active', true)->get();
        
        return view('purchases.create', compact('suppliers', 'stores', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Generate purchase number
            $data['purchase_no'] = 'PUR-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $data['user_id'] = Auth::id();
            
            // Calculate totals
            $total_amount = 0;
            foreach ($data['items'] as $item) {
                $total_amount += $item['quantity'] * $item['cost_price'];
            }
            
            $data['total_amount'] = $total_amount;
            $data['net_amount'] = $total_amount - ($data['discount'] ?? 0) + ($data['tax_amount'] ?? 0);

            // Handle document upload
            if ($request->hasFile('document')) {
                $data['document'] = $request->file('document')->store('purchases', 'public');
            }

            $purchase = Purchase::create($data);

            // Save items and update stock if received
            foreach ($data['items'] as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'tax_amount' => 0, // Simplified for now
                    'subtotal' => $item['quantity'] * $item['cost_price'],
                ]);

                if ($purchase->status === 'received') {
                    $product = Product::find($item['product_id']);
                    $product->increment('qty', $item['quantity']);
                    
                    // Log stock movement
                    $product->stockMovements()->create([
                        'store_id' => $purchase->store_id,
                        'type' => 'in',
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['cost_price'],
                        'user_id' => Auth::id(),
                        'reference_id' => $purchase->id,
                        'reference_type' => Purchase::class,
                        'notes' => 'Purchase received: ' . $purchase->purchase_no,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'store', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'received') {
            return back()->with('error', 'Cannot delete a received purchase.');
        }

        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
    }
}
