<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Attribute') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('attributes.store') }}" method="POST">
                        @csrf
                        <div class="max-w-xl">
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Attribute Name (e.g. Color, Size)')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', true) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Active') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('active')" class="mt-2" />
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('attributes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                    Cancel
                                </a>
                                <x-primary-button>
                                    {{ __('Save Attribute') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
