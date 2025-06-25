<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100">
        <div class="flex">
            <x-sidebar />
            <div class="flex-1 min-h-screen w-full transition-all duration-200 ease-in-out">
                <header class="bg-white border-b border-indigo-100 shadow-sm h-16 flex items-center px-4 md:px-8 justify-between pl-12 md:pl-0">
                    <div class="flex items-center gap-2 ml-7">
                        <a href="{{ route('dashboard') }}" class="focus:outline-none">
                            <!-- Móvil: solo iniciales CMH -->
                            <span class="text-xl font-extrabold md:hidden pl-4">
                                <span class="text-indigo-700">C</span><span class="text-purple-500">M</span><span class="text-indigo-700">H</span>
                            </span>
                            <!-- Escritorio: logo completo -->
                            <span class="hidden md:inline">
                                <x-insignia class="h-8 w-8" />
                            </span>
                        </a>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('profile.edit') }}" class="text-indigo-700 font-semibold hover:underline focus:outline-none">
                            {{ Auth::user()->name }}
                        </a>
                    </div>
                </header>
                <main class="px-4 py-6">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
            </div>
        </div>
    </body>
</html>
