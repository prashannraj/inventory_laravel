<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function export($type)
    {
        // Placeholder for export logic
        return back()->with('error', 'Export functionality is being implemented.');
    }
}
