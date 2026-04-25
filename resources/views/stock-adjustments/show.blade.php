<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stock Adjustment Details') }}: {{ $stockAdjustment->adjustment_no }}
            </h2>
            <a href="{{ route('stock-adjustments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border-b pb-6">
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Adjustment Date</p>
                            <p class="text-lg font-medium">{{ $stockAdjustment->date->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Store/Warehouse</p>
                            <p class="text-lg font-medium">{{ $stockAdjustment->store->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Processed By</p>
                            <p class="text-lg font-medium">{{ $stockAdjustment->user->name }}</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <p class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-2">Reason for Adjustment</p>
                        <div class="bg-gray-50 p-4 rounded-lg italic">
                            {{ $stockAdjustment->reason }}
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Adjusted Items</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Adjusted</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($stockAdjustment->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-bold {{ $item->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item->quantity > 0 ? '+' : '' }}{{ $item->quantity }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
