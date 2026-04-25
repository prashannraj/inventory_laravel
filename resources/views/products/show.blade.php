<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Product Details') }}: {{ $product->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Edit Product
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Product Images -->
                        <div class="space-y-4">
                            <div class="border rounded-lg overflow-hidden bg-gray-100">
                                @php $primaryImage = $product->images->where('is_primary', true)->first() ?: $product->images->first(); @endphp
                                @if($primaryImage)
                                    <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-auto object-cover">
                                @else
                                    <div class="flex items-center justify-center h-64 text-gray-400">
                                        <i class="fas fa-box fa-5x"></i>
                                    </div>
                                @endif
                            </div>
                            
                            @if($product->images->count() > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($product->images as $image)
                                        <div class="border rounded cursor-pointer overflow-hidden hover:opacity-75">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" class="h-16 w-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-gray-500">{{ $product->category?->name }} | {{ $product->brand?->name }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4 border-t pt-4">
                                <div>
                                    <p class="text-sm text-gray-500">SKU</p>
                                    <p class="font-medium">{{ $product->sku }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Barcode</p>
                                    <p class="font-medium">{{ $product->barcode }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Unit</p>
                                    <p class="font-medium">{{ $product->unit?->name }} ({{ $product->unit?->short_name }})</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 border-t pt-4">
                                <div>
                                    <p class="text-sm text-gray-500">Selling Price</p>
                                    <p class="text-xl font-bold text-green-600">Rs. {{ number_format($product->price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Buying Price</p>
                                    <p class="text-xl font-bold text-gray-700">Rs. {{ number_format($product->buying_price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Current Stock</p>
                                    <p class="text-xl font-bold {{ $product->qty <= $product->alert_quantity ? 'text-red-600' : 'text-blue-600' }}">
                                        {{ $product->qty }} {{ $product->unit?->short_name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Tax Rate</p>
                                    <p class="font-medium">{{ $product->taxRate ? $product->taxRate->name . ' (' . $product->taxRate->rate . '%)' : 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <p class="text-sm text-gray-500 mb-2">Description</p>
                                <div class="text-gray-700 whitespace-pre-line">
                                    {{ $product->description ?: 'No description provided.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Movements Table -->
                    <div class="mt-12">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Stock History (Last 10)</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($product->stockMovements()->latest()->take(10)->get() as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ in_array($movement->type, ['purchase', 'adjustment_in', 'return_in']) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $movement->reference_type }} #{{ $movement->reference_id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->notes }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No stock movements recorded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
