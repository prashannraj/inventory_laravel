<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Invoice Template') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('invoice-templates.update', $invoiceTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Template Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $invoiceTemplate->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="layout" :value="__('Layout')" />
                                <select id="layout" name="layout" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="default" {{ $invoiceTemplate->layout === 'default' ? 'selected' : '' }}>Default</option>
                                    <option value="classic" {{ $invoiceTemplate->layout === 'classic' ? 'selected' : '' }}>Classic</option>
                                    <option value="modern" {{ $invoiceTemplate->layout === 'modern' ? 'selected' : '' }}>Modern</option>
                                    <option value="thermal" {{ $invoiceTemplate->layout === 'thermal' ? 'selected' : '' }}>Thermal (80mm)</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('layout')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="header_text" :value="__('Header Text')" />
                                <textarea id="header_text" name="header_text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('header_text', $invoiceTemplate->header_text) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('header_text')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="footer_text" :value="__('Footer Text')" />
                                <textarea id="footer_text" name="footer_text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('footer_text', $invoiceTemplate->footer_text) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('footer_text')" />
                            </div>

                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <input id="show_logo" name="show_logo" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $invoiceTemplate->show_logo ? 'checked' : '' }} />
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Show Logo') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <input id="is_default" name="is_default" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ $invoiceTemplate->is_default ? 'checked' : '' }} />
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Set as Default') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button onclick="history.back()" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Template') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
