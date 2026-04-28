<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sale') }} - {{ $sale->invoice_no }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('sales.update', $sale) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <x-input-label for="customer_id" :value="__('Customer')" />
                                <select name="customer_id" id="customer_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Walk-in Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="store_id" :value="__('Store')" />
                                <select name="store_id" id="store_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ $sale->store_id == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Sale Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="datetime-local" name="date" :value="old('date', $sale->date->format('Y-m-d\TH:i'))" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="pending" {{ $sale->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $sale->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $sale->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="returned" {{ $sale->status == 'returned' ? 'selected' : '' }}>Returned</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="payment_status" :value="__('Payment Status')" />
                                <select name="payment_status" id="payment_status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="unpaid" {{ $sale->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partial" {{ $sale->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="pending" {{ $sale->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $sale->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <x-text-input id="payment_method" class="block mt-1 w-full" type="text" name="payment_method" :value="old('payment_method', $sale->payment_method)" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <x-input-label for="discount" :value="__('Order Discount')" />
                                <x-text-input id="discount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="discount" :value="old('discount', $sale->discount)" />
                            </div>
                            <div>
                                <x-input-label for="shipping" :value="__('Shipping Cost')" />
                                <x-text-input id="shipping" class="block mt-1 w-full" type="number" step="0.01" min="0" name="shipping" :value="old('shipping', $sale->shipping)" />
                            </div>
                            <div>
                                <x-input-label for="paid_amount" :value="__('Paid Amount')" />
                                <x-text-input id="paid_amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="paid_amount" :value="old('paid_amount', $sale->paid_amount)" required />
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
                                    <tbody id="items-container">
                                        @foreach($sale->items as $index => $item)
                                        <tr class="item-row">
                                            <td class="px-4 py-2">
                                                <select name="items[{{ $index }}][product_id]" class="product-select block w-full border-gray-300 rounded-md" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }} (Stock: {{ $product->qty }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" class="unit-price block w-full" :value="old('items.' . $index . '.unit_price', $item->unit_price)" required />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" min="1" name="items[{{ $index }}][quantity]" class="quantity block w-full" :value="old('items.' . $index . '.quantity', $item->quantity)" required />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" step="0.01" min="0" name="items[{{ $index }}][discount]" class="discount block w-full" :value="old('items.' . $index . '.discount', $item->discount)" />
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="subtotal">0.00</span>
                                            </td>
                                            <td class="px-4 py-2">
                                                <button type="button" class="remove-item text-red-600 hover:text-red-800">Remove</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <button type="button" id="add-item" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                    Add Item
                                </button>
                            </div>
                        </div>

                        <div class="mb-8">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea name="notes" id="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $sale->notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('sales.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Sale') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function saleItems() {
            return {
                init() {
                    this.calculateAll();
                    this.bindEvents();
                },
                bindEvents() {
                    // Add item button
                    document.getElementById('add-item').addEventListener('click', () => {
                        const container = document.getElementById('items-container');
                        const index = container.children.length;
                        const row = document.createElement('tr');
                        row.className = 'item-row';
                        row.innerHTML = `
                            <td class="px-4 py-2">
                                <select name="items[${index}][product_id]" class="product-select block w-full border-gray-300 rounded-md" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} (Stock: {{ $product->qty }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0" name="items[${index}][unit_price]" class="unit-price block w-full border-gray-300 rounded-md shadow-sm" required />
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="1" name="items[${index}][quantity]" class="quantity block w-full border-gray-300 rounded-md shadow-sm" required />
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0" name="items[${index}][discount]" class="discount block w-full border-gray-300 rounded-md shadow-sm" />
                            </td>
                            <td class="px-4 py-2">
                                <span class="subtotal">0.00</span>
                            </td>
                            <td class="px-4 py-2">
                                <button type="button" class="remove-item text-red-600 hover:text-red-800">Remove</button>
                            </td>
                        `;
                        container.appendChild(row);
                        this.bindRowEvents(row);
                    });

                    // Bind events to existing rows
                    document.querySelectorAll('.item-row').forEach(row => this.bindRowEvents(row));
                },
                bindRowEvents(row) {
                    const productSelect = row.querySelector('.product-select');
                    const unitPrice = row.querySelector('.unit-price');
                    const quantity = row.querySelector('.quantity');
                    const discount = row.querySelector('.discount');
                    const removeBtn = row.querySelector('.remove-item');

                    const calculate = () => {
                        const price = parseFloat(unitPrice.value) || 0;
                        const qty = parseInt(quantity.value) || 0;
                        const disc = parseFloat(discount.value) || 0;
                        const subtotal = (price * qty) - disc;
                        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
                    };

                    productSelect.addEventListener('change', (e) => {
                        const selectedOption = e.target.options[e.target.selectedIndex];
                        const price = selectedOption.dataset.price || 0;
                        unitPrice.value = price;
                        calculate();
                    });

                    unitPrice.addEventListener('input', calculate);
                    quantity.addEventListener('input', calculate);
                    discount.addEventListener('input', calculate);

                    removeBtn.addEventListener('click', () => {
                        row.remove();
                        this.renumberIndexes();
                    });
                },
                renumberIndexes() {
                    const rows = document.querySelectorAll('.item-row');
                    rows.forEach((row, index) => {
                        row.querySelectorAll('[name]').forEach(input => {
                            input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                        });
                    });
                },
                calculateAll() {
                    document.querySelectorAll('.item-row').forEach(row => {
                        const unitPrice = row.querySelector('.unit-price');
                        const quantity = row.querySelector('.quantity');
                        const discount = row.querySelector('.discount');
                        const price = parseFloat(unitPrice.value) || 0;
                        const qty = parseInt(quantity.value) || 0;
                        const disc = parseFloat(discount.value) || 0;
                        const subtotal = (price * qty) - disc;
                        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            saleItems().init();
        });
    </script>
    @endpush
</x-app-layout>