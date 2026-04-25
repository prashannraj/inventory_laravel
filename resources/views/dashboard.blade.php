<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-black text-3xl text-gray-900 tracking-tighter uppercase">
                    {{ __('Executive Dashboard') }}
                </h2>
                <p class="text-sm text-gray-500 font-medium mt-1 italic">Welcome back, {{ auth()->user()->name }}. Here's what's happening today.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Database Status</span>
                    @php
                        $dbStatus = true;
                        try { \DB::connection()->getPdo(); } catch (\Exception $e) { $dbStatus = false; }
                    @endphp
                    <span class="text-xs font-bold {{ $dbStatus ? 'text-emerald-500' : 'text-rose-500' }} flex items-center mt-1">
                        <span class="w-2 h-2 {{ $dbStatus ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full mr-2 {{ $dbStatus ? 'animate-pulse' : '' }}"></span> 
                        {{ $dbStatus ? 'Operational' : 'Offline' }}
                    </span>
                </div>
                <div class="h-10 w-px bg-gray-200 mx-2 hidden sm:block"></div>
                <a href="{{ route('reports.index') }}" class="bg-white border border-gray-100 shadow-sm px-4 py-2 rounded-xl text-xs font-black text-gray-600 hover:bg-gray-50 transition-all flex items-center gap-2 uppercase tracking-widest">
                    <i class="fas fa-download text-indigo-500"></i> Export Report
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        
        <!-- Key Performance Indicators -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-dashboard-widget 
                title="Total Products" 
                :value="number_format($totalProducts)" 
                icon="fas fa-box-open" 
                color="blue" 
                :trend="round($productTrend, 1)"
                label="SKUs"
                :link="route('products.index')"
                linkText="Manage Inventory"
            />

            <x-dashboard-widget 
                title="Total Sales" 
                :value="number_format($totalSales)" 
                icon="fas fa-shopping-cart" 
                color="emerald" 
                :trend="round($salesTrend, 1)"
                label="Invoices"
                :link="route('sales.index')"
                linkText="View All Sales"
            />

            <x-dashboard-widget 
                title="Net Revenue" 
                :value="'Rs. ' . number_format($totalRevenue, 2)" 
                icon="fas fa-wallet" 
                color="violet" 
                label="Live"
                :link="route('reports.index')"
                linkText="Revenue Reports"
            />

            <x-dashboard-widget 
                title="Stock Alerts" 
                :value="$lowStockProducts" 
                icon="fas fa-triangle-exclamation" 
                :color="$lowStockProducts > 0 ? 'rose' : 'amber'" 
                :label="$lowStockProducts > 0 ? 'Urgent' : 'Healthy'"
                :link="route('products.index') . '?filter=low-stock'"
                linkText="Restock Now"
                :class="$lowStockProducts > 0 ? 'ring-2 ring-rose-500/10' : ''"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sales Trend Chart -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
                    <i class="fas fa-chart-line text-9xl"></i>
                </div>
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="font-black text-xl text-gray-900 tracking-tight uppercase">Sales Performance</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Revenue analysis for the past week</p>
                    </div>
                    <div class="flex bg-gray-50 p-1 rounded-xl">
                        <button class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg bg-white shadow-sm text-indigo-600">7D</button>
                        <button class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg text-gray-400 hover:text-gray-600">30D</button>
                    </div>
                </div>
                <div id="main-sales-chart" class="min-h-[350px]" data-chart-data="{{ json_encode($salesChart) }}"></div>
            </div>

            <!-- Quick Actions & Categories -->
            <div class="space-y-8">
                <!-- Action Cards -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h3 class="font-black text-lg text-gray-900 tracking-tight uppercase mb-6">Operations</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('sales.pos') }}" class="group relative flex items-center p-5 bg-indigo-600 rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 overflow-hidden">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                                <i class="fas fa-cash-register text-4xl text-white"></i>
                            </div>
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-white mr-4 shadow-inner">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="text-white text-left relative z-10">
                                <p class="font-black text-sm uppercase tracking-widest leading-none">Quick POS</p>
                                <p class="text-[10px] opacity-70 font-bold mt-1">NEW SALE</p>
                            </div>
                        </a>

                        <a href="{{ route('products.create') }}" class="group flex items-center p-5 bg-white border border-gray-100 rounded-2xl hover:border-blue-500/30 hover:bg-blue-50/30 transition-all">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mr-4 group-hover:bg-blue-100 transition-colors">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-sm text-gray-900 uppercase tracking-widest leading-none">Add Product</p>
                                <p class="text-[10px] text-gray-400 font-bold mt-1 tracking-tighter uppercase">RESTOCK INVENTORY</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Popular Categories -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-black text-lg text-gray-900 tracking-tight uppercase">Categories</h3>
                        <i class="fas fa-layer-group text-gray-100 text-2xl"></i>
                    </div>
                    <div class="space-y-6">
                        @foreach($topCategories as $category)
                        @php
                            $percentage = min(100, ($category->item_count / ($totalProducts ?: 1)) * 100);
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-xs font-black text-gray-700 uppercase tracking-tighter">{{ $category->name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $category->item_count }} {{ $totalSales > 0 ? 'Sales' : 'SKUs' }}</span>
                            </div>
                            <div class="w-full bg-gray-50 rounded-full h-2 overflow-hidden border border-gray-100">
                                <div class="bg-gradient-to-r from-indigo-500 to-violet-500 h-full rounded-full transition-all duration-1000 shadow-sm shadow-indigo-100" 
                                     x-data="{ width: '{{ $percentage }}%' }"
                                     :style="{ width: width }"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                    <div>
                        <h3 class="font-black text-xl text-gray-900 tracking-tight uppercase">Recent Activity</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1 italic">Real-time transaction stream</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-[10px] font-black text-indigo-600 hover:shadow-sm transition-all uppercase tracking-widest">
                        View Audit Log
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                <th class="px-8 py-5">Transaction Details</th>
                                <th class="px-8 py-5">Customer Entity</th>
                                <th class="px-8 py-5 text-right">Value</th>
                                <th class="px-8 py-5 text-center">Settlement</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentSales as $sale)
                            <tr class="hover:bg-indigo-50/20 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:text-indigo-600 transition-all border border-transparent group-hover:border-indigo-100">
                                            <i class="fas fa-receipt text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $sale->invoice_no }}</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $sale->date->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold text-gray-700 uppercase tracking-tighter">{{ $sale->customer?->name ?? 'Walk-in Guest' }}</div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="text-sm font-black text-gray-900 tracking-tight">Rs. {{ number_format($sale->net_amount, 2) }}</div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @php
                                        $statusConfig = match($sale->payment_status) {
                                            'paid' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-500'],
                                            'partial' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-500'],
                                            default => ['bg' => 'bg-rose-500', 'text' => 'text-rose-500'],
                                        };
                                    @endphp
                                    <div class="flex items-center justify-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['bg'] }}"></span>
                                        <span class="text-[10px] font-black uppercase tracking-widest {{ $statusConfig['text'] }}">
                                            {{ $sale->payment_status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <a href="{{ route('sales.show', $sale) }}" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-300 hover:bg-white hover:text-indigo-600 hover:shadow-sm transition-all border border-transparent hover:border-gray-100">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <i class="fas fa-inbox text-4xl text-gray-100 mb-4 block"></i>
                                    <span class="text-xs font-black text-gray-300 uppercase tracking-[0.2em]">Zero transactions recorded today</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Restock Alert Panel -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-black text-lg text-gray-900 tracking-tight uppercase">Restock Queue</h3>
                    <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Attention Required</span>
                </div>
                <div class="space-y-6">
                    @forelse($lowStockItems as $product)
                    <div class="flex items-center gap-4 group">
                        <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:scale-105 transition-transform overflow-hidden shadow-inner border border-gray-50">
                            @if($product->images->where('is_primary', true)->first())
                                <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-image opacity-20"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-tighter leading-none">{{ $product->name }}</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ $product->category?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black leading-none {{ $product->qty <= 5 ? 'text-rose-600' : 'text-amber-500' }} tracking-tighter">{{ $product->qty }}</p>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Left</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-4">
                            <i class="fas fa-check text-2xl"></i>
                        </div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic">All levels optimized</p>
                    </div>
                    @endforelse
                </div>
                @if(count($lowStockItems) > 0)
                <div class="mt-10">
                    <a href="{{ route('products.index') }}?filter=low-stock" class="block w-full py-4 bg-gray-900 text-white text-[10px] font-black text-center uppercase tracking-[0.3em] rounded-2xl hover:bg-gray-800 hover:shadow-xl transition-all active:scale-[0.98]">
                        Full Inventory Audit
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Main Sales Trend Chart
            const chartElement = document.querySelector("#main-sales-chart");
            const chartData = JSON.parse(chartElement.dataset.chartData || '[]');
            
            const options = {
                series: [{
                    name: 'Revenue Flow',
                    data: chartData.map(d => d.total)
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    fontFamily: 'Figtree, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    sparkline: { enabled: false }
                },
                colors: ['#6366f1'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                    lineCap: 'round'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.5,
                        opacityTo: 0.02,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: chartData.map(d => {
                        const date = new Date(d.day);
                        return date.toLocaleDateString('en-US', { weekday: 'short' });
                    }),
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 800
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 800
                        },
                        formatter: (val) => 'Rs. ' + val.toLocaleString()
                    }
                },
                grid: {
                    borderColor: '#f8fafc',
                    strokeDashArray: 8,
                    padding: { left: 20, right: 20 }
                },
                markers: {
                    size: 0,
                    hover: { size: 6 }
                },
                tooltip: {
                    theme: 'dark',
                    y: { formatter: (val) => 'Rs. ' + val.toLocaleString() }
                }
            };

            const chart = new ApexCharts(document.querySelector("#main-sales-chart"), options);
            chart.render();
        });
    </script>
    @endpush
</x-app-layout>
