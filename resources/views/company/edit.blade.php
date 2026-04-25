<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Company Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('company.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold border-b pb-2">Basic Information</h3>
                                <div>
                                    <x-input-label for="company_name" :value="__('Company Name')" />
                                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $settings['company_name'] ?? '')" required />
                                </div>
                                <div>
                                    <x-input-label for="company_email" :value="__('Company Email')" />
                                    <x-text-input id="company_email" name="company_email" type="email" class="mt-1 block w-full" :value="old('company_email', $settings['company_email'] ?? '')" required />
                                </div>
                                <div>
                                    <x-input-label for="company_phone" :value="__('Company Phone')" />
                                    <x-text-input id="company_phone" name="company_phone" type="text" class="mt-1 block w-full" :value="old('company_phone', $settings['company_phone'] ?? '')" required />
                                </div>
                                <div>
                                    <x-input-label for="company_address" :value="__('Company Address')" />
                                    <textarea id="company_address" name="company_address" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required>{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Localization & Invoicing -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold border-b pb-2">Localization & Invoicing</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="currency_symbol" :value="__('Currency Symbol')" />
                                        <x-text-input id="currency_symbol" name="currency_symbol" type="text" class="mt-1 block w-full" :value="old('currency_symbol', $settings['currency_symbol'] ?? '$')" required />
                                    </div>
                                    <div>
                                        <x-input-label for="currency_code" :value="__('Currency Code')" />
                                        <x-text-input id="currency_code" name="currency_code" type="text" class="mt-1 block w-full" :value="old('currency_code', $settings['currency_code'] ?? 'USD')" required />
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="tax_number" :value="__('Tax/VAT Number')" />
                                    <x-text-input id="tax_number" name="tax_number" type="text" class="mt-1 block w-full" :value="old('tax_number', $settings['tax_number'] ?? '')" />
                                </div>
                                <div>
                                    <x-input-label for="invoice_prefix" :value="__('Invoice Prefix')" />
                                    <x-text-input id="invoice_prefix" name="invoice_prefix" type="text" class="mt-1 block w-full" :value="old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-')" required />
                                </div>
                                <div>
                                    <x-input-label for="company_logo" :value="__('Company Logo')" />
                                    <input id="company_logo" name="company_logo" type="file" class="mt-1 block w-full" />
                                    @if(isset($settings['company_logo']))
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Logo" class="h-16 w-auto">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 flex justify-end">
                            <x-primary-button>
                                {{ __('Save All Settings') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
