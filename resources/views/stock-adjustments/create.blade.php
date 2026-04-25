<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Stock Adjustment') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="adjustmentForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('stock-adjustments.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <x-input-label for="store_id" :value="__('Warehouse/Store')" />
                                <select id="store_id" name="store_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" value="{{ date('Y-m-d') }}" required />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="reason" :value="__('Reason for Adjustment')" />
                            <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" placeholder="e.g. Damaged items, Inventory count correction" required />
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-2">Adjusted Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-48">Quantity (+ for Add, - for Remove)</th>
                                            <th class="px-4 py-2 w-16"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-2">
                                                    <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">{{ $product->name }} (Available: {{ $product->qty }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <x-text-input type="number" ::name="'items[' + index + '][quantity]'" x-model.number="item.quantity" class="w-full" required />
                                                </td>
                                                <td class="px-4 py-2 text-right">
                                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" @click="addItem()" class="mt-2 text-blue-600 hover:text-blue-900 font-medium">
                                <i class="fas fa-plus mr-1"></i> Add Item
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-6 border-t pt-4">
                            <a href="{{ route('stock-adjustments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Process Adjustment') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adjustmentForm() {
            return {
                items: [{
                    product_id: '',
                    quantity: 0
                }],
                
                addItem() {
                    this.items.push({
                        product_id: '',
                        quantity: 0
                    });
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            }
        }
    </script>
</x-app-layout>
