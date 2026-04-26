<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Process Sale Return') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="returnForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Sale Selection -->
                    <div class="mb-8 p-4 bg-indigo-50 rounded-lg">
                        <x-input-label for="sale_id_select" :value="__('Select Sale Invoice to Return')" />
                        <div class="flex gap-2 mt-1">
                            <select id="sale_id_select" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Select Completed Sale --</option>
                                @foreach($sales as $sale)
                                    <option value="{{ $sale->id }}" {{ (isset($selectedSale) && $selectedSale->id == $sale->id) ? 'selected' : '' }}>
                                        {{ $sale->invoice_no }} - {{ $sale->customer->name ?? 'Walk-in Customer' }} ({{ $sale->date->format('Y-m-d') }}) - {{ number_format($sale->net_amount, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" @click="loadSale()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-bold">
                                Load
                            </button>
                        </div>
                    </div>

                    @if(isset($selectedSale))
                    <form action="{{ route('sale-returns.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="sale_id" value="{{ $selectedSale->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <h4 class="text-sm font-bold text-gray-400 uppercase">Sale Details</h4>
                                <p class="font-medium">Invoice: {{ $selectedSale->invoice_no }}</p>
                                <p class="font-medium">Customer: {{ $selectedSale->customer->name ?? 'Walk-in Customer' }}</p>
                                <p class="font-medium">Store: {{ $selectedSale->store->name }}</p>
                            </div>
                            <div>
                                <x-input-label for="date" :value="__('Return Date')" />
                                <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" value="{{ date('Y-m-d') }}" required />
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-2">Select Items to Return</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sold Qty</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sold Price</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">Return Qty</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($selectedSale->items as $index => $item)
                                        <tr>
                                            <td class="px-4 py-2">
                                                {{ $item->product->name }}
                                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                                            </td>
                                            <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                                            <td class="px-4 py-2 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="px-4 py-2">
                                                <x-text-input type="number" 
                                                    name="items[{{ $index }}][quantity]" 
                                                    class="w-full text-right" 
                                                    value="0"
                                                    min="0" 
                                                    max="{{ $item->quantity }}" 
                                                    x-on:input="updateTotal()"
                                                    data-price="{{ $item->unit_price }}" />
                                            </td>
                                            <td class="px-4 py-2 text-right font-medium">
                                                <span class="item-subtotal">0.00</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50">
                                            <td colspan="4" class="px-4 py-2 text-right font-bold uppercase">Grand Total</td>
                                            <td class="px-4 py-2 text-right font-black text-lg text-red-600">
                                                <span id="grand-total">0.00</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Reason for Return')" />
                            <x-text-input id="notes" class="block mt-1 w-full" type="text" name="notes" placeholder="e.g. Defective item, Wrong size" required />
                        </div>

                        <div class="flex items-center justify-end mt-6 border-t pt-4">
                            <a href="{{ route('sale-returns.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Complete Return') }}
                            </x-primary-button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-12 text-gray-500 italic">
                        Select a sale above to start the return process.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function returnForm() {
            return {
                loadSale() {
                    const saleId = document.getElementById('sale_id_select').value;
                    if (saleId) {
                        window.location.href = "{{ route('sale-returns.create') }}?sale_id=" + saleId;
                    }
                },
                
                updateTotal() {
                    let grandTotal = 0;
                    document.querySelectorAll('input[name^="items"]').forEach(input => {
                        if (input.name.includes('[quantity]')) {
                            const qty = parseFloat(input.value) || 0;
                            const price = parseFloat(input.dataset.price) || 0;
                            const subtotal = qty * price;
                            
                            const row = input.closest('tr');
                            row.querySelector('.item-subtotal').textContent = subtotal.toFixed(2);
                            grandTotal += subtotal;
                        }
                    });
                    document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
                }
            }
        }
    </script>
</x-app-layout>
