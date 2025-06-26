@extends('layouts.app')

@section('header')
Gestionar Usuarios
@endsection

@section('content')
<div class="container mx-auto py-6"
    x-data="{
        createUserModalOpen: false, // Controla el modal de creación de usuario
        editUserModalOpen: false,   // Controla el modal de edición
        deleteUserModalOpen: false, // Controla el modal de eliminación
        currentUser: {}, // Almacena el usuario seleccionado para editar/eliminar

        // Datos para el formulario de creación de usuario
        newUserName: '{{ old('name', '') }}',
        newUserEmail: '{{ old('email', '') }}',
        newUserPassword: '',
        newUserPasswordConfirmation: '',
        newUserRole: '{{ old('role', 'customer') }}', // Default role

        // Función para abrir el modal de creación y restablecer el formulario
        openCreateUserModal() {
            this.newUserName = '';
            this.newUserEmail = '';
            this.newUserPassword = '';
            this.newUserPasswordConfirmation = '';
            this.newUserRole = 'customer'; // Default role
            this.createUserModalOpen = true;
        },

        // Función para abrir el modal de edición y cargar los datos
        openEditUserModal(user) {
            this.currentUser = { ...user }; // Copiar el usuario para evitar mutar el original
            // Asegurarse de que el campo de contraseña no se cargue con el hash
            this.currentUser.password = '';
            this.currentUser.password_confirmation = '';
            this.editUserModalOpen = true;
        },

        // Función para abrir el modal de eliminación y cargar los datos
        openDeleteUserModal(user) {
            this.currentUser = { ...user };
            this.deleteUserModalOpen = true;
        }
    }"
    @keydown.escape="createUserModalOpen = false; editUserModalOpen = false; deleteUserModalOpen = false;">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Gestión de Usuarios</h1>
        {{-- Botón que abre el modal de creación de usuario --}}
        <button @click="openCreateUserModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-md inline-flex items-center gap-2 hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Usuario
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any() && (request()->routeIs('admin.users.store') || (request()->routeIs('admin.users.update') && old('user_id') == ($currentUser['id'] ?? null))))
        {{-- Muestra errores de validación si provienen de un intento de guardado o actualización --}}
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">
        <x-responsive-table>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 rounded-tl-md">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleTranslations = [
                                        'customer' => 'Cliente',
                                        'service_provider' => 'Proveedor de servicio',
                                        'admin' => 'Administrador',
                                    ];
                                @endphp
                                {{ $roleTranslations[$user->role] ?? ucfirst($user->role) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                {{-- Botón Editar que abre el modal de edición --}}
                                <button @click="openEditUserModal({{ $user }})" class="text-blue-600 hover:underline focus:outline-none">Editar</button>
                                {{-- Botón Eliminar que abre el modal de confirmación --}}
                                <button @click="openDeleteUserModal({{ $user }})" class="text-red-600 hover:underline focus:outline-none">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-responsive-table>
    </div>

    <!-- Modal de CREACIÓN de Usuario -->
    <div x-show="createUserModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="createUserModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:text-left w-full">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Crear Nuevo Usuario</h3>
                                <button @click="createUserModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="new-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                                    <input type="text" id="new-name" name="name"
                                           x-model="newUserName"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('name')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="new-email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                                    <input type="email" id="new-email" name="email"
                                           x-model="newUserEmail"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('email')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="new-password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña:</label>
                                    <input type="password" id="new-password" name="password"
                                           x-model="newUserPassword"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('password')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label for="new-password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña:</label>
                                    <input type="password" id="new-password_confirmation" name="password_confirmation"
                                           x-model="newUserPasswordConfirmation"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                </div>

                                <div>
                                    <label for="new-role" class="block text-sm font-medium text-gray-700 mb-1">Rol:</label>
                                    <select id="new-role" name="role"
                                            x-model="newUserRole"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                            required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}">
                                                {{ $role === 'customer' ? 'Cliente' : ($role === 'service_provider' ? 'Proveedor de servicio' : 'Administrador') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div class="mt-6 flex justify-center gap-3">
                                    <button type="button" @click="createUserModalOpen = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Crear Usuario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de EDICIÓN de Usuario -->
    <div x-show="editUserModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="editUserModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:text-left w-full">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4" x-text="'Editar Usuario: ' + currentUser.name"></h3>
                                <button @click="editUserModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form x-bind:action="'{{ route('admin.users.update', ':user_id') }}'.replace(':user_id', currentUser.id)" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')

                                {{-- Campo oculto para user_id en caso de errores de validación, útil si se usa old('user_id') --}}
                                <input type="hidden" name="user_id" :value="currentUser.id"> 

                                <div>
                                    <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                                    <input type="text" id="edit-name" name="name"
                                           x-model="currentUser.name"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('name')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                                    <input type="email" id="edit-email" name="email"
                                           x-model="currentUser.email"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('email')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-1">Rol:</label>
                                    <select id="edit-role" name="role"
                                            x-model="currentUser.role"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                            required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}">
                                                {{ $role === 'customer' ? 'Cliente' : ($role === 'service_provider' ? 'Proveedor de servicio' : 'Administrador') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <h2 class="text-xl font-bold mt-6 mb-4">Cambiar Contraseña (Opcional)</h2>
                                <div>
                                    <label for="edit-password" class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña:</label>
                                    <input type="password" id="edit-password" name="password"
                                           x-model="currentUser.password"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200">
                                    @error('password')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label for="edit-password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña:</label>
                                    <input type="password" id="edit-password_confirmation" name="password_confirmation"
                                           x-model="currentUser.password_confirmation"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200">
                                </div>

                                <div class="mt-6 flex justify-center gap-3">
                                    <button type="button" @click="editUserModalOpen = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Actualizar Usuario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de CONFIRMACIÓN de Eliminación de Usuario -->
    <div x-show="deleteUserModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="deleteUserModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Confirmar Eliminación
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que quieres eliminar al usuario <span class="font-semibold" x-text="currentUser.name"></span>?
                                    Esta acción no se puede deshacer.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form x-bind:action="'{{ route('admin.users.destroy', ':user_id') }}'.replace(':user_id', currentUser.id)" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                    </form>
                    <button type="button" @click="deleteUserModalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
