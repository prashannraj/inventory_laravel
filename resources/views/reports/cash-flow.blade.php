<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cash Flow Statement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('reports.cash-flow') }}" class="mb-8 flex gap-4 items-end bg-gray-50 p-4 rounded-lg">
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
                            <h3 class="text-xl font-bold mb-4 uppercase text-gray-500 tracking-wider">Cash Inflow</h3>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg">Sale Payments Received</span>
                                <span class="text-lg font-medium text-green-600">Rs. {{ number_format($salePayments, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 font-bold border-t mt-2">
                                <span class="text-xl">Total Cash Inflow</span>
                                <span class="text-xl text-green-600">Rs. {{ number_format($salePayments, 2) }}</span>
                            </div>
                        </div>

                        <div class="border-b pb-4">
                            <h3 class="text-xl font-bold mb-4 uppercase text-gray-500 tracking-wider">Cash Outflow</h3>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg">Purchase Payments / Total Purchases</span>
                                <span class="text-lg font-medium text-red-600">(Rs. {{ number_format($purchases, 2) }})</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg">Operating Expenses</span>
                                <span class="text-lg font-medium text-red-600">(Rs. {{ number_format($expenses, 2) }})</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-lg">Sale Returns (Refunds)</span>
                                <span class="text-lg font-medium text-red-600">(Rs. {{ number_format($saleReturns, 2) }})</span>
                            </div>
                            <div class="flex justify-between items-center py-2 font-bold border-t mt-2">
                                <span class="text-xl">Total Cash Outflow</span>
                                <span class="text-xl text-red-600">(Rs. {{ number_format($purchases + $expenses + $saleReturns, 2) }})</span>
                            </div>
                        </div>

                        <div class="pt-4">
                            <div class="flex justify-between items-center p-4 rounded-lg {{ $netCashFlow >= 0 ? 'bg-green-100' : 'bg-red-100' }}">
                                <span class="text-2xl font-black uppercase">Net Cash Flow</span>
                                <span class="text-3xl font-black {{ $netCashFlow >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                    Rs. {{ number_format($netCashFlow, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex justify-end gap-4">
                        <a href="{{ route('reports.export', ['type' => 'cash-flow', 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="bg-green-600 text-white px-6 py-2 rounded-md font-bold hover:bg-green-700 transition-colors">
                            <i class="fas fa-file-excel mr-2"></i> Export Excel
                        </a>
                        <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-md font-bold hover:bg-black transition-colors">
                            <i class="fas fa-print mr-2"></i> Print Statement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
