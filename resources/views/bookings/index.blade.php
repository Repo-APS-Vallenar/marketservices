@extends('layouts.app')

@section('header')
Reservas
@endsection

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Reservas</h1>

    @if(session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">
        <x-responsive-table>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50 rounded-tl-md">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Servicio</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Estado</th>
                        <th class="px-6 py-3 bg-gray-50 rounded-tr-md">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->service->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->scheduled_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $statusTranslations = [
                            'pending' => 'Pendiente',
                            'confirmed' => 'Confirmada',
                            'cancelled' => 'Cancelada',
                            'completed' => 'Completada',
                            'rescheduled' => 'Reprogramada', // Agrega más estados si los tienes
                            ];
                            @endphp
                            {{ $statusTranslations[$booking->status] ?? ucfirst($booking->status) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Ver</a>
                            @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'service_provider' && $booking->service->service_provider_id === auth()->user()->serviceProvider->id))
                            <a href="{{ route('bookings.edit', $booking->id) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                            @endif
                            <a href="{{ route('messages.create', ['user_id' => $booking->service->serviceProvider->user->id ?? '']) }}" class="text-blue-600 hover:text-blue-900">Contactar</a>
                            {{-- Aquí puedes añadir botones adicionales como Cancelar --}}
                            {{-- Ejemplo de botón Cancelar (requiere controlador y lógica): --}}
                            {{-- <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="inline ml-4">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-red-600 hover:text-red-900"
                                onclick="return confirm('¿Estás seguro de que quieres cancelar esta reserva?');">
                                Cancelar
                            </button>
                            </form> --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay reservas para mostrar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-responsive-table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection