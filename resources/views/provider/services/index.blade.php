@extends('layouts.app')

@section('header')
    Mis servicios
@endsection

<script>alert('VITE NO CARGA');</script>

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Mis servicios</h1>
    </div>
    <!-- Botón que abre el modal -->
    <label for="modal-toggle" style="cursor:pointer; background:#2563eb; color:white; padding:8px 16px; border-radius:6px; display:inline-flex; align-items:center; gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" style="height:20px; width:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Nuevo servicio
    </label>
    <input type="checkbox" id="modal-toggle" style="display:none;" />
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    <div class="bg-white shadow rounded">
        <x-responsive-table>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($services as $service)
                    <tr>
                        <td class="px-6 py-4">{{ $service->name }}</td>
                        <td class="px-6 py-4">{{ $service->category->name ?? '-' }}</td>
                        <td class="px-6 py-4">${{ number_format($service->price, 2) }} {{ $service->price_unit }}</td>
                        <td class="px-6 py-4">{{ $service->estimated_duration ? $service->estimated_duration . ' min' : '-' }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('services.edit', $service) }}" class="text-blue-600 hover:underline">Editar</a>
                            <form action="{{ route('services.destroy', $service) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este servicio?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No tienes servicios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </x-responsive-table>
    </div>
</div>
@endsection

<!-- Modal solo HTML+JS -->
<div id="modal" style="position:fixed; inset:0; z-index:50; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,0.5);">
    <div style="background:white; border-radius:12px; box-shadow:0 2px 16px #0002; padding:2rem; width:100%; max-width:600px; position:relative;">
        <label for="modal-toggle" style="position:absolute; top:12px; right:20px; font-size:2rem; color:#888; cursor:pointer;">&times;</label>
        <h2 style="font-size:1.25rem; font-weight:bold; margin-bottom:1rem;">Nuevo servicio</h2>
        <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block font-medium">Nombre</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name') }}" required>
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block font-medium">Categoría</label>
                <select name="category_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block font-medium">Descripción</label>
                <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
                @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block font-medium">Precio</label>
                    <input type="number" name="price" class="w-full border rounded px-3 py-2" value="{{ old('price') }}" min="0" step="0.01">
                    @error('price')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="w-1/2">
                    <label class="block font-medium">Unidad</label>
                    <input type="text" name="price_unit" class="w-full border rounded px-3 py-2" value="{{ old('price_unit') }}">
                    @error('price_unit')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>
            <div>
                <label class="block font-medium">Duración estimada (minutos)</label>
                <input type="number" name="estimated_duration" class="w-full border rounded px-3 py-2" value="{{ old('estimated_duration') }}" min="1">
                @error('estimated_duration')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="flex justify-end gap-2">
                <label for="modal-toggle" style="padding:8px 16px; background:#eee; border-radius:6px; cursor:pointer;">Cancelar</label>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById('modal-toggle').addEventListener('change', function() {
        document.getElementById('modal').style.display = this.checked ? 'flex' : 'none';
    });
</script> 