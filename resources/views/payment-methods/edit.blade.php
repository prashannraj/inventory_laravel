<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Payment Method') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('payment-methods.update', $paymentMethod) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Method Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $paymentMethod->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="offline" {{ $paymentMethod->type === 'offline' ? 'selected' : '' }}>Offline (Cash, Bank Transfer, etc.)</option>
                                    <option value="online" {{ $paymentMethod->type === 'online' ? 'selected' : '' }}>Online (Payment Gateway)</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type')" />
                            </div>

                            <div>
                                <x-input-label for="gateway" :value="__('Gateway (Optional)')" />
                                <select id="gateway" name="gateway" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Select Gateway --</option>
                                    <option value="esewa" {{ $paymentMethod->gateway === 'esewa' ? 'selected' : '' }}>eSewa</option>
                                    <option value="khalti" {{ $paymentMethod->gateway === 'khalti' ? 'selected' : '' }}>Khalti</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Select a payment gateway if applicable.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('gateway')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="details" :value="__('Configuration Details (Optional)')" />
                                <textarea id="details" name="details" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="5" placeholder='{
  "merchant_code": "EPAYTEST",
  "secret_key": "8gBm/:&EnhH.1/q",
  "payment_url": "https://rc-epay.esewa.com.np/api/epay/main/v2/form",
  "success_url": "http://localhost:8000/esewa/success"
}'>{{ old('details', is_array($paymentMethod->details) ? json_encode($paymentMethod->details, JSON_PRETTY_PRINT) : $paymentMethod->details) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">For online gateways, add API keys or merchant IDs here in JSON format. Example above for eSewa.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('details')" />
                            </div>

                            <div class="flex items-center">
                                <input type="hidden" name="active" value="0" />
                                <input id="active" name="active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $paymentMethod->active ? 'checked' : '' }} />
                                <span class="ml-2 text-sm text-gray-600">{{ __('Active') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button onclick="history.back()" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Payment Method') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
