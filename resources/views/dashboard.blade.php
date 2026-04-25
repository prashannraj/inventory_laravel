<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6">
            <div class="flex-1">
                <h2 class="font-black text-2xl sm:text-3xl text-gray-900 tracking-tighter uppercase">
                    {{ __('Executive Dashboard') }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1 italic">Welcome back, {{ auth()->user()->name }}. Here's what's happening today.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 mt-2 md:mt-0">
                <div class="flex flex-col items-start sm:items-end">
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
                <div class="h-6 w-px bg-gray-200 mx-1 sm:mx-2 block"></div>
                <a href="{{ route('reports.index') }}" class="bg-white border border-gray-100 shadow-sm px-3 sm:px-4 py-2 rounded-xl text-xs font-black text-gray-600 hover:bg-gray-50 transition-all flex items-center gap-2 uppercase tracking-widest whitespace-nowrap">
                    <i class="fas fa-download text-indigo-500"></i> <span class="inline">Export Report</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 sm:space-y-8">
        
        <!-- Key Performance Indicators -->
        <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
            
            <!-- Sales Trend Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 sm:p-8 opacity-[0.03] pointer-events-none">
                    <i class="fas fa-chart-line text-6xl sm:text-9xl"></i>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 sm:mb-8 gap-4">
                    <div>
                        <h3 class="font-black text-lg sm:text-xl text-gray-900 tracking-tight uppercase">Sales Performance</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Revenue analysis for the past week</p>
                    </div>
                    <div class="flex bg-gray-50 p-1 rounded-xl self-start">
                        <button class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg bg-white shadow-sm text-indigo-600">7D</button>
                        <button class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg text-gray-400 hover:text-gray-600">30D</button>
                    </div>
                </div>
                <div id="main-sales-chart" class="min-h-[250px] sm:min-h-[350px]" data-chart-data="{{ json_encode($salesChart) }}"></div>
            </div>

            <!-- Quick Actions & Categories -->
            <div class="space-y-6 sm:space-y-8">
                <!-- Action Cards -->
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:p-8">
                    <h3 class="font-black text-lg text-gray-900 tracking-tight uppercase mb-4 sm:mb-6">Operations</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <a href="{{ route('sales.pos') }}" class="group relative flex items-center p-4 sm:p-5 bg-indigo-600 rounded-xl sm:rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 overflow-hidden">
                            <div class="absolute top-0 right-0 p-2 sm:p-4 opacity-10 group-hover:scale-110 transition-transform">
                                <i class="fas fa-cash-register text-2xl sm:text-4xl text-white"></i>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-lg sm:rounded-xl flex items-center justify-center text-white mr-3 sm:mr-4 shadow-inner">
                                <i class="fas fa-bolt text-sm sm:text-base"></i>
                            </div>
                            <div class="text-white text-left relative z-10">
                                <p class="font-black text-xs sm:text-sm uppercase tracking-widest leading-none">Quick POS</p>
                                <p class="text-[10px] opacity-70 font-bold mt-1">NEW SALE</p>
                            </div>
                        </a>

                        <a href="{{ route('products.create') }}" class="group flex items-center p-4 sm:p-5 bg-white border border-gray-100 rounded-xl sm:rounded-2xl hover:border-blue-500/30 hover:bg-blue-50/30 transition-all">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg sm:rounded-xl flex items-center justify-center text-blue-600 mr-3 sm:mr-4 group-hover:bg-blue-100 transition-colors">
                                <i class="fas fa-plus text-sm sm:text-base"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-widest leading-none">Add Product</p>
                                <p class="text-[10px] text-gray-400 font-bold mt-1 tracking-tighter uppercase">RESTOCK INVENTORY</p>
                            </div>
                        </a>

                        <a href="{{ route('stores.create') }}" class="group flex items-center p-4 sm:p-5 bg-white border border-gray-100 rounded-xl sm:rounded-2xl hover:border-amber-500/30 hover:bg-amber-50/30 transition-all">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-50 rounded-lg sm:rounded-xl flex items-center justify-center text-amber-600 mr-3 sm:mr-4 group-hover:bg-amber-100 transition-colors">
                                <i class="fas fa-store text-sm sm:text-base"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-widest leading-none">Add Store</p>
                                <p class="text-[10px] text-gray-400 font-bold mt-1 tracking-tighter uppercase">MANAGE LOCATIONS</p>
                            </div>
                        </a>

                        <a href="{{ route('units.create') }}" class="group flex items-center p-4 sm:p-5 bg-white border border-gray-100 rounded-xl sm:rounded-2xl hover:border-violet-500/30 hover:bg-violet-50/30 transition-all">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-violet-50 rounded-lg sm:rounded-xl flex items-center justify-center text-violet-600 mr-3 sm:mr-4 group-hover:bg-violet-100 transition-colors">
                                <i class="fas fa-balance-scale text-sm sm:text-base"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-widest leading-none">Add Unit</p>
                                <p class="text-[10px] text-gray-400 font-bold mt-1 tracking-tighter uppercase">MEASUREMENT UNITS</p>
                            </div>
                        </a>

                        <a href="{{ route('tax-rates.create') }}" class="group flex items-center p-4 sm:p-5 bg-white border border-gray-100 rounded-xl sm:rounded-2xl hover:border-rose-500/30 hover:bg-rose-50/30 transition-all">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-rose-50 rounded-lg sm:rounded-xl flex items-center justify-center text-rose-600 mr-3 sm:mr-4 group-hover:bg-rose-100 transition-colors">
                                <i class="fas fa-percentage text-sm sm:text-base"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-black text-xs sm:text-sm text-gray-900 uppercase tracking-widest leading-none">Add Tax Rate</p>
                                <p class="text-[10px] text-gray-400 font-bold mt-1 tracking-tighter uppercase">TAX CONFIGURATION</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Popular Categories -->
                <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-4 sm:mb-6">
                        <h3 class="font-black text-lg text-gray-900 tracking-tight uppercase">Categories</h3>
                        <i class="fas fa-layer-group text-gray-100 text-xl sm:text-2xl"></i>
                    </div>
                    <div class="space-y-4 sm:space-y-6">
                        @foreach($topCategories as $category)
                        @php
                            $percentage = min(100, ($category->item_count / ($totalProducts ?: 1)) * 100);
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-xs font-black text-gray-700 uppercase tracking-tighter truncate pr-2">{{ $category->name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase whitespace-nowrap">{{ $category->item_count }} {{ $totalSales > 0 ? 'Sales' : 'SKUs' }}</span>
                            </div>
                            <div class="w-full bg-gray-50 rounded-full h-1.5 sm:h-2 overflow-hidden border border-gray-100">
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
            <div class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 lg:p-8 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50/30 gap-4">
                    <div>
                        <h3 class="font-black text-lg sm:text-xl text-gray-900 tracking-tight uppercase">Recent Activity</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1 italic">Real-time transaction stream</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="px-3 sm:px-4 py-2 bg-white border border-gray-200 rounded-xl text-[10px] font-black text-indigo-600 hover:shadow-sm transition-all uppercase tracking-widest whitespace-nowrap self-start">
                        View Audit Log
                    </a>
                </div>
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <div class="min-w-[600px] sm:min-w-0">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    <th class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5">Transaction</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 hidden sm:table-cell">Customer</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 text-right">Value</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 text-center hidden xs:table-cell">Status</th>
                                    <th class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($recentSales as $sale)
                                <tr class="hover:bg-indigo-50/20 transition-colors group">
                                    <td class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-white group-hover:text-indigo-600 transition-all border border-transparent group-hover:border-indigo-100">
                                                <i class="fas fa-receipt text-xs"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs sm:text-sm font-black text-gray-900 group-hover:text-indigo-600 transition-colors truncate max-w-[120px] sm:max-w-none">{{ $sale->invoice_no }}</div>
                                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $sale->date->diffForHumans() }}</div>
                                                <div class="text-xs text-gray-500 font-medium sm:hidden mt-1">{{ $sale->customer?->name ?? 'Walk-in Guest' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 hidden sm:table-cell">
                                        <div class="text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-tighter truncate max-w-[100px] lg:max-w-none">{{ $sale->customer?->name ?? 'Walk-in Guest' }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 text-right">
                                        <div class="text-xs sm:text-sm font-black text-gray-900 tracking-tight">Rs. {{ number_format($sale->net_amount, 2) }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 text-center hidden xs:table-cell">
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
                                    <td class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 text-right">
                                        <a href="{{ route('sales.show', $sale) }}" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-gray-300 hover:bg-white hover:text-indigo-600 hover:shadow-sm transition-all border border-transparent hover:border-gray-100">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 sm:px-8 py-12 sm:py-20 text-center">
                                        <i class="fas fa-inbox text-3xl sm:text-4xl text-gray-100 mb-4 block"></i>
                                        <span class="text-xs font-black text-gray-300 uppercase tracking-[0.2em]">Zero transactions recorded today</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Restock Alert Panel -->
            <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-4 sm:p-6 lg:p-8">
                <div class="flex items-center justify-between mb-6 sm:mb-8">
                    <h3 class="font-black text-lg text-gray-900 tracking-tight uppercase">Restock Queue</h3>
                    <span class="px-2 sm:px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest rounded-lg whitespace-nowrap">Attention Required</span>
                </div>
                <div class="space-y-4 sm:space-y-6">
                    @forelse($lowStockItems as $product)
                    <div class="flex items-center gap-3 sm:gap-4 group">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 bg-gray-50 rounded-xl sm:rounded-2xl flex items-center justify-center text-gray-400 group-hover:scale-105 transition-transform overflow-hidden shadow-inner border border-gray-50 flex-shrink-0">
                            @if($product->images->where('is_primary', true)->first())
                                <img src="{{ asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-image opacity-20 text-sm sm:text-base"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-tighter leading-none truncate">{{ $product->name }}</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1 truncate">{{ $product->category?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-base sm:text-lg font-black leading-none {{ $product->qty <= 5 ? 'text-rose-600' : 'text-amber-500' }} tracking-tighter">{{ $product->qty }}</p>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Left</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 sm:py-12">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-3 sm:mb-4">
                            <i class="fas fa-check text-xl sm:text-2xl"></i>
                        </div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest italic">All levels optimized</p>
                    </div>
                    @endforelse
                </div>
                @if(count($lowStockItems) > 0)
                <div class="mt-6 sm:mt-10">
                    <a href="{{ route('products.index') }}?filter=low-stock" class="block w-full py-3 sm:py-4 bg-gray-900 text-white text-[10px] font-black text-center uppercase tracking-[0.3em] rounded-xl sm:rounded-2xl hover:bg-gray-800 hover:shadow-xl transition-all active:scale-[0.98]">
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
