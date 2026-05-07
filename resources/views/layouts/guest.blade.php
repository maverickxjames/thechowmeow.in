<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PetWear') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl shadow-gray-200/50 sm:rounded-2xl border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-violet-500 to-fuchsia-500"></div>
                <div class="mb-8 text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                        @if(config('app.logo'))
                            <img src="{{ Storage::url(config('app.logo')) }}" alt="Logo" class="w-10 h-10 object-contain">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shrink-0">
                                <svg class="w-5.5 h-5.5 text-white" viewBox="0 0 100 100" fill="currentColor">
                                    <ellipse cx="50" cy="68" rx="22" ry="18"/>
                                    <ellipse cx="28" cy="44" rx="10" ry="12" transform="rotate(-15 28 44)"/>
                                    <ellipse cx="72" cy="44" rx="10" ry="12" transform="rotate(15 72 44)"/>
                                    <ellipse cx="38" cy="28" rx="9" ry="11" transform="rotate(-10 38 28)"/>
                                    <ellipse cx="62" cy="28" rx="9" ry="11" transform="rotate(10 62 28)"/>
                                </svg>
                            </div>
                        @endif
                        <span class="text-xl font-bold text-gray-900 tracking-tight">{{ config('app.name', 'PetWear') }}</span>
                    </a>
                </div>

                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'PetWear') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
