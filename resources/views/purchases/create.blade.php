<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Purchase Order') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="purchaseForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('purchases.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <x-input-label for="supplier_id" :value="__('Supplier')" />
                                <select id="supplier_id" name="supplier_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                            <h3 class="text-lg font-medium mb-2">Order Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-32">Quantity</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-40">Cost Price</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-40">Subtotal</th>
                                            <th class="px-4 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-2">
                                                    <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" @change="updatePrice(index)" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->buying_price }}">{{ $product->name }} ({{ $product->sku }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <x-text-input type="number" ::name="'items[' + index + '][quantity]'" x-model.number="item.quantity" class="w-full" min="1" required />
                                                </td>
                                                <td class="px-4 py-2">
                                                    <x-text-input type="number" step="0.01" ::name="'items[' + index + '][cost_price]'" x-model.number="item.cost_price" class="w-full" required />
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span class="text-sm font-medium" x-text="formatCurrency(item.quantity * item.cost_price)"></span>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="notes" :value="__('Notes')" />
                                    <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="document" :value="__('Attach Invoice (PDF/Image)')" />
                                    <input type="file" id="document" name="document" class="block mt-1 w-full">
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="status" :value="__('Purchase Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="pending">Pending</option>
                                        <option value="ordered">Ordered</option>
                                        <option value="received">Received (Update Stock)</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-600">Total Items Cost:</span>
                                    <span class="font-medium" x-text="formatCurrency(totalItemsCost())"></span>
                                </div>
                                <div class="flex justify-between mb-2 items-center">
                                    <span class="text-gray-600">Discount:</span>
                                    <x-text-input type="number" step="0.01" name="discount" x-model.number="discount" class="w-32 text-right" />
                                </div>
                                <div class="flex justify-between mb-2 items-center">
                                    <span class="text-gray-600">Tax Amount:</span>
                                    <div class="w-32 text-right">
                                        <span class="text-sm text-gray-500 italic">Auto-calculated</span>
                                        <input type="hidden" name="tax_amount" x-model.number="tax_amount" value="0">
                                    </div>
                                </div>
                                <div class="border-t pt-2 mt-2 flex justify-between">
                                    <span class="text-lg font-bold">Grand Total:</span>
                                    <span class="text-lg font-bold text-indigo-600" x-text="formatCurrency(grandTotal())"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ms-4">
                                {{ __('Create Purchase Order') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function purchaseForm() {
            return {
                items: [{
                    product_id: '',
                    quantity: 1,
                    cost_price: 0
                }],
                discount: 0,
                tax_amount: 0,
                
                addItem() {
                    this.items.push({
                        product_id: '',
                        quantity: 1,
                        cost_price: 0
                    });
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                updatePrice(index) {
                    const select = document.querySelectorAll('select[name^="items"]')[index];
                    const selectedOption = select.options[select.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    if (price) {
                        this.items[index].cost_price = parseFloat(price);
                    }
                },
                
                totalItemsCost() {
                    return this.items.reduce((total, item) => total + (item.quantity * item.cost_price), 0);
                },
                
                grandTotal() {
                    return this.totalItemsCost() - (this.discount || 0) + (this.tax_amount || 0);
                },
                
                formatCurrency(amount) {
                    return 'Rs. ' + amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</x-app-layout>
