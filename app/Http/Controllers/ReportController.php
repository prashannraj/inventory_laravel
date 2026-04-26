<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Expense;
use App\Models\SaleReturn;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        // Monthly Sales Chart Data
        $salesData = Sale::select(
            DB::raw('SUM(net_amount) as total'),
            DB::raw("DATE_FORMAT(date, '%M') as month"),
            DB::raw('MIN(date) as min_date')
        )
        ->whereYear('date', date('Y'))
        ->groupBy('month')
        ->orderBy('min_date')
        ->get();

        // Monthly Purchases Chart Data
        $purchaseData = Purchase::select(
            DB::raw('SUM(net_amount) as total'),
            DB::raw("DATE_FORMAT(date, '%M') as month"),
            DB::raw('MIN(date) as min_date')
        )
        ->whereYear('date', date('Y'))
        ->groupBy('month')
        ->orderBy('min_date')
        ->get();

        $topProducts = Product::withCount(['stockMovements as sales_count' => function($query) {
            $query->where('type', 'out');
        }])
        ->orderBy('sales_count', 'desc')
        ->take(5)
        ->get();

        return view('reports.index', compact('salesData', 'purchaseData', 'topProducts'));
    }

    public function sales(Request $request)
    {
        $start_date = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $end_date = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('date', [$start_date, $end_date])
            ->latest()
            ->get();

        return view('reports.sales', compact('sales', 'start_date', 'end_date'));
    }

    public function inventory()
    {
        $products = Product::with(['category', 'brand', 'unit'])->get();
        return view('reports.inventory', compact('products'));
    }

    public function purchases(Request $request)
    {
        $start_date = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $end_date = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $purchases = Purchase::with(['supplier', 'user'])
            ->whereBetween('date', [$start_date, $end_date])
            ->latest()
            ->get();

        return view('reports.purchases', compact('purchases', 'start_date', 'end_date'));
    }

    public function stock()
    {
        $movements = StockMovement::with(['product', 'user'])
            ->latest()
            ->paginate(20);
            
        return view('reports.stock', compact('movements'));
    }

    public function profitAndLoss(Request $request)
    {
        $start_date = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $end_date = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Income
        $sales = Sale::whereBetween('date', [$start_date, $end_date])->sum('net_amount');
        $saleReturns = SaleReturn::whereBetween('date', [$start_date, $end_date])->sum('total_amount');
        $netSales = $sales - $saleReturns;

        // Cost of Goods Sold (Simplified: based on buying price of sold items)
        $cogs = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.date', [$start_date, $end_date])
            ->sum(DB::raw('sale_items.quantity * sale_items.unit_price')); // This is revenue, need buying price
        
        // Actually COGS should be sum(quantity * buying_price)
        $cogs = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->whereBetween('sales.date', [$start_date, $end_date])
            ->sum(DB::raw('sale_items.quantity * products.buying_price'));

        $grossProfit = $netSales - $cogs;

        // Expenses
        $expenses = Expense::whereBetween('date', [$start_date, $end_date])->sum('amount');
        
        $netProfit = $grossProfit - $expenses;

        return view('reports.profit-loss', compact(
            'sales', 'saleReturns', 'netSales', 'cogs', 'grossProfit', 'expenses', 'netProfit', 'start_date', 'end_date'
        ));
    }

    public function cashFlow(Request $request)
    {
        $start_date = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $end_date = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Cash Inflow: Sale Payments
        $salePayments = SalePayment::whereBetween('date', [$start_date, $end_date])->sum('amount');
        
        // Cash Outflow: Expenses
        $expenses = Expense::whereBetween('date', [$start_date, $end_date])->sum('amount');
        
        // Cash Outflow: Purchases (assuming fully paid for now)
        $purchases = Purchase::whereBetween('date', [$start_date, $end_date])->sum('net_amount');
        
        // Cash Outflow: Sale Returns (Refunds)
        $saleReturns = SaleReturn::whereBetween('date', [$start_date, $end_date])->sum('total_amount');

        $netCashFlow = $salePayments - ($expenses + $purchases + $saleReturns);

        return view('reports.cash-flow', compact(
            'salePayments', 'expenses', 'purchases', 'saleReturns', 'netCashFlow', 'start_date', 'end_date'
        ));
    }

    public function export($type)
    {
        $request = request();
        $start_date = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : \Carbon\Carbon::now()->startOfMonth();
        $end_date = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : \Carbon\Carbon::now()->endOfMonth();
        
        switch ($type) {
            case 'sales':
                $data = Sale::with(['customer', 'user'])
                    ->whereBetween('date', [$start_date, $end_date])
                    ->latest()
                    ->get();
                
                $exportData = $data->map(function ($sale) {
                    return [
                        'Invoice No' => $sale->invoice_no,
                        'Date' => $sale->date->format('Y-m-d'),
                        'Customer' => $sale->customer ? $sale->customer->name : 'Walk-in',
                        'Items' => $sale->items->count(),
                        'Subtotal' => $sale->subtotal,
                        'Discount' => $sale->discount,
                        'Tax' => $sale->tax_amount,
                        'Total' => $sale->net_amount,
                        'Payment Status' => $sale->payment_status,
                        'Created By' => $sale->user ? $sale->user->name : 'N/A',
                    ];
                });
                
                $filename = 'sales_report_' . date('Y_m_d') . '.xlsx';
                $exportType = \Maatwebsite\Excel\Excel::XLSX;
                break;
                
            case 'purchases':
                $data = Purchase::with(['supplier', 'user'])
                    ->whereBetween('date', [$start_date, $end_date])
                    ->latest()
                    ->get();
                
                $exportData = $data->map(function ($purchase) {
                    return [
                        'Purchase No' => $purchase->purchase_no,
                        'Date' => $purchase->date->format('Y-m-d'),
                        'Supplier' => $purchase->supplier ? $purchase->supplier->name : 'N/A',
                        'Items' => $purchase->items->count(),
                        'Subtotal' => $purchase->subtotal,
                        'Discount' => $purchase->discount,
                        'Tax' => $purchase->tax_amount,
                        'Total' => $purchase->net_amount,
                        'Status' => $purchase->status,
                        'Created By' => $purchase->user ? $purchase->user->name : 'N/A',
                    ];
                });
                
                $filename = 'purchases_report_' . date('Y_m_d') . '.xlsx';
                $exportType = \Maatwebsite\Excel\Excel::XLSX;
                break;
                
            case 'inventory':
                $data = Product::with(['category', 'brand', 'unit'])->get();
                
                $exportData = $data->map(function ($product) {
                    return [
                        'SKU' => $product->sku,
                        'Product Name' => $product->name,
                        'Category' => $product->category ? $product->category->name : 'N/A',
                        'Brand' => $product->brand ? $product->brand->name : 'N/A',
                        'Unit' => $product->unit ? $product->unit->name : 'N/A',
                        'Cost Price' => $product->cost_price,
                        'Selling Price' => $product->selling_price,
                        'Stock Quantity' => $product->qty,
                        'Reorder Level' => $product->reorder_level,
                        'Stock Value (Cost)' => $product->cost_price * $product->qty,
                        'Stock Value (Selling)' => $product->selling_price * $product->qty,
                        'Status' => $product->qty > 0 ? ($product->qty <= $product->reorder_level ? 'Low Stock' : 'In Stock') : 'Out of Stock',
                    ];
                });
                
                $filename = 'inventory_report_' . date('Y_m_d') . '.xlsx';
                $exportType = \Maatwebsite\Excel\Excel::XLSX;
                break;
                
            case 'stock':
                $data = StockMovement::with(['product', 'user'])
                    ->latest()
                    ->get();
                
                $exportData = $data->map(function ($movement) {
                    return [
                        'Date' => $movement->created_at->format('Y-m-d H:i'),
                        'Product' => $movement->product ? $movement->product->name : 'N/A',
                        'SKU' => $movement->product ? $movement->product->sku : 'N/A',
                        'Type' => $movement->type,
                        'Quantity' => $movement->qty,
                        'Reference Type' => $movement->reference_type,
                        'Reference ID' => $movement->reference_id,
                        'Notes' => $movement->notes,
                        'User' => $movement->user ? $movement->user->name : 'System',
                    ];
                });
                
                $filename = 'stock_movements_report_' . date('Y_m_d') . '.xlsx';
                $exportType = \Maatwebsite\Excel\Excel::XLSX;
                break;
                
            default:
                return back()->with('error', 'Invalid export type specified.');
        }
        
        // For CSV export
        if ($request->format === 'csv') {
            $filename = str_replace('.xlsx', '.csv', $filename);
            $exportType = \Maatwebsite\Excel\Excel::CSV;
        }
        
        // For PDF export (would need DomPDF installed)
        if ($request->format === 'pdf') {
            return back()->with('info', 'PDF export will be available soon. Please use Excel or CSV format for now.');
        }
        
        // Create export
        $export = new class($exportData) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $data;
            
            public function __construct($data)
            {
                $this->data = $data;
            }
            
            public function collection()
            {
                return $this->data;
            }
            
            public function headings(): array
            {
                if ($this->data->isEmpty()) {
                    return [];
                }
                return array_keys($this->data->first());
            }
        };
        
        return \Excel::download($export, $filename, $exportType);
    }
}
