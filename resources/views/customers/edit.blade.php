<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="name" :value="__('Customer Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $customer->name)" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $customer->email)" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="phone" :value="__('Phone')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $customer->phone)" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="tax_number" :value="__('Tax Number / VAT')" />
                                    <x-text-input id="tax_number" class="block mt-1 w-full" type="text" name="tax_number" :value="old('tax_number', $customer->tax_number)" />
                                    <x-input-error :messages="$errors->get('tax_number')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="opening_balance" :value="__('Opening Balance')" />
                                    <x-text-input id="opening_balance" class="block mt-1 w-full" type="number" step="0.01" name="opening_balance" :value="old('opening_balance', $customer->opening_balance)" />
                                    <x-input-error :messages="$errors->get('opening_balance')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="credit_limit" :value="__('Credit Limit')" />
                                    <x-text-input id="credit_limit" class="block mt-1 w-full" type="number" step="0.01" name="credit_limit" :value="old('credit_limit', $customer->credit_limit)" />
                                    <x-input-error :messages="$errors->get('credit_limit')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <x-input-label for="loyalty_points" :value="__('Loyalty Points')" />
                                    <x-text-input id="loyalty_points" class="block mt-1 w-full" type="number" name="loyalty_points" :value="old('loyalty_points', $customer->loyalty_points)" />
                                    <x-input-error :messages="$errors->get('loyalty_points')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="active" value="0">
                                        <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', $customer->active) ? 'checked' : '' }}>
                                        <span class="ms-2 text-sm text-gray-600">{{ __('Active') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('address', $customer->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-4 border-t pt-4">
                            <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Customer') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
