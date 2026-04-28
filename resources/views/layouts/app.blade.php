<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $siteSettings['company_name'] ?? config('app.name', 'Inventory') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

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
            <div class="flex-1 sm:pl-64 pt-16 flex flex-col transition-all duration-300">
                <!-- POS पेज हो भने py-0 px-0 बनाउने, नत्र साविककै प्याडिङ राख्ने -->
                <main class="flex-1 {{ request()->is('pos*') ? 'p-0' : 'py-4 xs:py-6 sm:py-8 px-2 xs:px-3 sm:px-4 lg:px-6 xl:px-8' }}">
                    @if (isset($header) && !request()->is('pos*'))
                        <header class="mb-4 xs:mb-6 sm:mb-8">
                            <div class="max-w-7xl mx-auto">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <div class="{{ request()->is('pos*') ? 'max-w-full' : 'max-w-7xl mx-auto' }}">
                        @if(session('success'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-3 xs:mb-4 sm:mb-6 p-2 xs:p-3 sm:p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-r-lg shadow-sm flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-1 xs:mr-2 sm:mr-3 text-xs xs:text-sm"></i>
                                    <span class="text-xs xs:text-sm font-bold">{{ session('success') }}</span>
                                </div>
                                <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                                    <i class="fas fa-times text-xs xs:text-sm"></i>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" class="mb-3 xs:mb-4 sm:mb-6 p-2 xs:p-3 sm:p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-lg shadow-sm flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1 xs:mr-2 sm:mr-3 text-xs xs:text-sm"></i>
                                    <span class="text-xs xs:text-sm font-bold">{{ session('error') }}</span>
                                </div>
                                <button @click="show = false" class="text-rose-400 hover:text-rose-600">
                                    <i class="fas fa-times text-xs xs:text-sm"></i>
                                </button>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>

                <!-- Footer - POS पेज बाहेक अरुमा मात्र देखाउने -->
                @if(!request()->is('pos*'))
                <footer class="bg-white border-t border-gray-100 py-3 xs:py-4 sm:py-6 px-2 xs:px-3 sm:px-4 lg:px-6 xl:px-8 mt-auto">
                    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-2 xs:gap-3 sm:gap-4">
                        <div class="text-xs xs:text-sm text-gray-500 font-medium text-center md:text-left">
                            &copy; {{ date('Y') }} {{ $siteSettings['company_name'] ?? 'Inventory Management System' }}. All rights reserved.
                        </div>
                        <div class="flex items-center gap-2 xs:gap-3 sm:gap-6 flex-wrap justify-center">
                            <a href="{{ route('documentation') }}" class="text-xs font-bold text-gray-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">Docs</a>
                            <a href="https://appantech.com.np" target="_blank" class="text-xs font-bold text-gray-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">Support</a>
                            <div class="h-4 w-px bg-gray-200 hidden sm:block"></div>
                            <div class="text-xs font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded">v2.1.0</div>
                        </div>
                    </div>
                </footer>
                @endif
            </div>
        </div>
        
        @stack('scripts')
    </body>
</html>
