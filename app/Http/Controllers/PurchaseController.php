<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Store;
use App\Models\TaxRate;
use App\Http\Requests\StorePurchaseRequest;
use Illuminate\Http\Request;
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
            
            // Calculate totals and tax
            $total_amount = 0;
            $total_tax_amount = 0;
            $items_with_tax = [];
            
            // Preload products with tax rates for efficiency
            $product_ids = array_column($data['items'], 'product_id');
            $products = Product::with('taxRate')->whereIn('id', $product_ids)->get()->keyBy('id');
            
            foreach ($data['items'] as $item) {
                $item_total = $item['quantity'] * $item['cost_price'];
                $total_amount += $item_total;
                
                // Calculate tax for this item
                $product = $products[$item['product_id']] ?? null;
                $tax_rate = $product->taxRate ?? null;
                $item_tax_amount = 0;
                
                if ($tax_rate && $tax_rate->rate > 0) {
                    $item_tax_amount = $item_total * ($tax_rate->rate / 100);
                }
                
                $total_tax_amount += $item_tax_amount;
                
                // Store item with tax info for later creation
                $items_with_tax[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'tax_amount' => $item_tax_amount,
                    'subtotal' => $item_total,
                ];
            }
            
            $data['total_amount'] = $total_amount;
            $data['tax_amount'] = $total_tax_amount; // Auto-calculated tax
            $data['net_amount'] = $total_amount - ($data['discount'] ?? 0) + $total_tax_amount;

            // Handle document upload
            if ($request->hasFile('document')) {
                $data['document'] = $request->file('document')->store('purchases', 'public');
            }

            $purchase = Purchase::create($data);

            // Save items and update stock if received
            foreach ($items_with_tax as $item) {
                $purchase->items()->create($item);

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
        $purchase->load(['supplier', 'store', 'user', 'items.product.taxRate']);
        return view('purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, Purchase $purchase)
    {
        $request->validate([
            'status' => 'required|in:pending,ordered,received,cancelled',
        ]);

        $oldStatus = $purchase->status;
        $newStatus = $request->status;

        DB::beginTransaction();
        try {
            $purchase->status = $newStatus;
            $purchase->save();

            // If changing to 'received', update stock
            if ($oldStatus !== 'received' && $newStatus === 'received') {
                foreach ($purchase->items as $item) {
                    $product = Product::find($item->product_id);
                    $product->increment('qty', $item->quantity);
                    
                    // Log stock movement
                    $product->stockMovements()->create([
                        'store_id' => $purchase->store_id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->cost_price,
                        'user_id' => Auth::id(),
                        'reference_id' => $purchase->id,
                        'reference_type' => Purchase::class,
                        'notes' => 'Purchase received: ' . $purchase->purchase_no,
                    ]);
                }
            }

            // If changing from 'received' to another status, reverse stock
            if ($oldStatus === 'received' && $newStatus !== 'received') {
                foreach ($purchase->items as $item) {
                    $product = Product::find($item->product_id);
                    $product->decrement('qty', $item->quantity);
                    
                    // Log stock movement reversal
                    $product->stockMovements()->create([
                        'store_id' => $purchase->store_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->cost_price,
                        'user_id' => Auth::id(),
                        'reference_id' => $purchase->id,
                        'reference_type' => Purchase::class,
                        'notes' => 'Purchase status changed from received to ' . $newStatus . ': ' . $purchase->purchase_no,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Purchase status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase status: ' . $e->getMessage());
        }
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
