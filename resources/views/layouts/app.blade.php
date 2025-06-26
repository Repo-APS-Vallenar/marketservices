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

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak] { display: none !important; }</style>
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

                <!-- Global Message Handling -->
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                            class="relative bg-green-100 text-green-800 px-4 py-3 rounded-md mb-4 flex items-center justify-between shadow-sm">
                            <span>{{ session('success') }}</span>
                            <button @click="show = false" class="text-green-800 hover:text-green-900 font-bold ml-4 p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                            class="relative bg-red-100 text-red-800 px-4 py-3 rounded-md mb-4 flex items-center justify-between shadow-sm">
                            <span>{{ session('error') }}</span>
                            <button @click="show = false" class="text-red-800 hover:text-red-900 font-bold ml-4 p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- Para errores de validación, que se mantienen por más tiempo --}}
                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
                            class="relative bg-orange-100 text-orange-800 px-4 py-3 rounded-md mb-4 flex items-start justify-between shadow-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button @click="show = false" class="text-orange-800 hover:text-orange-900 font-bold ml-4 p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>

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
