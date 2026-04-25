<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalProducts = Product::count();
        $totalSales = Sale::count();
        $lowStockProducts = Product::where('qty', '<=', 10)->count();
        $lowStockItems = Product::where('qty', '<=', 10)->latest()->take(5)->get();
        $totalRevenue = Sale::where('payment_status', 'paid')->sum('net_amount');
        $recentSales = Sale::with('customer')->latest()->take(8)->get();

        // Calculate Trends (Current vs Previous 30 days)
        $currentPeriodSales = Sale::where('date', '>=', now()->subDays(30))->count();
        $previousPeriodSales = Sale::whereBetween('date', [now()->subDays(60), now()->subDays(30)])->count();
        $salesTrend = $previousPeriodSales > 0 ? (($currentPeriodSales - $previousPeriodSales) / $previousPeriodSales) * 100 : 0;

        $currentPeriodProducts = Product::where('created_at', '>=', now()->subDays(30))->count();
        $previousPeriodProducts = Product::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        $productTrend = $previousPeriodProducts > 0 ? (($currentPeriodProducts - $previousPeriodProducts) / $previousPeriodProducts) * 100 : 0;

        // Chart Data: Last 7 days of sales
        $salesChart = Sale::select(
            DB::raw('DATE(date) as day'),
            DB::raw('SUM(net_amount) as total')
        )
        ->where('date', '>=', now()->subDays(7))
        ->groupBy('day')
        ->orderBy('day')
        ->get();

        // Top Selling Categories based on sale items
        $topCategories = Category::select('categories.id', 'categories.name')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw('COUNT(sale_items.id) as item_count')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('item_count')
            ->take(4)
            ->get();

        // If no sales yet, fallback to product count
        if ($topCategories->isEmpty()) {
            $topCategories = Category::withCount('products as item_count')
                ->orderBy('item_count', 'desc')
                ->take(4)
                ->get();
        }

        return view('dashboard', compact(
            'totalProducts',
            'totalSales',
            'lowStockProducts',
            'lowStockItems',
            'totalRevenue',
            'recentSales',
            'salesChart',
            'topCategories',
            'salesTrend',
            'productTrend'
        ));
    }
}
