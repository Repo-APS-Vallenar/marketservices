@extends('layouts.app')

@section('header')
    Reservas
@endsection

@section('content')
<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-4">Reservas</h1>
    <x-responsive-table>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr>
                        <td class="px-6 py-4">{{ $booking->customer->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $booking->service->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-6 py-4">{{ ucfirst($booking->status) }}</td>
                        <td class="px-6 py-4 flex flex-wrap gap-2">
                            <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:underline">Ver</a>
                            <a href="{{ route('messages.index', ['user' => $booking->customer->id]) }}" class="text-indigo-600 hover:underline">Contactar</a>
                            @if($booking->status === 'pendiente')
                                <form action="{{ route('bookings.accept', $booking) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:underline">Aceptar</button>
                                </form>
                                <form action="{{ route('bookings.reject', $booking) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:underline">Rechazar</button>
                                </form>
                            @elseif(in_array($booking->status, ['confirmada', 'próxima']))
                                <form action="{{ route('bookings.complete', $booking) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:underline">Completar</button>
                                </form>
                                <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:underline">Cancelar</button>
                                </form>
                                <a href="#" class="text-yellow-600 hover:underline">Reagendar</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay reservas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-responsive-table>
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection 