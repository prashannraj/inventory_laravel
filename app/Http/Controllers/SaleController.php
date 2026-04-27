<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Store;
use App\Models\TaxRate;
use App\Models\PaymentMethod;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['customer', 'user', 'store'])->latest()->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function pos()
    {
        $customers = Customer::where('active', true)->get();
        $stores = Store::where('active', true)->get();

        if ($stores->isEmpty()) {
            return redirect()->route('stores.create')
                ->with('error', 'You must create at least one active store before using the POS system.');
        }

        $products = Product::where('active', true)->where('qty', '>', 0)->get();
        $paymentMethods = PaymentMethod::where('active', true)->get();
        
        return view('sales.pos', compact('customers', 'stores', 'products', 'paymentMethods'));
    }

    public function create()
    {
        $customers = Customer::where('active', true)->get();
        $stores = Store::where('active', true)->get();
        $products = Product::with('taxRate')->where('active', true)->get();
        $paymentMethods = PaymentMethod::where('active', true)->get();

        return view('sales.create', compact('customers', 'stores', 'products', 'paymentMethods'));
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Generate invoice number
            $data['invoice_no'] = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $data['user_id'] = Auth::id();
            
            // Calculate totals and tax
            $total_amount = 0;
            $total_net_amount = 0;
            $total_tax_amount = 0;
            $items_with_tax = [];
            
            // Preload products with tax rates for efficiency
            $product_ids = array_column($data['items'], 'product_id');
            $products = Product::with('taxRate')->whereIn('id', $product_ids)->get()->keyBy('id');
            
            foreach ($data['items'] as $item) {
                $item_subtotal = $item['quantity'] * $item['unit_price'];
                $item_discount = $item['discount'] ?? 0;
                $item_net = $item_subtotal - $item_discount;
                $total_amount += $item_subtotal;
                $total_net_amount += $item_net;
                
                // Calculate tax for this item
                $product = $products[$item['product_id']] ?? null;
                $tax_rate = $product->taxRate ?? null;
                $item_tax_amount = 0;
                
                if ($tax_rate && $tax_rate->rate > 0) {
                    // Calculate tax on net amount (after discount)
                    $item_tax_amount = $item_net * ($tax_rate->rate / 100);
                }
                
                $total_tax_amount += $item_tax_amount;
                
                // Store item with tax info for later creation
                $items_with_tax[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item_discount,
                    'tax_amount' => $item_tax_amount,
                    'subtotal' => $item_net,
                ];
            }
            
            // Determine final tax amount: manual override takes precedence
            $manual_tax_amount = $data['tax_amount'] ?? null;
            $manual_tax_rate = $data['tax_rate'] ?? null;
            $final_tax_amount = $total_tax_amount;
            
            if ($manual_tax_amount !== null && $manual_tax_amount != $total_tax_amount) {
                // Use manual tax amount, redistribute across items proportionally based on item_net
                $final_tax_amount = $manual_tax_amount;
                if ($total_net_amount > 0) {
                    foreach ($items_with_tax as &$item) {
                        $item['tax_amount'] = ($item['subtotal'] / $total_net_amount) * $final_tax_amount;
                    }
                }
            } elseif ($manual_tax_rate !== null && $manual_tax_rate > 0) {
                // Calculate tax based on manual tax rate applied to taxable amount (total_net_amount - order discount)
                $taxable_amount = $total_net_amount - ($data['discount'] ?? 0);
                $final_tax_amount = $taxable_amount * ($manual_tax_rate / 100);
                if ($total_net_amount > 0) {
                    foreach ($items_with_tax as &$item) {
                        $item['tax_amount'] = ($item['subtotal'] / $total_net_amount) * $final_tax_amount;
                    }
                }
            }
            
            $data['total_amount'] = $total_amount;
            $data['tax_amount'] = $final_tax_amount;
            $data['net_amount'] = $total_amount - ($data['discount'] ?? 0) + $final_tax_amount;
            
            // Check Customer Credit Limit
            if (!empty($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
                if ($customer && $customer->credit_limit > 0) {
                    $due_amount = $data['net_amount'] - $data['paid_amount'];
                    if ($due_amount > 0) {
                        if (!$customer->canAfford($due_amount)) {
                            throw new \Exception("Customer credit limit exceeded. Current Balance: " . $customer->outstanding_balance . ", Limit: " . $customer->credit_limit);
                        }
                    }
                }
            }

            // Check if payment method is an online gateway
            $paymentMethod = PaymentMethod::where('name', $data['payment_method'])->first();
            $isOnlinePayment = $paymentMethod && $paymentMethod->gateway && in_array($paymentMethod->gateway, ['esewa', 'khalti']);
            
            // Determine payment status (based on net amount with auto-calculated tax)
            if ($isOnlinePayment && $data['paid_amount'] > 0) {
                // For online payments, set status to pending until payment is confirmed
                $data['payment_status'] = 'pending';
                $data['status'] = 'pending';
            } elseif ($data['paid_amount'] >= $data['net_amount']) {
                $data['payment_status'] = 'paid';
            } elseif ($data['paid_amount'] > 0) {
                $data['payment_status'] = 'partial';
            } else {
                $data['payment_status'] = 'unpaid';
            }

            $sale = Sale::create($data);
            
            // Add Loyalty Points
            if (!empty($sale->customer_id)) {
                $customer = $sale->customer;
                if ($customer) {
                    // 1 point per 100 net_amount
                    $points = floor($sale->net_amount / 100);
                    $customer->increment('loyalty_points', $points);
                }
            }

            // Save items and update stock
            foreach ($items_with_tax as $item) {
                $sale->items()->create($item);

                $product = Product::find($item['product_id']);
                
                if ($product->qty < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: " . $product->name);
                }

                $product->decrement('qty', $item['quantity']);
                
                // Log stock movement
                $product->stockMovements()->create([
                    'store_id' => $sale->store_id,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $product->buying_price,
                    'user_id' => Auth::id(),
                    'reference_id' => $sale->id,
                    'reference_type' => Sale::class,
                    'notes' => 'Sale invoice: ' . $sale->invoice_no,
                ]);
            }

            if ($isOnlinePayment && $data['paid_amount'] > 0) {
                // For online payments, create a Payment record (gateway payment)
                $payment = \App\Models\Payment::create([
                    'user_id' => Auth::id(),
                    'transaction_id' => Str::uuid()->toString(),
                    'payment_gateway' => $paymentMethod->gateway,
                    'amount' => $data['paid_amount'],
                    'status' => 'pending',
                    'product_name' => 'Sale Invoice: ' . $sale->invoice_no,
                ]);
                
                // Also create a SalePayment record to link with sale
                $salePayment = $sale->payments()->create([
                    'date' => $data['date'],
                    'amount' => $data['paid_amount'],
                    'payment_method' => $data['payment_method'],
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'gateway' => $paymentMethod->gateway,
                    'transaction_id' => $payment->transaction_id,
                ]);
                
                DB::commit();
                
                // Redirect to payment gateway based on gateway type
                if ($paymentMethod->gateway === 'esewa') {
                    return redirect()->route('esewa.checkout')->with([
                        'sale_id' => $sale->id,
                        'payment_id' => $payment->id,
                        'amount' => $data['paid_amount'],
                        'product_name' => 'Sale Invoice: ' . $sale->invoice_no,
                    ]);
                } elseif ($paymentMethod->gateway === 'khalti') {
                    return redirect()->route('khalti.checkout')->with([
                        'sale_id' => $sale->id,
                        'payment_id' => $payment->id,
                        'amount' => $data['paid_amount'],
                        'product_name' => 'Sale Invoice: ' . $sale->invoice_no,
                    ]);
                }
            } else {
                // Record payment for offline payments
                if ($data['paid_amount'] > 0) {
                    $sale->payments()->create([
                        'date' => $data['date'],
                        'amount' => $data['paid_amount'],
                        'payment_method' => $data['payment_method'],
                        'user_id' => Auth::id(),
                        'status' => 'completed',
                    ]);
                }
                
                DB::commit();
                return redirect()->route('sales.index')->with('success', 'Sale completed successfully.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing sale: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'store', 'user', 'items.product.taxRate', 'payments']);
        return view('sales.show', compact('sale'));
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['customer', 'store', 'user', 'items.product', 'payments']);
        $pdf = Pdf::loadView('sales.invoice', compact('sale'));
        return $pdf->stream('invoice-' . $sale->invoice_no . '.pdf');
    }

    public function edit(Sale $sale)
    {
        $sale->load(['items.product']);
        $customers = Customer::where('active', true)->get();
        $stores = Store::where('active', true)->get();
        $products = Product::where('active', true)->get();

        return view('sales.edit', compact('sale', 'customers', 'stores', 'products'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        try {
            DB::beginTransaction();

            // Prevent editing completed/cancelled sales
            if (in_array($sale->status, ['completed', 'cancelled', 'returned'])) {
                return back()->with('error', 'Cannot edit a sale that is already ' . $sale->status);
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
                $item_subtotal = $item['quantity'] * $item['unit_price'];
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
                    'unit_price' => $item['unit_price'],
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

            // Restore stock from old items
            foreach ($sale->items as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->increment('qty', $oldItem->quantity);
                    // Remove old stock movement
                    $product->stockMovements()
                        ->where('reference_id', $sale->id)
                        ->where('reference_type', Sale::class)
                        ->delete();
                }
            }

            // Delete old items
            $sale->items()->delete();

            // Update sale
            $sale->update($data);

            // Save new items and update stock
            foreach ($items_with_tax as $item) {
                $sale->items()->create($item);

                $product = Product::find($item['product_id']);

                if ($product->qty < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: " . $product->name);
                }

                $product->decrement('qty', $item['quantity']);

                // Log stock movement
                $product->stockMovements()->create([
                    'store_id' => $sale->store_id,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $product->buying_price,
                    'user_id' => Auth::id(),
                    'reference_id' => $sale->id,
                    'reference_type' => Sale::class,
                    'notes' => 'Sale update: ' . $sale->invoice_no,
                ]);
            }

            // Update payments
            $sale->payments()->delete();
            if ($data['paid_amount'] > 0) {
                $sale->payments()->create([
                    'date' => $data['date'],
                    'amount' => $data['paid_amount'],
                    'payment_method' => $data['payment_method'],
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating sale: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Sale $sale)
    {
        try {
            DB::beginTransaction();

            // Only allow deletion of pending sales
            if ($sale->status !== 'pending') {
                return back()->with('error', 'Only pending sales can be deleted.');
            }

            // Restore stock
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('qty', $item->quantity);
                }
            }

            // Delete related records
            $sale->items()->delete();
            $sale->payments()->delete();
            $sale->stockMovements()->delete();
            $sale->delete();

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }
}
