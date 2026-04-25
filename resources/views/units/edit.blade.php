<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Unit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('units.update', $unit) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="max-w-xl">
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Unit Name (e.g. Kilogram)')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $unit->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <x-input-label for="short_name" :value="__('Short Name (e.g. kg)')" />
                                <x-text-input id="short_name" class="block mt-1 w-full" type="text" name="short_name" :value="old('short_name', $unit->short_name)" required />
                                <x-input-error :messages="$errors->get('short_name')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', $unit->active) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Active') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('active')" class="mt-2" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('units.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                    Cancel
                                </a>
                                <x-primary-button>
                                    {{ __('Update Unit') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
