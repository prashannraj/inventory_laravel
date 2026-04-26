<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Store;
use App\Models\TaxRate;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
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
                
                // Calculate tax for this item (based on product tax rate)
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
            
            // Determine final tax amount: manual override takes precedence
            $manual_tax_amount = $data['tax_amount'] ?? null;
            $manual_tax_rate = $data['tax_rate'] ?? null;
            $final_tax_amount = $total_tax_amount;
            
            if ($manual_tax_amount !== null && $manual_tax_amount != $total_tax_amount) {
                // Use manual tax amount, redistribute across items proportionally
                $final_tax_amount = $manual_tax_amount;
                if ($total_amount > 0) {
                    foreach ($items_with_tax as &$item) {
                        $item['tax_amount'] = ($item['subtotal'] / $total_amount) * $final_tax_amount;
                    }
                }
            } elseif ($manual_tax_rate !== null && $manual_tax_rate > 0) {
                // Calculate tax based on manual tax rate
                $taxable_amount = $total_amount - ($data['discount'] ?? 0);
                $final_tax_amount = $taxable_amount * ($manual_tax_rate / 100);
                if ($total_amount > 0) {
                    foreach ($items_with_tax as &$item) {
                        $item['tax_amount'] = ($item['subtotal'] / $total_amount) * $final_tax_amount;
                    }
                }
            }
            
            $data['total_amount'] = $total_amount;
            $data['tax_amount'] = $final_tax_amount;
            $data['net_amount'] = $total_amount - ($data['discount'] ?? 0) + $final_tax_amount;

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

    public function edit(Purchase $purchase)
    {
        $purchase->load(['items.product']);
        $suppliers = Supplier::where('active', true)->get();
        $stores = Store::where('active', true)->get();
        $products = Product::where('active', true)->get();

        return view('purchases.edit', compact('purchase', 'suppliers', 'stores', 'products'));
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            // Prevent editing received purchases
            if ($purchase->status === 'received') {
                return back()->with('error', 'Cannot edit a received purchase.');
            }

            $data = $request->validated();
            $data['user_id'] = Auth::id();

            // Calculate totals and tax
            $total_amount = 0;
            $total_tax_amount = 0;
            $items_with_tax = [];

            // Preload products with tax rates for efficiency
            $product_ids = array_column($data['items'], 'product_id');
            $products = Product::with('taxRate')->whereIn('id', $product_ids)->get()->keyBy('id');

            foreach ($data['items'] as $item) {
                $item_subtotal = $item['quantity'] * $item['unit_cost'];
                $item_discount = $item['discount'] ?? 0;
                $item_net = $item_subtotal - $item_discount;
                $total_amount += $item_subtotal;

                // Calculate tax for this item
                $product = $products[$item['product_id']] ?? null;
                $tax_rate = $product->taxRate ?? null;
                $item_tax_amount = 0;

                if ($tax_rate && $tax_rate->rate > 0) {
                    $item_tax_amount = $item_net * ($tax_rate->rate / 100);
                }

                $total_tax_amount += $item_tax_amount;

                $items_with_tax[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'discount' => $item_discount,
                    'tax_amount' => $item_tax_amount,
                    'subtotal' => $item_net,
                ];
            }

            $data['total_amount'] = $total_amount;
            $data['tax_amount'] = $total_tax_amount;
            $data['net_amount'] = $total_amount - ($data['discount'] ?? 0) + $total_tax_amount;

            // Determine payment status
            if ($data['paid_amount'] >= $data['net_amount']) {
                $data['payment_status'] = 'paid';
            } elseif ($data['paid_amount'] > 0) {
                $data['payment_status'] = 'partial';
            } else {
                $data['payment_status'] = 'unpaid';
            }

            // Restore stock from old items if purchase was received
            if ($purchase->status === 'received') {
                foreach ($purchase->items as $oldItem) {
                    $product = Product::find($oldItem->product_id);
                    if ($product) {
                        $product->decrement('qty', $oldItem->quantity);
                        // Remove old stock movement
                        $product->stockMovements()
                            ->where('reference_id', $purchase->id)
                            ->where('reference_type', Purchase::class)
                            ->delete();
                    }
                }
            }

            // Delete old items
            $purchase->items()->delete();

            // Update purchase
            $purchase->update($data);

            // Save new items and update stock if purchase is received
            if ($purchase->status === 'received') {
                foreach ($items_with_tax as $item) {
                    $purchase->items()->create($item);

                    $product = Product::find($item['product_id']);
                    $product->increment('qty', $item['quantity']);

                    // Log stock movement
                    $product->stockMovements()->create([
                        'store_id' => $purchase->store_id,
                        'type' => 'in',
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'user_id' => Auth::id(),
                        'reference_id' => $purchase->id,
                        'reference_type' => Purchase::class,
                        'notes' => 'Purchase update: ' . $purchase->purchase_no,
                    ]);
                }
            } else {
                // Just create items without stock update
                foreach ($items_with_tax as $item) {
                    $purchase->items()->create($item);
                }
            }

            // Update payments
            $purchase->payments()->delete();
            if ($data['paid_amount'] > 0) {
                $purchase->payments()->create([
                    'date' => $data['date'],
                    'amount' => $data['paid_amount'],
                    'payment_method' => $data['payment_method'],
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase: ' . $e->getMessage())->withInput();
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
