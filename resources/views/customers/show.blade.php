<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Customer Ledger') }}: {{ $customer->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('customers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-print mr-1"></i> Print Statement
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Summary Cards -->
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <div class="text-xs font-bold text-gray-500 uppercase">Total Sales</div>
                    <div class="text-2xl font-black">Rs. {{ number_format($customer->sales->sum('net_amount'), 2) }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <div class="text-xs font-bold text-gray-500 uppercase">Outstanding Balance</div>
                    <div class="text-2xl font-black text-red-600">Rs. {{ number_format($customer->outstanding_balance, 2) }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <div class="text-xs font-bold text-gray-500 uppercase">Loyalty Points</div>
                    <div class="text-2xl font-black text-indigo-600">{{ number_format($customer->loyalty_points) }}</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                    <div class="text-xs font-bold text-gray-500 uppercase">Credit Limit</div>
                    <div class="text-2xl font-black">Rs. {{ number_format($customer->credit_limit, 2) }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Transaction History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sales as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                        <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_no }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">Rs. {{ number_format($sale->net_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold">Rs. {{ number_format($sale->paid_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">Rs. {{ number_format($sale->net_amount - $sale->paid_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sale->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($sale->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
