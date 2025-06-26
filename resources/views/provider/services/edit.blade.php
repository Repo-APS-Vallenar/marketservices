@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Editar servicio</h1>
    <form action="{{ route('services.update', $service) }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block font-medium">Nombre</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $service->name) }}" required>
            @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="service-category" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select id="service-category" name="category_id"
                x-model="serviceCategoryId"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                required>
                <option value="">Selecciona una categoría</option>
                @foreach($categories as $category) {{-- Asegúrate de que $categories venga filtrado del controlador --}}
                <option value="{{ $category->id }}">
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            @error('category_id')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block font-medium">Descripción</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $service->description) }}</textarea>
            @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex gap-4">
            <div class="w-1/2">
                <label class="block font-medium">Precio</label>
                <input type="number" name="price" class="w-full border rounded px-3 py-2" value="{{ old('price', $service->price) }}" min="0" step="0.01">
                @error('price')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="w-1/2">
                <label class="block font-medium">Unidad</label>
                <input type="text" name="price_unit" class="w-full border rounded px-3 py-2" value="{{ old('price_unit', $service->price_unit) }}">
                @error('price_unit')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
        </div>
        <div>
            <label class="block font-medium">Duración estimada (minutos)</label>
            <input type="number" name="estimated_duration" class="w-full border rounded px-3 py-2" value="{{ old('estimated_duration', $service->estimated_duration) }}" min="1">
            @error('estimated_duration')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('services.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Actualizar</button>
        </div>
    </form>
</div>
@endsection