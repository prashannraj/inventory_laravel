<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-600 via-blue-700 to-indigo-900 relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
            
            <div class="z-10 w-full sm:max-w-md">
                <div class="flex flex-col items-center mb-8">
                    <a href="/" wire:navigate class="transition-transform hover:scale-105 duration-300">
                        <div class="bg-white p-4 rounded-2xl shadow-2xl">
                            <x-application-logo class="w-16 h-16 fill-current text-indigo-600" />
                        </div>
                    </a>
                    <h1 class="mt-6 text-3xl font-black text-white uppercase tracking-tighter">Inventory <span class="text-blue-300">Pro</span></h1>
                    <p class="text-blue-100/70 text-sm font-bold uppercase tracking-widest mt-1">Management System</p>
                </div>

                <div class="w-full px-8 py-10 bg-white/95 backdrop-blur-md shadow-[0_20px_50px_rgba(0,0,0,0.3)] overflow-hidden sm:rounded-3xl border border-white/20">
                    {{ $slot }}
                </div>

                <div class="mt-8 text-center">
                    <p class="text-blue-100/60 text-[10px] font-black uppercase tracking-[0.3em]">Developed By</p>
                    <a href="https://appantech.com.np" target="_blank" class="text-white font-black text-sm uppercase tracking-tighter hover:text-blue-300 transition-colors">
                        Appan Technology Pvt. Ltd.
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
