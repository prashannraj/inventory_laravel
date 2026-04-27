<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center py-1">
            <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                {{ __('POS Interface') }}
            </h2>
            <div class="text-sm text-gray-500" x-data="{ timer: new Date().toLocaleString() }" x-init="setInterval(() => timer = new Date().toLocaleString(), 1000)" x-text="timer"></div>
        </div>
    </x-slot>

    <div class="py-2" @keydown.window="handleShortcuts($event)">
        <div class="max-w-full mx-auto sm:px-2 lg:px-4">
            <div x-data="posSystem()" 
                 id="pos-container"
                 x-init="init()"
                 data-products="{{ json_encode($products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => (float)$p->price,
                    'qty' => $p->qty,
                    'category_id' => $p->category_id,
                    'tax_rate' => $p->taxRate ? (float)$p->taxRate->rate : 0,
                    'tax_name' => $p->taxRate ? $p->taxRate->name : null,
                    'primary_image' => $p->images->where('is_primary', true)->first()?->image_path
                 ])) }}"
                 class="flex flex-col lg:flex-row gap-4 h-[calc(100vh-6rem)]">
                
                <!-- Product Selection Area (Left Side) -->
                <div class="lg:w-3/5 xl:w-2/3 flex flex-col">
                    <div class="bg-white p-3 rounded-lg shadow mb-3">
                        <div class="flex gap-2 mb-2 items-center">
                            <div class="flex-1 relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" x-ref="barcodeInput" x-model="barcode" @keydown.enter.prevent="scanBarcode()" placeholder="Scan Barcode (ESC)..." class="text-sm pl-9 block w-full border-indigo-200 focus:border-indigo-500 rounded-md shadow-sm bg-indigo-50 py-1.5">
                            </div>
                            <div class="flex-1 relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-xs">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" x-ref="searchInput" x-model="searchQuery" @input.debounce.300ms="filterProducts()" placeholder="Search (F2)..." class="text-sm pl-9 block w-full border-gray-200 focus:border-indigo-500 rounded-md shadow-sm py-1.5">
                            </div>
                            <select x-model="selectedCategory" @change="filterProducts()" class="text-sm w-40 border-gray-200 rounded-md shadow-sm py-1.5">
                                <option value="">All Categories</option>
                                @foreach($products->pluck('category.name', 'category.id')->unique() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Product Grid with scroll -->
                        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-3 h-[calc(100vh-14rem)] overflow-y-auto p-1">
                            <template x-for="product in filteredProducts" :key="product.id">
                                <div @click="addToCart(product)" class="cursor-pointer border border-gray-100 rounded-lg p-2 hover:shadow-md hover:border-indigo-400 transition bg-white flex flex-col items-center text-center">
                                    <div class="w-full h-16 bg-gray-50 rounded mb-1 flex items-center justify-center">
                                        <template x-if="product.primary_image">
                                            <img :src="'/storage/' + product.primary_image" class="h-full object-contain">
                                        </template>
                                        <template x-if="!product.primary_image">
                                            <i class="fas fa-box text-xl text-gray-200"></i>
                                        </template>
                                    </div>
                                    <div class="text-[11px] font-bold truncate w-full" x-text="product.name"></div>
                                    <div class="text-xs text-indigo-600 font-bold" x-text="formatCurrency(product.price)"></div>
                                    <div class="text-[9px]" :class="product.qty > 0 ? 'text-green-600' : 'text-red-600'" x-text="product.qty + ' in stock'"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Cart & Checkout Area (Right Side) -->
                <div class="lg:w-2/5 xl:w-1/3 flex flex-col">
                    <form action="{{ route('sales.store') }}" method="POST" class="flex flex-col h-full">
                        @csrf
                        
                        <!-- Hidden required fields -->
                        <input type="hidden" name="store_id" value="{{ $stores->first()->id ?? '' }}">
                        <input type="hidden" name="date" value="{{ date('Y-m-d H:i:s') }}">
                        <input type="hidden" name="notes" value="">
                        
                        <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col h-full border border-gray-200">
                            
                            <!-- Customer Info (Compact) -->
                            <div class="p-2 bg-gray-50 border-b flex items-center justify-between gap-2">
                                <select name="customer_id" class="text-xs block w-full border-gray-300 rounded shadow-sm py-1">
                                    <option value="">Walk-in Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-[10px] text-right leading-tight min-w-[100px]">
                                    <span class="font-bold text-gray-700">FY: 2080/81</span><br>
                                    <span class="text-gray-500">{{ date('H:i:s') }}</span>
                                </div>
                            </div>

                            <!-- Cart Items (Maximized View) -->
                            <div class="flex-1 overflow-y-auto bg-white">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-gray-100 sticky top-0 z-10">
                                        <tr>
                                            <th class="px-2 py-1 font-bold">Item</th>
                                            <th class="px-2 py-1 text-center font-bold">Qty</th>
                                            <th class="px-2 py-1 text-right font-bold">Total</th>
                                            <th class="px-2 py-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="(item, index) in cart" :key="index">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-2 py-1">
                                                    <div class="font-bold text-[11px] leading-tight" x-text="item.name"></div>
                                                    <div class="text-[9px] text-gray-400" x-text="formatCurrency(item.unit_price)"></div>
                                                </td>
                                                <td class="px-2 py-1">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <button type="button" @click="updateQty(index, -1)" class="w-5 h-5 bg-gray-100 rounded flex items-center justify-center text-[10px]">-</button>
                                                        <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" class="w-8 border-gray-200 text-center text-[11px] p-0 rounded">
                                                        <button type="button" @click="updateQty(index, 1)" class="w-5 h-5 bg-gray-100 rounded flex items-center justify-center text-[10px]">+</button>
                                                    </div>
                                                </td>
                                                <td class="px-2 py-1 text-right font-bold" x-text="formatCurrency(item.quantity * item.unit_price)"></td>
                                                <td class="px-1 py-1 text-center">
                                                    <button type="button" @click="removeFromCart(index)" class="text-red-400 hover:text-red-600">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                                <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                                                <input type="hidden" :name="'items['+index+'][unit_price]'" :value="item.unit_price">
                                                <input type="hidden" :name="'items['+index+'][discount]'" :value="item.discount || 0">
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <template x-if="cart.length === 0">
                                    <div class="text-center py-10 text-gray-400 text-sm">Cart is empty</div>
                                </template>
                            </div>

                            <!-- Order Summary & Checkout (Very Compact) -->
                            <div class="p-2 border-t bg-gray-50">
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Subtotal:</span>
                                        <span class="font-bold" x-text="formatCurrency(subtotal())"></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500">Discount:</span>
                                        <div class="flex items-center">
                                            <input type="number" name="discount" x-model.number="discount" class="w-16 text-right border-gray-300 rounded text-[11px] py-0 px-1 ml-1" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Tax:</span>
                                        <span class="font-bold" x-text="formatCurrency(taxTotal())"></span>
                                    </div>
                                    <div class="flex justify-between text-indigo-700 font-black text-sm border-t border-indigo-100 pt-1">
                                        <span>TOTAL:</span>
                                        <span x-text="formatCurrency(grandTotal())"></span>
                                    </div>
                                </div>

                                <!-- Payment Options (Grid) -->
                                <div class="grid grid-cols-3 gap-1 mt-2">
                                    @foreach($paymentMethods as $method)
                                        <label class="border rounded p-1.5 flex flex-col items-center cursor-pointer transition" :class="paymentMethod === '{{ $method->name }}' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white border-gray-200'">
                                            <input type="radio" name="payment_method" value="{{ $method->name }}" x-model="paymentMethod" class="hidden" data-gateway="{{ $method->gateway }}">
                                            <i class="fas text-[10px] mb-0.5 {{ $method->type === 'cash' ? 'fa-money-bill' : ($method->type === 'card' ? 'fa-credit-card' : 'fa-wallet') }}"></i>
                                            <span class="text-[9px] font-bold">{{ $method->name }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <!-- Payment Input (Bottom Row) -->
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex-1 relative">
                                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-[10px]">Rs.</span>
                                        <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount" class="block w-full text-right font-black border-gray-300 rounded-md py-1.5 text-sm pl-6" placeholder="Amount Paid">
                                    </div>
                                    <div class="text-right leading-tight pr-1">
                                        <div class="text-[9px] text-gray-400">Change</div>
                                        <div class="text-xs font-black text-green-600" x-text="formatCurrency(Math.max(0, paidAmount - grandTotal()))"></div>
                                    </div>
                                </div>

                                <button type="submit" :disabled="cart.length === 0" class="w-full mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-sm shadow flex items-center justify-center gap-2 transition-all active:scale-95">
                                    <i class="fas fa-print"></i>
                                    COMPLETE & PRINT (F10)
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript posSystem logic remains largely same, ensure updatePaidAmount is called correctly -->
    <script>
        function posSystem() {
            return {
                products: JSON.parse(document.getElementById('pos-container').dataset.products || '[]'),
                barcode: '',
                searchQuery: '',
                selectedCategory: '',
                filteredProducts: [],
                cart: [],
                discount: 0,
                paymentMethod: 'Cash',
                paidAmount: 0,

                init() {
                    this.filteredProducts = this.products;
                    this.$nextTick(() => { this.$refs.barcodeInput.focus(); });
                    this.$watch('paymentMethod', () => { this.handlePaymentMethodChange(); });
                },

                handleShortcuts(e) {
                    if (e.key === 'F2') { e.preventDefault(); this.$refs.searchInput.focus(); }
                    else if (e.key === 'F10') { if (this.cart.length > 0) document.querySelector('form').submit(); }
                    else if (e.key === 'Escape') { this.$refs.barcodeInput.focus(); }
                },

                scanBarcode() {
                    const product = this.products.find(p => p.sku.toLowerCase() === this.barcode.toLowerCase());
                    if (product) { this.addToCart(product); this.barcode = ''; }
                    else { this.barcode = ''; }
                },

                filterProducts() {
                    this.filteredProducts = this.products.filter(p => {
                        const matchesSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                            p.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesCategory = !this.selectedCategory || p.category_id == this.selectedCategory;
                        return matchesSearch && matchesCategory;
                    });
                },

                addToCart(product) {
                    const existingItem = this.cart.find(item => item.product_id === product.id);
                    if (existingItem) {
                        if (existingItem.quantity < product.qty) existingItem.quantity++;
                    } else if (product.qty > 0) {
                        this.cart.push({
                            product_id: product.id,
                            name: product.name,
                            unit_price: product.price,
                            quantity: 1,
                            max_qty: product.qty,
                            tax_rate: product.tax_rate || 0,
                            discount: 0
                        });
                    }
                    this.updatePaidAmount();
                },

                removeFromCart(index) { this.cart.splice(index, 1); this.updatePaidAmount(); },

                updateQty(index, delta) {
                    const item = this.cart[index];
                    const newQty = item.quantity + delta;
                    if (newQty > 0 && newQty <= item.max_qty) item.quantity = newQty;
                    this.updatePaidAmount();
                },

                subtotal() { return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0); },

                taxTotal() { return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price * (item.tax_rate / 100)), 0); },

                grandTotal() { return Math.max(0, this.subtotal() - this.discount + this.taxTotal()); },

                updatePaidAmount() { this.paidAmount = this.grandTotal(); },

                formatCurrency(amount) {
                    return 'Rs. ' + amount.toLocaleString(undefined, { minimumFractionDigits: 2 });
                },

                handlePaymentMethodChange() {
                    const selectedRadio = document.querySelector('input[name="payment_method"]:checked');
                    if (selectedRadio && selectedRadio.dataset.gateway) this.updatePaidAmount();
                }
            }
        }
    </script>
</x-app-layout>