<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('POS Interface') }}
            </h2>
            <div class="text-sm text-gray-500" x-data="{ timer: new Date().toLocaleString() }" x-init="setInterval(() => timer = new Date().toLocaleString(), 1000)" x-text="timer"></div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-4 lg:px-6">
            <div x-data="posSystem()" 
                 id="pos-container"
                 data-products="{{ json_encode($products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'price' => (float)$p->price,
                    'qty' => $p->qty,
                    'category_id' => $p->category_id,
                    'primary_image' => $p->images->where('is_primary', true)->first()?->image_path
                 ])) }}"
                 class="flex flex-col lg:flex-row gap-8 h-[calc(100vh-12rem)]">
                <!-- Product Selection Area -->
                <div class="lg:w-2/3">
                    <div class="bg-white p-4 rounded-lg shadow mb-6">
                        <div class="flex gap-4 mb-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" x-model="searchQuery" @input.debounce.300ms="filterProducts()" placeholder="Search product by name or SKU..." class="pl-10 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                </div>
                            </div>
                            <div class="w-48">
                                <select x-model="selectedCategory" @change="filterProducts()" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">All Categories</option>
                                    @foreach($products->pluck('category.name', 'category.id')->unique() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 max-h-[600px] overflow-y-auto p-2">
                            <template x-for="product in filteredProducts" :key="product.id">
                                <div @click="addToCart(product)" class="cursor-pointer border rounded-lg p-3 hover:shadow-md hover:border-indigo-500 transition bg-white flex flex-col items-center text-center">
                                    <div class="w-full h-24 bg-gray-100 rounded mb-2 flex items-center justify-center">
                                        <template x-if="product.primary_image">
                                            <img :src="'/storage/' + product.primary_image" class="h-full object-contain">
                                        </template>
                                        <template x-if="!product.primary_image">
                                            <i class="fas fa-box text-3xl text-gray-300"></i>
                                        </template>
                                    </div>
                                    <div class="text-sm font-bold truncate w-full" x-text="product.name"></div>
                                    <div class="text-xs text-gray-500" x-text="'SKU: ' + product.sku"></div>
                                    <div class="text-indigo-600 font-bold mt-1" x-text="formatCurrency(product.price)"></div>
                                    <div class="text-[10px] mt-1 px-2 rounded-full" :class="product.qty > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" x-text="product.qty + ' in stock'"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Cart & Checkout Area -->
                <div class="lg:w-1/3">
                    <form action="{{ route('sales.store') }}" method="POST">
                        @csrf
                        <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col h-[750px]">
                            <div class="p-4 bg-gray-50 border-b">
                                <div class="mb-4">
                                    <x-input-label for="customer_id" :value="__('Customer')" />
                                    <select name="customer_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Walk-in Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="store_id" value="{{ $stores->first()?->id }}">
                                <input type="hidden" name="date" value="{{ date('Y-m-d H:i:s') }}">
                            </div>

                            <div class="flex-1 overflow-y-auto p-4">
                                <h3 class="font-bold mb-4 border-b pb-2">Shopping Cart</h3>
                                <template x-if="cart.length === 0">
                                    <div class="text-center py-10 text-gray-400 italic">Cart is empty</div>
                                </template>
                                <div class="space-y-4">
                                    <template x-for="(item, index) in cart" :key="index">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1">
                                                <div class="text-sm font-bold" x-text="item.name"></div>
                                                <div class="text-xs text-gray-500" x-text="formatCurrency(item.unit_price)"></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">-</button>
                                                <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" class="w-12 text-center text-sm border-none focus:ring-0 p-0">
                                                <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">+</button>
                                            </div>
                                            <div class="text-sm font-bold w-20 text-right" x-text="formatCurrency(item.quantity * item.unit_price)"></div>
                                            <button type="button" @click="removeFromCart(index)" class="text-red-500 ml-2">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                                            <input type="hidden" :name="'items['+index+'][unit_price]'" :value="item.unit_price">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 border-t space-y-3">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal:</span>
                                    <span x-text="formatCurrency(subtotal())"></span>
                                </div>
                                <div class="flex justify-between items-center gap-4">
                                    <span class="text-gray-600">Discount:</span>
                                    <input type="number" name="discount" x-model.number="discount" class="w-24 text-right border-gray-300 rounded text-sm p-1">
                                </div>
                                <div class="flex justify-between text-xl font-bold border-t pt-2">
                                    <span>Total Payable:</span>
                                    <span class="text-indigo-600" x-text="formatCurrency(grandTotal())"></span>
                                </div>
                                
                                <div class="pt-4 space-y-3">
                                    <div>
                                        <x-input-label :value="__('Payment Method')" />
                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                            <label class="border rounded p-2 flex items-center justify-center cursor-pointer hover:bg-indigo-50" :class="paymentMethod === 'cash' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white'">
                                                <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="hidden">
                                                <i class="fas fa-money-bill-wave mr-2"></i> Cash
                                            </label>
                                            <label class="border rounded p-2 flex items-center justify-center cursor-pointer hover:bg-indigo-50" :class="paymentMethod === 'card' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white'">
                                                <input type="radio" name="payment_method" value="card" x-model="paymentMethod" class="hidden">
                                                <i class="fas fa-credit-card mr-2"></i> Card
                                            </label>
                                        </div>
                                    </div>
                                    <div>
                                        <x-input-label :value="__('Amount Paid')" />
                                        <input type="number" step="0.01" name="paid_amount" x-model.number="paidAmount" class="block w-full text-2xl font-bold text-right border-gray-300 rounded-lg p-3 bg-indigo-50 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="flex justify-between items-center text-sm font-medium">
                                        <span class="text-gray-500">Change:</span>
                                        <span :class="paidAmount - grandTotal() >= 0 ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(Math.max(0, paidAmount - grandTotal()))"></span>
                                    </div>
                                    <button type="submit" :disabled="cart.length === 0" class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white font-bold py-4 rounded-lg text-xl shadow-lg transition">
                                        COMPLETE SALE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                products: JSON.parse(document.getElementById('pos-container').dataset.products || '[]'),
                searchQuery: '',
                selectedCategory: '',
                filteredProducts: [],
                cart: [],
                discount: 0,
                paymentMethod: 'cash',
                paidAmount: 0,

                init() {
                    this.filteredProducts = this.products;
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
                    if (product.qty <= 0) {
                        alert('Product out of stock!');
                        return;
                    }
                    
                    const existingItem = this.cart.find(item => item.product_id === product.id);
                    if (existingItem) {
                        if (existingItem.quantity < product.qty) {
                            existingItem.quantity++;
                        } else {
                            alert('Cannot exceed available stock!');
                        }
                    } else {
                        this.cart.push({
                            product_id: product.id,
                            name: product.name,
                            unit_price: product.price,
                            quantity: 1,
                            max_qty: product.qty
                        });
                    }
                    this.updatePaidAmount();
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.updatePaidAmount();
                },

                updateQty(index, delta) {
                    const item = this.cart[index];
                    const newQty = item.quantity + delta;
                    if (newQty > 0 && newQty <= item.max_qty) {
                        item.quantity = newQty;
                    } else if (newQty > item.max_qty) {
                        alert('Cannot exceed available stock!');
                    }
                    this.updatePaidAmount();
                },

                subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                },

                grandTotal() {
                    return Math.max(0, this.subtotal() - this.discount);
                },

                updatePaidAmount() {
                    this.paidAmount = this.grandTotal();
                },

                formatCurrency(amount) {
                    return 'Rs. ' + amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
        }
    </script>
</x-app-layout>
