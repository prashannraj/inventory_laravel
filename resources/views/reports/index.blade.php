<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Total Revenue (Year)</div>
                    <div class="text-2xl font-bold text-gray-900">Rs. {{ number_format($salesData->sum('total'), 2) }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Total Purchases (Year)</div>
                    <div class="text-2xl font-bold text-gray-900">Rs. {{ number_format($purchaseData->sum('total'), 2) }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Net Profit (Est.)</div>
                    <div class="text-2xl font-bold text-gray-900">Rs. {{ number_format($salesData->sum('total') - $purchaseData->sum('total'), 2) }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Top Category</div>
                    <div class="text-2xl font-bold text-gray-900">Electronics</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Sales vs Purchases Chart -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold mb-4">Sales vs Purchases Trend</h3>
                    <div id="revenue-chart" 
                         data-sales-data="{{ json_encode($salesData) }}" 
                         data-purchase-data="{{ json_encode($purchaseData) }}"></div>
                </div>

                <!-- Top Selling Products -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold mb-4">Top Selling Products</h3>
                    <div class="space-y-4">
                        @foreach($topProducts as $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold mr-3">
                                    {{ substr($product->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">{{ $product->sales_count }} sales</div>
                                <div class="text-xs text-green-600">+12% from last month</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Detailed Reports Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('reports.sales') }}" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="text-indigo-600 text-3xl mb-3"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h4 class="font-bold text-lg mb-1">Sales Report</h4>
                    <p class="text-sm text-gray-500">Detailed analysis of sales, revenue and customer behavior.</p>
                </a>
                <a href="{{ route('reports.purchases') }}" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="text-green-600 text-3xl mb-3"><i class="fas fa-shopping-bag"></i></div>
                    <h4 class="font-bold text-lg mb-1">Purchase Report</h4>
                    <p class="text-sm text-gray-500">Monitor supplier performance and procurement costs.</p>
                </a>
                <a href="{{ route('reports.inventory') }}" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="text-yellow-600 text-3xl mb-3"><i class="fas fa-boxes"></i></div>
                    <h4 class="font-bold text-lg mb-1">Inventory Report</h4>
                    <p class="text-sm text-gray-500">Real-time stock levels, valuations and low stock alerts.</p>
                </a>
                <a href="{{ route('reports.profit-loss') }}" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="text-red-600 text-3xl mb-3"><i class="fas fa-chart-line"></i></div>
                    <h4 class="font-bold text-lg mb-1">Profit & Loss</h4>
                    <p class="text-sm text-gray-500">Financial statement that summarizes the revenues, costs, and expenses.</p>
                </a>
                <a href="{{ route('reports.cash-flow') }}" class="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="text-blue-600 text-3xl mb-3"><i class="fas fa-money-bill-wave"></i></div>
                    <h4 class="font-bold text-lg mb-1">Cash Flow</h4>
                    <p class="text-sm text-gray-500">Statement showing incoming and outgoing cash and cash equivalents.</p>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartElement = document.getElementById('revenue-chart');
            const salesData = JSON.parse(chartElement.dataset.salesData || '[]');
            const purchaseData = JSON.parse(chartElement.dataset.purchaseData || '[]');
            
            const options = {
                series: [{
                    name: 'Sales',
                    data: salesData.map(d => d.total)
                }, {
                    name: 'Purchases',
                    data: purchaseData.map(d => d.total)
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: { show: false }
                },
                colors: ['#6366f1', '#10b981'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth' },
                xaxis: {
                    categories: salesData.map(d => d.month)
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "Rs. " + val.toLocaleString()
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
            chart.render();
        });
    </script>
    @endpush
</x-app-layout>
