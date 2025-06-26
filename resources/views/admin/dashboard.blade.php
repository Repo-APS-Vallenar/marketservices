@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-6">Dashboard Administrador</h1>

    <!-- Estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Total Usuarios</h2>
                    <p class="text-lg font-semibold text-gray-800">{{ $stats['total_users'] }}</p>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <span class="text-green-600">{{ $stats['total_providers'] }}</span> proveedores
                <span class="mx-1">•</span>
                <span class="text-blue-600">{{ $stats['total_customers'] }}</span> clientes
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Total Servicios</h2>
                    <p class="text-lg font-semibold text-gray-800">{{ $stats['total_services'] }}</p>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                En {{ $stats['total_categories'] }} categorías diferentes
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Reservas Totales</h2>
                    <p class="text-lg font-semibold text-gray-800">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <span class="text-yellow-600">{{ $stats['pending_bookings'] }}</span> pendientes
                <span class="mx-1">•</span>
                <span class="text-green-600">{{ $stats['completed_bookings'] }}</span> completadas
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Proveedores Pendientes</h2>
                    <p class="text-lg font-semibold text-gray-800">{{ $unverified_providers->count() }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.providers.pending') }}" class="text-sm text-yellow-600 hover:text-yellow-700">Ver proveedores pendientes →</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Últimos usuarios registrados -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Últimos usuarios registrados</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ver todos →</a>
            </div>
            <div class="p-6">
                <div class="divide-y">
                    @foreach($latest_users as $user)
                        <div class="py-3 flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ 
                                $user->role === 'admin' ? 'bg-red-100 text-red-800' :
                                ($user->role === 'service_provider' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800')
                            }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Últimas reservas -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Últimas reservas</h2>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ver todas →</a>
            </div>
            <div class="p-6">
                <div class="divide-y">
                    @foreach($latest_bookings as $booking)
                        <div class="py-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium">{{ $booking->service->name }}</p>
                                    <p class="text-sm text-gray-600">Cliente: {{ $booking->customer->name }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ 
                                    $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    ($booking->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800')
                                }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                Proveedor: {{ $booking->serviceProvider->user->name }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection