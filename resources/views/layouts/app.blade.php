<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $siteSettings['company_name'] ?? config('app.name', 'Inventory') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            <livewire:layout.navigation />

            <!-- Main Content Area -->
            <div class="flex-1 sm:pl-64 flex flex-col transition-all duration-300">
                <main class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
                    @if (isset($header))
                        <header class="mb-8">
                            <div class="max-w-7xl mx-auto">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <div class="max-w-7xl mx-auto">
                        @if(session('success'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg shadow-sm flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-3"></i>
                                    <span class="text-sm font-bold">{{ session('success') }}</span>
                                </div>
                                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-lg shadow-sm flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle mr-3"></i>
                                    <span class="text-sm font-bold">{{ session('error') }}</span>
                                </div>
                                <button @click="show = false" class="text-rose-400 hover:text-rose-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>

                <!-- Footer -->
                <footer class="bg-white border-t border-gray-100 py-6 px-4 sm:px-6 lg:px-8 mt-auto">
                    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-500 font-medium">
                            &copy; {{ date('Y') }} {{ $siteSettings['company_name'] ?? 'Inventory Management System' }}. All rights reserved.
                        </div>
                        <div class="flex items-center gap-6">
                            <a href="#" class="text-xs font-bold text-gray-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">Documentation</a>
                            <a href="#" class="text-xs font-bold text-gray-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">Support</a>
                            <div class="h-4 w-px bg-gray-200"></div>
                            <div class="text-xs font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded">v2.1.0</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html>
