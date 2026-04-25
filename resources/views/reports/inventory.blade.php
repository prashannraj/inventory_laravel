<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory/Stock Report') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Back to Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value (Cost)</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Potential Value (Sale)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php 
                                    $totalCostValue = 0; 
                                    $totalSaleValue = 0;
                                @endphp
                                @foreach($products as $product)
                                @php
                                    $costValue = $product->qty * $product->buying_price;
                                    $saleValue = $product->qty * $product->price;
                                    $totalCostValue += $costValue;
                                    $totalSaleValue += $saleValue;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <span class="font-bold {{ $product->qty <= $product->alert_quantity ? 'text-rose-600' : 'text-gray-900' }}">
                                            {{ $product->qty }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">Rs. {{ number_format($costValue, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-indigo-600">Rs. {{ number_format($saleValue, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 font-black">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-right uppercase tracking-widest text-xs">Total Inventory Valuation</td>
                                    <td class="px-6 py-4 text-right text-sm">Rs. {{ number_format($totalCostValue, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-indigo-600">Rs. {{ number_format($totalSaleValue, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
