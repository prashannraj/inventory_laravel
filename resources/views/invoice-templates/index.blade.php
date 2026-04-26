<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice Templates') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-medium">Manage Templates</h3>
                        <a href="{{ route('invoice-templates.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Template
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($templates as $template)
                        <div class="border rounded-lg p-4 {{ $template->is_default ? 'border-blue-500 bg-blue-50' : '' }}">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-lg">{{ $template->name }}</h4>
                                @if($template->is_default)
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">Default</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mb-4">Layout: <span class="capitalize font-medium">{{ $template->layout }}</span></p>
                            
                            <div class="flex gap-2">
                                <a href="{{ route('invoice-templates.edit', $template) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold">Edit</a>
                                <form action="{{ route('invoice-templates.destroy', $template) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-bold" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($templates->isEmpty())
                    <div class="text-center py-12 text-gray-500 italic">
                        No templates found. Create one to customize your receipts.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
