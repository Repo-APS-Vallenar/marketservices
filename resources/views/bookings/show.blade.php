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
    ];
@endphp

<div class="container mx-auto py-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Reserva #{{ $booking->id }}</h1>
    <div class="bg-white rounded shadow p-6 mb-4">
        <div class="mb-2"><strong>Servicio:</strong> {{ $booking->service->name ?? '-' }}</div>
        <div class="mb-2"><strong>Proveedor:</strong> {{ $booking->service->provider->name ?? '-' }}</div>
        <div class="mb-2"><strong>Fecha y hora:</strong> {{ $booking->scheduled_at ? $booking->scheduled_at->format('d/m/Y H:i') : '-' }}</div>
        <div class="mb-2"><strong>Notas:</strong> {{ $booking->notes ?? '-' }}</div>
        <div class="mb-2"><strong>Estado:</strong> {{ $estados[$booking->status] ?? ucfirst($booking->status) }}</div>
    </div>
    @if($booking->status !== 'cancelada')
    <form method="POST" action="{{ route('bookings.cancel', $booking) }}">
        @csrf
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Cancelar reserva</button>
    </form>
    @else
    <div class="text-red-600 font-semibold">Reserva cancelada</div>
    @endif
    <div class="mt-4">
        <a href="{{ route('bookings.index') }}" class="text-indigo-600 hover:underline">Volver a reservas</a>
    </div>
</div>
@endsection