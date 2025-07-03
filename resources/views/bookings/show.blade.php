@extends('layouts.app')

@section('header')
    Detalle de reserva
@endsection

@section('content')
@php
    $estados = [
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmada',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada',
        'rescheduled' => 'Reprogramada', // Asegúrate de incluir este estado si lo usas
    ];
@endphp

<div class="container mx-auto py-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Reserva #{{ $booking->id }}</h1>
    <div class="bg-white rounded shadow p-6 mb-4">
        <div class="mb-2"><strong>Servicio:</strong> {{ $booking->service->name ?? '-' }}</div>
        {{-- Asegúrate de que la relación 'serviceProvider' y 'user' estén cargadas en el controlador show --}}
        <div class="mb-2"><strong>Proveedor:</strong> {{ $booking->service->serviceProvider->user->name ?? '-' }}</div>
        <div class="mb-2"><strong>Fecha y hora:</strong> {{ $booking->scheduled_at ? $booking->scheduled_at->format('d/m/Y H:i') : '-' }}</div>
        <div class="mb-2"><strong>Notas:</strong> {{ $booking->notes ?? '-' }}</div>
        <div class="mb-2"><strong>Estado:</strong> {{ $estados[$booking->status] ?? ucfirst($booking->status) }}</div>
    </div>

    {{-- Formulario para cancelar la reserva --}}
    {{-- Solo mostrar si el usuario es el cliente de la reserva o un admin, y si el estado no es 'cancelled' o 'completed' --}}
    @if(Auth::check() && (Auth::id() === $booking->customer_id || Auth::user()->role === 'admin') && $booking->status !== 'cancelled' && $booking->status !== 'completed')
        <form method="POST" action="{{ route('bookings.update', $booking) }}" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta reserva?');">
            @csrf
            @method('PUT') {{-- Usar el método PUT para actualizar --}}
            <input type="hidden" name="status" value="cancelled"> {{-- Campo oculto para cambiar el estado --}}
            
            {{-- También necesitas enviar los campos requeridos por la validación del update,
                 aunque no se muestren en el formulario, para evitar errores de validación.
                 Los precargamos con los valores actuales de la reserva. --}}
            <input type="hidden" name="service_id" value="{{ $booking->service_id }}">
            <input type="hidden" name="scheduled_at" value="{{ $booking->scheduled_at->format('Y-m-d H:i:s') }}">
            <input type="hidden" name="notes" value="{{ $booking->notes }}">

            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Cancelar reserva</button>
        </form>
    @elseif($booking->status === 'cancelled')
        <div class="text-red-600 font-semibold text-lg">Reserva cancelada</div>
    @endif

    <div class="mt-4">
        <a href="{{ route('bookings.index') }}" class="text-indigo-600 hover:underline">Volver a reservas</a>
    </div>
</div>
@endsection
