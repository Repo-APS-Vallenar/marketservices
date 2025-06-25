@extends('layouts.app')

@section('header')
    Dashboard Proveedor
@endsection

@section('content')
<div class="py-6 px-4">
    <h1 class="text-2xl font-bold mb-6">¡Bienvenido, proveedor!</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-indigo-100 rounded-lg p-6 shadow flex flex-col items-center">
            <span class="text-3xl font-bold text-indigo-700">{{ $pendingCount ?? 0 }}</span>
            <span class="text-gray-700 mt-2">Reservas pendientes</span>
        </div>
        <div class="bg-green-100 rounded-lg p-6 shadow flex flex-col items-center">
            <span class="text-3xl font-bold text-green-700">{{ $upcomingCount ?? 0 }}</span>
            <span class="text-gray-700 mt-2">Próximas reservas</span>
        </div>
        <div class="bg-purple-100 rounded-lg p-6 shadow flex flex-col items-center">
            <span class="text-3xl font-bold text-purple-700">{{ $completedCount ?? 0 }}</span>
            <span class="text-gray-700 mt-2">Reservas completadas</span>
        </div>
    </div>
    <div class="bg-white shadow rounded mb-8">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">Próximas reservas</h2>
            <a href="{{ route('bookings.index') }}" class="text-indigo-600 hover:underline">Ver todas</a>
        </div>
        <div class="w-full overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($upcomingBookings ?? [] as $booking)
                    <tr>
                        <td class="px-6 py-4">{{ $booking->customer->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $booking->service->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-6 py-4">{{ ucfirst($booking->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay próximas reservas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection 