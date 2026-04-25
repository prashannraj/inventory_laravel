<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="max-w-xl">
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Category Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $category->name)" required />
                            </div>
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" name="active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('active', $category->active) ? 'checked' : '' }}>
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Active') }}</span>
                                </label>
                            </div>
                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    {{ __('Update Category') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
