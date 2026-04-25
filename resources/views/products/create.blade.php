<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium border-b pb-2">Basic Information</h3>
                                
                                <div>
                                    <x-input-label for="name" :value="__('Product Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="sku" :value="__('SKU (Auto-generated if empty)')" />
                                        <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                                    </div>
                                    <div>
                                        <x-input-label for="barcode" :value="__('Barcode (Auto-generated if empty)')" />
                                        <x-text-input id="barcode" name="barcode" type="text" class="mt-1 block w-full" :value="old('barcode')" />
                                        <x-input-error class="mt-2" :messages="$errors->get('barcode')" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="category_id" :value="__('Category')" />
                                        <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                                    </div>
                                    <div>
                                        <x-input-label for="brand_id" :value="__('Brand')" />
                                        <select id="brand_id" name="brand_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('brand_id')" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="unit_id" :value="__('Unit')" />
                                        <select id="unit_id" name="unit_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Select Unit</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->short_name }})</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('unit_id')" />
                                    </div>
                                    <div>
                                        <x-input-label for="tax_rate_id" :value="__('Tax Rate')" />
                                        <select id="tax_rate_id" name="tax_rate_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">No Tax</option>
                                            @foreach($taxRates as $taxRate)
                                                <option value="{{ $taxRate->id }}" {{ old('tax_rate_id') == $taxRate->id ? 'selected' : '' }}>{{ $taxRate->name }} ({{ $taxRate->rate }}%)</option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('tax_rate_id')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                </div>
                            </div>

                            <!-- Pricing & Inventory -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium border-b pb-2">Pricing & Inventory</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="buying_price" :value="__('Buying Price')" />
                                        <x-text-input id="buying_price" name="buying_price" type="number" step="0.01" class="mt-1 block w-full" :value="old('buying_price', 0)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('buying_price')" />
                                    </div>
                                    <div>
                                        <x-input-label for="price" :value="__('Selling Price')" />
                                        <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price', 0)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="qty" :value="__('Initial Quantity')" />
                                        <x-text-input id="qty" name="qty" type="number" class="mt-1 block w-full" :value="old('qty', 0)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('qty')" />
                                    </div>
                                    <div>
                                        <x-input-label for="alert_quantity" :value="__('Alert Quantity')" />
                                        <x-text-input id="alert_quantity" name="alert_quantity" type="number" class="mt-1 block w-full" :value="old('alert_quantity', 10)" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('alert_quantity')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="store_id" :value="__('Store')" />
                                    <select id="store_id" name="store_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('store_id')" />
                                </div>

                                <div>
                                    <x-input-label for="images" :value="__('Product Images')" />
                                    <input id="images" name="images[]" type="file" class="mt-1 block w-full" multiple accept="image/*" />
                                    <p class="text-xs text-gray-500 mt-1">You can upload multiple images. First image will be primary.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('images.*')" />
                                </div>

                                <div class="flex items-center">
                                    <input id="active" name="active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', true) ? 'checked' : '' }}>
                                    <label for="active" class="ml-2 block text-sm text-gray-900">Active</label>
                                    <x-input-error class="mt-2" :messages="$errors->get('active')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 border-t pt-4">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Save Product') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
