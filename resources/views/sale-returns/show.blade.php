<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sale Return Details') }}: {{ $saleReturn->return_no }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $saleReturn->return_no }}</h3>
                            <p class="text-gray-500">Date: {{ $saleReturn->date->format('F d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Returned
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border-b pb-6">
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">Sale Reference</h4>
                            <p class="font-medium text-lg">{{ $saleReturn->sale->invoice_no }}</p>
                            <p class="text-sm text-gray-500">Store: {{ $saleReturn->sale->store->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">Customer</h4>
                            <p class="font-medium text-lg">{{ $saleReturn->sale->customer->name ?? 'Walk-in Customer' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">Processed By</h4>
                            <p class="font-medium text-lg">{{ $saleReturn->user->name }}</p>
                        </div>
                    </div>

                    @if($saleReturn->notes)
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">Reason for Return</h4>
                        <p>{{ $saleReturn->notes }}</p>
                    </div>
                    @endif

                    <div class="mb-8">
                        <h4 class="text-lg font-bold mb-4">Returned Items</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($saleReturn->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-bold">{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-6 py-4 text-right font-bold uppercase">Total Refund Amount</td>
                                        <td class="px-6 py-4 text-right font-black text-xl text-red-600">
                                            {{ number_format($saleReturn->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
                        <a href="{{ route('sale-returns.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded shadow-sm transition-colors">
                            Back to List
                        </a>
                        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                            <i class="fas fa-print mr-2"></i> Print Return Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
