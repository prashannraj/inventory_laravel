<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Invoice Template') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('invoice-templates.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Template Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="layout" :value="__('Layout Type')" />
                                <select id="layout" name="layout" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="default">Default</option>
                                    <option value="classic">Classic</option>
                                    <option value="modern">Modern</option>
                                    <option value="thermal">Thermal (80mm)</option>
                                </select>
                                <x-input-error :messages="$errors->get('layout')" class="mt-2" />
                            </div>

                            <div class="mb-4 md:col-span-2">
                                <x-input-label for="header_text" :value="__('Header Text (Appears at top)')" />
                                <textarea id="header_text" name="header_text" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('header_text') }}</textarea>
                                <x-input-error :messages="$errors->get('header_text')" class="mt-2" />
                            </div>

                            <div class="mb-4 md:col-span-2">
                                <x-input-label for="footer_text" :value="__('Footer Text (Appears at bottom)')" />
                                <textarea id="footer_text" name="footer_text" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('footer_text') }}</textarea>
                                <x-input-error :messages="$errors->get('footer_text')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="show_logo" value="0">
                                    <input type="checkbox" name="show_logo" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Show Company Logo') }}</span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="is_default" value="0">
                                    <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Set as Default Template') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('invoice-templates.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md mr-2">Cancel</a>
                            <x-primary-button>
                                {{ __('Save Template') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
