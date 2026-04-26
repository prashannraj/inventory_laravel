<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="phone" :value="__('Phone')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="tax_number" :value="__('Tax Number')" />
                                <x-text-input id="tax_number" class="block mt-1 w-full" type="text" name="tax_number" :value="old('tax_number')" />
                                <x-input-error :messages="$errors->get('tax_number')" class="mt-2" />
                            </div>
                            <div class="mb-4 md:col-span-2">
                                <x-input-label for="address" :value="__('Address')" />
                                <textarea id="address" name="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address') }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="opening_balance" :value="__('Opening Balance')" />
                                <x-text-input id="opening_balance" class="block mt-1 w-full" type="number" step="0.01" name="opening_balance" :value="old('opening_balance', 0)" />
                                <x-input-error :messages="$errors->get('opening_balance')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="credit_limit" :value="__('Credit Limit')" />
                                <x-text-input id="credit_limit" class="block mt-1 w-full" type="number" step="0.01" name="credit_limit" :value="old('credit_limit', 0)" />
                                <x-input-error :messages="$errors->get('credit_limit')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="loyalty_points" :value="__('Initial Loyalty Points')" />
                                <x-text-input id="loyalty_points" class="block mt-1 w-full" type="number" name="loyalty_points" :value="old('loyalty_points', 0)" />
                                <x-input-error :messages="$errors->get('loyalty_points')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <label class="inline-flex items-center mt-8">
                                    <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', true) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Active') }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save Customer') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
