<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Purchase Order') }} - {{ $purchase->purchase_no }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="purchaseForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('purchases.update', $purchase) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <x-input-label for="supplier_id" :value="__('Supplier')" />
                                <select id="supplier_id" name="supplier_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="store_id" :value="__('Warehouse/Store')" />
                                <select id="store_id" name="store_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ $purchase->store_id == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', $purchase->date->format('Y-m-d'))" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="pending" {{ $purchase->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received</option>
                                    <option value="partial" {{ $purchase->status == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="cancelled" {{ $purchase->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="payment_status" :value="__('Payment Status')" />
                                <select id="payment_status" name="payment_status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="unpaid" {{ $purchase->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partial" {{ $purchase->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="pending" {{ $purchase->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $purchase->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <x-text-input id="payment_method" class="block mt-1 w-full" type="text" name="payment_method" :value="old('payment_method', $purchase->payment_method)" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <x-input-label for="discount" :value="__('Discount')" />
                                <x-text-input id="discount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="discount" :value="old('discount', $purchase->discount)" />
                            </div>
                            <div>
                                <x-input-label for="shipping" :value="__('Shipping Cost')" />
                                <x-text-input id="shipping" class="block mt-1 w-full" type="number" step="0.01" min="0" name="shipping" :value="old('shipping', $purchase->shipping)" />
                            </div>
                            <div>
                                <x-input-label for="paid_amount" :value="__('Paid Amount')" />
                                <x-text-input id="paid_amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="paid_amount" :value="old('paid_amount', $purchase->paid_amount)" required />
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
                                    <tbody id="items-container">
                                        @foreach($purchase->items as $index => $item)
                                        <tr class="item-row">
                                            <td class="px-4 py-2">
                                                <select name="items[{{ $index }}][product_id]" class="product-select block w-full border-gray-300 rounded-md" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-cost="{{ $product->buying_price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" min="1" name="items[{{ $index }}][quantity]" class="quantity block w-full" :value="old('items.' . $index . '.quantity', $item->quantity)" required />
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_cost]" class="unit-cost block w-full" :value="old('items.' . $index . '.unit_cost', $item->unit_cost)" required />
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

                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea name="notes" id="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $purchase->notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('purchases.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Purchase') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function purchaseForm() {
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
                                        <option value="{{ $product->id }}" data-cost="{{ $product->buying_price }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" min="1" name="items[${index}][quantity]" class="quantity block w-full border-gray-300 rounded-md shadow-sm" required />
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" step="0.01" min="0" name="items[${index}][unit_cost]" class="unit-cost block w-full border-gray-300 rounded-md shadow-sm" required />
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
                    const unitCost = row.querySelector('.unit-cost');
                    const quantity = row.querySelector('.quantity');
                    const removeBtn = row.querySelector('.remove-item');

                    const calculate = () => {
                        const cost = parseFloat(unitCost.value) || 0;
                        const qty = parseInt(quantity.value) || 0;
                        const subtotal = cost * qty;
                        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
                    };

                    productSelect.addEventListener('change', (e) => {
                        const selectedOption = e.target.options[e.target.selectedIndex];
                        const cost = selectedOption.dataset.cost || 0;
                        unitCost.value = cost;
                        calculate();
                    });

                    unitCost.addEventListener('input', calculate);
                    quantity.addEventListener('input', calculate);

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
                        const unitCost = row.querySelector('.unit-cost');
                        const quantity = row.querySelector('.quantity');
                        const cost = parseFloat(unitCost.value) || 0;
                        const qty = parseInt(quantity.value) || 0;
                        const subtotal = cost * qty;
                        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            purchaseForm().init();
        });
    </script>
    @endpush
</x-app-layout>