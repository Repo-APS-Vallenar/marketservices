@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 px-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado -->
            <div class="flex items-center justify-between border-b pb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $service->name }}</h1>
                    <div class="mt-1 flex items-center">
                        <span class="px-2 py-1 text-sm bg-indigo-100 text-indigo-800 rounded-full">
                            {{ $service->category->name }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-gray-900">${{ number_format($service->price, 2) }}</div>
                    <div class="text-sm text-gray-500">por {{ $service->price_unit }}</div>
                </div>
            </div>

            <!-- Información del proveedor -->
            <div class="mt-6 border-b pb-4">
                <h2 class="text-lg font-semibold mb-2">Proveedor del Servicio</h2>
                <div class="flex items-center">
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ $service->serviceProvider->user->name }}</div>
                        @if($service->serviceProvider->company_name)
                            <div class="text-sm text-gray-500">{{ $service->serviceProvider->company_name }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detalles del servicio -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold mb-2">Descripción del Servicio</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $service->description }}</p>
                
                <div class="mt-4 grid grid-cols-2 gap-4">
                    @if($service->estimated_duration)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm text-gray-500">Duración estimada</div>
                        <div class="text-lg font-medium">{{ $service->estimated_duration }} minutos</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Botón de reserva -->
            <div class="mt-8">
                @auth
                    @if(auth()->user()->role === 'customer' || auth()->user()->role === 'admin')
                    <a href="{{ route('bookings.create', ['service' => $service->id]) }}" 
                       class="block w-full text-center bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700">
                        Reservar este servicio
                    </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" 
                       class="block w-full text-center bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700">
                        Inicia sesión para reservar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection