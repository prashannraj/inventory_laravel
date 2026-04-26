<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Sale (Manual Order)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <x-input-label for="customer_id" :value="__('Customer')" />
                                <select name="customer_id" id="customer_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Walk-in Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="store_id" :value="__('Store')" />
                                <select name="store_id" id="store_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Sale Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="datetime-local" name="date" :value="old('date', date('Y-m-d\TH:i'))" required />
                            </div>
                        </div>

                        <div class="mb-8" x-data="saleItems()">
                            <h3 class="text-lg font-medium mb-4">Sale Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                            <th class="px-4 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-for="(item, index) in items" :key="index">
                                            <tr>
                                                <td class="px-4 py-2">
                                                    <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="updatePrice(index)" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} ({{ $product->sku }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" :name="'items['+index+'][unit_price]'" x-model.number="item.unit_price" step="0.01" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <input type="number" :name="'items['+index+'][discount]'" x-model.number="item.discount" step="0.01" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                                </td>
                                                <td class="px-4 py-2 text-sm font-bold" x-text="formatCurrency((item.unit_price * item.quantity) - item.discount)"></td>
                                                <td class="px-4 py-2 text-right">
                                                    <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" @click="addItem()" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                                <i class="fas fa-plus mr-1"></i> Add Item
                            </button>

                            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <x-input-label for="notes" :value="__('Order Notes')" />
                                    <textarea name="notes" id="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                                </div>
                                <div class="bg-gray-50 p-6 rounded-lg space-y-3">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Subtotal:</span>
                                        <span x-text="formatCurrency(subtotal())"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Order Discount:</span>
                                        <input type="number" name="discount" x-model.number="orderDiscount" step="0.01" class="w-32 text-right border-gray-300 rounded-md shadow-sm text-sm">
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Tax Rate (%):</span>
                                        <input type="number" name="tax_rate" x-model.number="taxRate" step="0.01" class="w-32 text-right border-gray-300 rounded-md shadow-sm text-sm" @input="updateTaxAmountFromRate()">
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Tax Amount:</span>
                                        <input type="number" name="tax_amount" x-model.number="taxAmount" step="0.01" class="w-32 text-right border-gray-300 rounded-md shadow-sm text-sm" @input="updateTaxRateFromAmount()">
                                    </div>
                                    <div class="flex justify-between text-xl font-bold border-t pt-2 text-indigo-600">
                                        <span>Total:</span>
                                        <span x-text="formatCurrency(grandTotal())"></span>
                                    </div>
                                    <div class="pt-4 space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                                <select name="payment_method" id="payment_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="cash">Cash</option>
                                                    <option value="card">Card</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                </select>
                                            </div>
                                            <div>
                                                <x-input-label for="paid_amount" :value="__('Paid Amount')" />
                                                <input type="number" name="paid_amount" x-model.number="paidAmount" step="0.01" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition shadow-lg">
                                            CREATE ORDER
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function saleItems() {
            return {
                items: [{ product_id: '', unit_price: 0, quantity: 1, discount: 0 }],
                orderDiscount: 0,
                taxRate: 0,
                taxAmount: 0,
                paidAmount: 0,

                addItem() {
                    this.items.push({ product_id: '', unit_price: 0, quantity: 1, discount: 0 });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                updatePrice(index) {
                    const select = event.target;
                    const option = select.options[select.selectedIndex];
                    const price = option.dataset.price;
                    if (price) {
                        this.items[index].unit_price = parseFloat(price);
                    }
                },

                subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.unit_price * item.quantity - (item.discount || 0)), 0);
                },

                taxableAmount() {
                    return this.subtotal() - (this.orderDiscount || 0);
                },

                updateTaxAmountFromRate() {
                    // Calculate tax amount based on tax rate
                    const taxable = this.taxableAmount();
                    this.taxAmount = taxable * (this.taxRate / 100);
                },

                updateTaxRateFromAmount() {
                    // Calculate tax rate based on tax amount
                    const taxable = this.taxableAmount();
                    if (taxable > 0) {
                        this.taxRate = (this.taxAmount / taxable) * 100;
                    } else {
                        this.taxRate = 0;
                    }
                },

                grandTotal() {
                    return Math.max(0, this.taxableAmount() + (this.taxAmount || 0));
                },

                formatCurrency(amount) {
                    return 'Rs. ' + amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</x-app-layout>
