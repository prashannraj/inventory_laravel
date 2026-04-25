<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Unit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('units.store') }}" method="POST">
                        @csrf
                        <div class="max-w-xl">
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Unit Name (e.g. Kilogram)')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="short_name" :value="__('Short Name (e.g. kg)')" />
                                <x-text-input id="short_name" class="block mt-1 w-full" type="text" name="short_name" :value="old('short_name')" required />
                            </div>
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', true) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Active') }}</span>
                                </label>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    {{ __('Save Unit') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
