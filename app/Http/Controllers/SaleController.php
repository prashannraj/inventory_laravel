<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Store;
use App\Http\Requests\StoreSaleRequest;
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
        
        return view('sales.pos', compact('customers', 'stores', 'products'));
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            // Generate invoice number
            $data['invoice_no'] = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $data['user_id'] = Auth::id();
            
            // Calculate totals
            $total_amount = 0;
            foreach ($data['items'] as $item) {
                $total_amount += $item['quantity'] * $item['unit_price'];
            }
            
            $data['total_amount'] = $total_amount;
            $data['net_amount'] = $total_amount - ($data['discount'] ?? 0) + ($data['tax_amount'] ?? 0);
            
            // Determine payment status
            if ($data['paid_amount'] >= $data['net_amount']) {
                $data['payment_status'] = 'paid';
            } elseif ($data['paid_amount'] > 0) {
                $data['payment_status'] = 'partial';
            } else {
                $data['payment_status'] = 'unpaid';
            }

            $sale = Sale::create($data);

            // Save items and update stock
            foreach ($data['items'] as $item) {
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax_amount' => 0, // Simplified
                    'subtotal' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0),
                ]);

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

            // Record payment if any
            if ($data['paid_amount'] > 0) {
                $sale->payments()->create([
                    'date' => $data['date'],
                    'amount' => $data['paid_amount'],
                    'payment_method' => $data['payment_method'],
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing sale: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'store', 'user', 'items.product', 'payments']);
        return view('sales.show', compact('sale'));
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['customer', 'store', 'user', 'items.product', 'payments']);
        $pdf = Pdf::loadView('sales.invoice', compact('sale'));
        return $pdf->stream('invoice-' . $sale->invoice_no . '.pdf');
    }
}
