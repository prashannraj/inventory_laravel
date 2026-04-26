<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profit & Loss Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('reports.profit-loss') }}" class="mb-8 flex gap-4 items-end bg-gray-50 p-4 rounded-lg">
                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" value="{{ $start_date->format('Y-m-d') }}" />
                        </div>
                        <div>
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" value="{{ $end_date->format('Y-m-d') }}" />
                        </div>
                        <x-primary-button>
                            Filter
                        </x-primary-button>
                    </form>

                    <div class="space-y-6">
                        <div class="border-b pb-4">
                            <h3 class="text-xl font-bold mb-4 uppercase text-gray-500 tracking-wider">Income</h3>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg">Gross Sales</span>
                                <span class="text-lg font-medium">Rs. {{ number_format($sales, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 text-red-600">
                                <span class="text-lg">Sales Returns</span>
                                <span class="text-lg font-medium">(Rs. {{ number_format($saleReturns, 2) }})</span>
                            </div>
                            <div class="flex justify-between items-center py-2 font-bold border-t mt-2">
                                <span class="text-xl">Net Sales</span>
                                <span class="text-xl">Rs. {{ number_format($netSales, 2) }}</span>
                            </div>
                        </div>

                        <div class="border-b pb-4">
                            <h3 class="text-xl font-bold mb-4 uppercase text-gray-500 tracking-wider">Cost of Goods Sold</h3>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg">Cost of Goods Sold</span>
                                <span class="text-lg font-medium text-red-600">(Rs. {{ number_format($cogs, 2) }})</span>
                            </div>
                            <div class="flex justify-between items-center py-2 font-bold border-t mt-2 bg-gray-50 p-2 rounded">
                                <span class="text-xl">Gross Profit</span>
                                <span class="text-xl {{ $grossProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rs. {{ number_format($grossProfit, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="border-b pb-4">
                            <h3 class="text-xl font-bold mb-4 uppercase text-gray-500 tracking-wider">Operating Expenses</h3>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg">General Expenses</span>
                                <span class="text-lg font-medium text-red-600">(Rs. {{ number_format($expenses, 2) }})</span>
                            </div>
                        </div>

                        <div class="pt-4">
                            <div class="flex justify-between items-center p-4 rounded-lg {{ $netProfit >= 0 ? 'bg-green-100' : 'bg-red-100' }}">
                                <span class="text-2xl font-black uppercase">Net Profit / Loss</span>
                                <span class="text-3xl font-black {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                    Rs. {{ number_format($netProfit, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex justify-end">
                        <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-md font-bold hover:bg-black transition-colors">
                            <i class="fas fa-print mr-2"></i> Print Statement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
