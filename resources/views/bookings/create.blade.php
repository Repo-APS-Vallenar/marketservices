@extends('layouts.app')

@section('header')
    Crear reserva
@endsection

@section('content')
<div class="container mx-auto py-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Crear reserva</h1>
    <form method="POST" action="{{ route('bookings.store') }}" class="bg-white rounded shadow p-6">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Servicio</label>
            @php
                $serviceId = old('service_id', request('service_id'));
                $service = $serviceId ? App\Models\Service::find($serviceId) : null;
            @endphp
            @if($service)
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <div class="px-3 py-2 border rounded bg-gray-100">{{ $service->name }}</div>
            @else
                <select name="service_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecciona un servicio</option>
                    @foreach(App\Models\Service::all() as $serviceOption)
                        <option value="{{ $serviceOption->id }}" {{ (old('service_id', request('service_id')) == $serviceOption->id) ? 'selected' : '' }}>{{ $serviceOption->name }}</option>
                    @endforeach
                </select>
            @endif
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Fecha y hora</label>
            <input type="datetime-local" name="scheduled_at" class="w-full border rounded px-3 py-2" min="{{ now()->format('Y-m-d\TH:i') }}" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Notas adicionales</label>
            <textarea name="notes" class="w-full border rounded px-3 py-2" rows="3"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Confirmar reserva</button>
        </div>
    </form>
</div>
@endsection