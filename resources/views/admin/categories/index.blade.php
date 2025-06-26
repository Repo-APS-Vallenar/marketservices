@extends('layouts.app')

@section('header')
Gestionar Categorías
@endsection

@section('content')
<div class="container mx-auto py-6"
    x-data="{
        createCategoryModalOpen: false, // Controla el modal de creación
        editCategoryModalOpen: false,   // Controla el modal de edición
        deleteCategoryModalOpen: false, // Controla el modal de eliminación
        currentCategory: {}, // Almacena la categoría seleccionada para editar/eliminar

        // Datos para el formulario de creación
        newCategoryName: '{{ old('name', '') }}',
        newCategoryDescription: '{{ old('description', '') }}',

        // Función para abrir el modal de creación y restablecer el formulario
        openCreateCategoryModal() {
            this.newCategoryName = '';
            this.newCategoryDescription = '';
            this.createCategoryModalOpen = true;
        },

        // Función para abrir el modal de edición y cargar los datos
        openEditCategoryModal(category) {
            this.currentCategory = { ...category }; // Copiar la categoría
            this.editCategoryModalOpen = true;
        },

        // Función para abrir el modal de eliminación y cargar los datos
        openDeleteCategoryModal(category) {
            this.currentCategory = { ...category };
            this.deleteCategoryModalOpen = true;
        }
    }"
    @keydown.escape="createCategoryModalOpen = false; editCategoryModalOpen = false; deleteCategoryModalOpen = false;">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Gestión de Categorías</h1>
        {{-- Botón que abre el modal de creación de categoría --}}
        <button @click="openCreateCategoryModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-md inline-flex items-center gap-2 hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nueva Categoría
        </button>
    </div>

    {{-- LOS MENSAJES DE SUCCESS/ERROR/VALIDACION AHORA SON MANEJADOS GLOBALMENTE POR layouts/app.blade.php --}}
    {{-- Por lo tanto, se han eliminado de aquí. --}}

    <div class="bg-white shadow rounded overflow-hidden">
        <x-responsive-table>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 rounded-tl-md">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                            <td class="px-6 py-4">{{ $category->description ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                                {{-- Botón Editar que abre el modal de edición --}}
                                <button @click="openEditCategoryModal({{ $category }})" class="text-blue-600 hover:underline focus:outline-none">Editar</button>
                                {{-- Botón Eliminar que abre el modal de confirmación --}}
                                <button @click="openDeleteCategoryModal({{ $category }})" class="text-red-600 hover:underline focus:outline-none">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay categorías registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-responsive-table>
    </div>

    <!-- Modal de CREACIÓN de Categoría -->
    <div x-show="createCategoryModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="createCategoryModalOpen = false"></div>
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Crear Nueva Categoría</h3>
                                <button @click="createCategoryModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="new-category-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Categoría:</label>
                                    <input type="text" id="new-category-name" name="name"
                                           x-model="newCategoryName"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    {{-- @error('name')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror --}}
                                </div>

                                <div>
                                    <label for="new-category-description" class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                                    <textarea id="new-category-description" name="description" rows="3"
                                              x-model="newCategoryDescription"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                              placeholder="Describe la categoría..."></textarea>
                                    {{-- @error('description')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror --}}
                                </div>

                                <div class="mt-6 flex justify-center gap-3">
                                    <button type="button" @click="createCategoryModalOpen = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Crear Categoría
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de EDICIÓN de Categoría -->
    <div x-show="editCategoryModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="editCategoryModalOpen = false"></div>
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4" x-text="'Editar Categoría: ' + currentCategory.name"></h3>
                                <button @click="editCategoryModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form x-bind:action="'{{ route('admin.categories.update', ':category_id') }}'.replace(':category_id', currentCategory.id)" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')

                                {{-- Campo oculto para category_id en caso de errores de validación --}}
                                <input type="hidden" name="category_id" :value="currentCategory.id"> 

                                <div>
                                    <label for="edit-category-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Categoría:</label>
                                    <input type="text" id="edit-category-name" name="name"
                                           x-model="currentCategory.name"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    {{-- @error('name')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror --}}
                                </div>

                                <div>
                                    <label for="edit-category-description" class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                                    <textarea id="edit-category-description" name="description" rows="3"
                                              x-model="currentCategory.description"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                              placeholder="Describe la categoría..."></textarea>
                                    {{-- @error('description')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror --}}
                                </div>

                                <div class="mt-6 flex justify-center gap-3">
                                    <button type="button" @click="editCategoryModalOpen = false"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Actualizar Categoría
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de CONFIRMACIÓN de Eliminación de Categoría -->
    <div x-show="deleteCategoryModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="deleteCategoryModalOpen = false"></div>
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
                                    ¿Estás seguro de que quieres eliminar la categoría <span class="font-semibold" x-text="currentCategory.name"></span>?
                                    Esta acción no se puede deshacer si tiene elementos asociados.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form x-bind:action="'{{ route('admin.categories.destroy', ':category_id') }}'.replace(':category_id', currentCategory.id)" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                    </form>
                    <button type="button" @click="deleteCategoryModalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
