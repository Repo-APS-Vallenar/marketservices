@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Nuevo servicio</h1>
    <form action="{{ route('services.store') }}" method="POST" class="bg-white shadow rounded p-6 space-y-4">
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
            <a href="{{ route('services.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Guardar</button>
        </div>
    </form>
</div>
@endsection 