@extends('layouts.app')

@section('header')
Crear Nuevo Usuario
@endsection

@section('content')
<div class="container mx-auto py-6 max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Crear Nuevo Usuario</h1>
    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded shadow p-6">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
            <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('name') }}" required>
            @error('name')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('email') }}" required>
            @error('email')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña:</label>
            <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            @error('password')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-6">
            <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Rol:</label>
            <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            @error('role')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancelar</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Crear Usuario</button>
        </div>
    </form>
</div>
@endsection
