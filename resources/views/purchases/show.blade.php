<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Details') }}: {{ $purchase->purchase_no }}
            </h2>
            <div class="flex space-x-2">
                @if($purchase->document)
                    <a href="{{ asset('storage/' . $purchase->document) }}" target="_blank" class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded text-sm">
                        View Invoice
                    </a>
                @endif
                <a href="{{ route('purchases.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 border-b pb-6">
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Date</p>
                            <p class="text-lg font-medium">{{ $purchase->date->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Supplier</p>
                            <p class="text-lg font-medium">{{ $purchase->supplier->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Store/Warehouse</p>
                            <p class="text-lg font-medium">{{ $purchase->store->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Status</p>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'ordered' => 'bg-blue-100 text-blue-800',
                                    'received' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$purchase->status] }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Ordered Items</h3>
                    <div class="overflow-x-auto mb-8">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cost Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($purchase->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">Rs. {{ number_format($item->cost_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right uppercase tracking-wider">Items Total</td>
                                    <td class="px-6 py-4 text-right text-lg">Rs. {{ number_format($purchase->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                            <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Financial Summary</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-bold">Rs. {{ number_format($purchase->total_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-rose-600">
                                    <span>Discount:</span>
                                    <span class="font-bold">- Rs. {{ number_format($purchase->discount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-emerald-600">
                                    <span>Tax Amount:</span>
                                    <span class="font-bold">+ Rs. {{ number_format($purchase->tax_amount, 2) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-3 mt-3 flex justify-between items-center text-indigo-600">
                                    <span class="text-lg font-black uppercase">Net Total:</span>
                                    <span class="text-2xl font-black">Rs. {{ number_format($purchase->net_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Notes & Observations</h4>
                            <div class="bg-white border border-gray-100 p-4 rounded-xl min-h-[100px] italic text-gray-600">
                                {{ $purchase->notes ?: 'No additional notes provided for this purchase order.' }}
                            </div>
                            <div class="mt-6">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Recorded By</p>
                                <p class="text-sm font-bold text-gray-700 uppercase tracking-tighter">{{ $purchase->user->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
