<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Transfer Details') }}: {{ $stockTransfer->transfer_no }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $stockTransfer->transfer_no }}</h3>
                            <p class="text-gray-500">Date: {{ $stockTransfer->date->format('F d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'sent' => 'bg-blue-100 text-blue-800',
                                    'received' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColors[$stockTransfer->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($stockTransfer->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border-b pb-6">
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">From Store</h4>
                            <p class="font-medium text-lg">{{ $stockTransfer->fromStore->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">To Store</h4>
                            <p class="font-medium text-lg">{{ $stockTransfer->toStore->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">Initiated By</h4>
                            <p class="font-medium text-lg">{{ $stockTransfer->user->name }}</p>
                        </div>
                    </div>

                    @if($stockTransfer->notes)
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-bold text-gray-400 uppercase mb-2">Notes</h4>
                        <p>{{ $stockTransfer->notes }}</p>
                    </div>
                    @endif

                    <div class="mb-8">
                        <h4 class="text-lg font-bold mb-4">Transfer Items</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stockTransfer->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->sku }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium">{{ $item->quantity }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($stockTransfer->status === 'pending')
                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
                        <form action="{{ route('stock-transfers.status.update', $stockTransfer) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                Cancel Transfer
                            </button>
                        </form>
                        <form action="{{ route('stock-transfers.status.update', $stockTransfer) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="sent">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                Mark as Sent
                            </button>
                        </form>
                    </div>
                    @elseif($stockTransfer->status === 'sent')
                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
                        <form action="{{ route('stock-transfers.status.update', $stockTransfer) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="received">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow-sm transition-colors">
                                Confirm Receipt (Update Inventory)
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
