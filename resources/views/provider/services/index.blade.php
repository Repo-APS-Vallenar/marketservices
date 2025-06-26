@extends('layouts.app')

@section('header')
Mis servicios
{{-- Link a Font Awesome para los iconos --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endsection

@section('content')
{{-- Estilos para x-cloak (puedes mover esto a tu archivo CSS principal si lo deseas) --}}
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="container mx-auto py-6"
    x-data="{
        // Estados de los modales
        createModalOpen: false,
        editModalOpen: false,
        deleteModalOpen: false,
        detailsModalOpen: false,

        // Variable para almacenar el servicio actual que se está viendo/editando/eliminando
        currentService: null,

        // Variables de estado para los campos del formulario (usadas para crear y para cargar en edición)
        serviceName: '{{ old('name', '') }}',
        serviceCategoryId: '{{ old('category_id', '') }}',
        serviceDescription: '{{ old('description', '') }}',
        rawPrice: parseFloat('{{ old('price', 0) }}') || 0, // Valor numérico real para enviar
        displayPrice: '{{ old('price', '') ? number_format(old('price'), 0, ',', '.') : '' }}', // Valor formateado para mostrar
        servicePriceUnit: '{{ old('price_unit', 'por hora') }}',
        serviceEstimatedDuration: '{{ old('estimated_duration', '1') }}', 
        serviceServiceProviderId: '{{ old('service_provider_id', '') }}', // Nueva variable para el ServiceProviderId en el formulario

        // Funciones para abrir modales específicos
        openCreateModal() {
            this.resetForm(); // Limpiar el formulario para un nuevo servicio
            this.createModalOpen = true;
        },
        openEditModal(service) {
            this.currentService = service; // Establecer el servicio actual
            // Precargar los datos del servicio en las variables del formulario
            this.serviceName = service.name;
            this.serviceCategoryId = service.category_id;
            this.serviceDescription = service.description;
            this.rawPrice = parseFloat(service.price) || 0;
            this.formatOnBlur(); // Formatear el precio para la visualización
            this.servicePriceUnit = service.price_unit;
            this.serviceEstimatedDuration = service.estimated_duration;
            this.serviceServiceProviderId = service.service_provider_id; // Cargar el ServiceProviderId
            this.editModalOpen = true;
        },
        openDeleteModal(service) {
            this.currentService = service;
            this.deleteModalOpen = true;
        },
        openDetailsModal(service) {
            this.currentService = service;
            this.detailsModalOpen = true;
        },

        // Función para cerrar todos los modales y limpiar el servicio actual
        closeModals() {
            this.createModalOpen = false;
            this.editModalOpen = false;
            this.deleteModalOpen = false;
            this.detailsModalOpen = false;
            this.currentService = null; // Limpiar datos del servicio actual
            this.resetForm(); // Opcional: resetear formulario después de cerrar cualquier modal
        },

        // Función para limpiar los campos del formulario (usada principalmente para el modal de creación)
        resetForm() {
            this.serviceName = '';
            this.serviceCategoryId = '';
            this.serviceDescription = '';
            this.rawPrice = 0;
            this.displayPrice = '';
            this.servicePriceUnit = 'por hora';
            this.serviceEstimatedDuration = '1';
            this.serviceServiceProviderId = ''; // Resetear también el ServiceProviderId
            this.formChanged = false;
        },

        // Funciones para el formato de precio (sin cambios respecto a la última versión)
        handlePriceInput(event) {
            let inputElement = event.target;
            let cursorPosition = inputElement.selectionStart;
            let originalValue = inputElement.value;

            let cleanValue = originalValue.replace(/[^0-9]/g, '');
            
            this.rawPrice = parseInt(cleanValue, 10) || 0;

            const formatter = new Intl.NumberFormat('es-CL', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
                useGrouping: true
            });

            let newFormattedValue = formatter.format(this.rawPrice);
            if (cleanValue === '') {
                newFormattedValue = '';
            }

            this.displayPrice = newFormattedValue;

            if (inputElement === document.activeElement) {
                let lengthDifference = newFormattedValue.length - originalValue.length;
                let newCursorPosition = Math.min(cursorPosition + lengthDifference, newFormattedValue.length);

                this.$nextTick(() => {
                    inputElement.setSelectionRange(newCursorPosition, newCursorPosition);
                });
            }
        },

        formatOnBlur() {
            if (this.rawPrice > 0) {
                const formatter = new Intl.NumberFormat('es-CL', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                    useGrouping: true
                });
                this.displayPrice = formatter.format(this.rawPrice);
            } else {
                this.displayPrice = '';
            }
        },

        // Inicialización y watcher para el modal de creación (sin cambios)
        init() {
            this.formatOnBlur(); // Asegura que el old('price') se formatee al cargar la página

            this.$watch('createModalOpen', (value) => { // Solo para el modal de creación
                if (value) {
                    this.$nextTick(() => {
                        this.serviceEstimatedDuration = '1'; 
                        this.formatOnBlur(); 
                    });
                }
            });
        }
    }"
    @keydown.escape="closeModals()"> {{-- Modificado para cerrar cualquier modal --}}

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Mis servicios</h1>
    </div>
    <!-- Botón que abre el modal de creación - Añadido margin-bottom -->
    <button @click="openCreateModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-md inline-flex items-center gap-2 hover:bg-indigo-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nuevo servicio
    </button>

    @if(session('success'))
    {{-- Mensaje de éxito con Alpine.js para ocultar después de 5 segundos --}}
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition.opacity.duration.500ms class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden"> {{-- Añadido overflow-hidden aquí --}}
        <x-responsive-table>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50 rounded-tl-md">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Duración</th>
                        <th class="px-6 py-3 bg-gray-50 rounded-tr-md"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($services as $service)
                    <tr class="hover:bg-gray-50 transition-colors duration-150"> {{-- Hover effect for rows --}}
                        <td class="px-6 py-4">{{ $service->name }}</td>
                        <td class="px-6 py-4">{{ $service->category->name ?? '-' }}</td>
                        <td class="px-6 py-4">${{ number_format($service->price, 2) }} {{ $service->price_unit }}</td>
                        <td class="px-6 py-4">
                            @if($service->estimated_duration)
                                {{ $service->estimated_duration }} 
                                {{ $service->estimated_duration == 1 ? 'hora' : 'horas' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 flex items-center justify-center gap-2"> {{-- Alineación centrada y separación para los botones --}}
                            {{-- Botón para ver detalles (abre modal) --}}
                            <button @click="openDetailsModal({{ $service }})" class="flex items-center justify-center px-3 py-1 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                <i class="fa-solid fa-eye mr-1 text-white"></i> Ver
                            </button>
                            {{-- Botón para editar (abre modal) --}}
                            <button @click="openEditModal({{ $service }})" class="flex items-center justify-center px-3 py-1 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200">
                                <i class="fa-solid fa-pen mr-1 text-white"></i> Editar
                            </button>
                            {{-- Botón para eliminar (abre modal) --}}
                            <button @click="openDeleteModal({{ $service }})" class="flex items-center justify-center px-3 py-1 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200">
                                <i class="fa-solid fa-trash-alt mr-1 text-white"></i> Eliminar
                            </button>
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

    {{-- MODAL DE CREACIÓN DE SERVICIO --}}
    <div x-show="createModalOpen" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="closeModals()"></div>
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Formulario Nuevo Servicio</h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
                                @csrf

                                @if(Auth::user()->role === 'admin')
                                    <div>
                                        <label for="service-provider-id" class="block text-sm font-medium text-gray-700 mb-1">Proveedor de Servicio</label>
                                        <select id="service-provider-id" name="service_provider_id" 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200" 
                                                x-model="serviceServiceProviderId" required> {{-- Usar la variable de estado --}}
                                            <option value="">Selecciona un proveedor</option>
                                            @foreach($serviceProviders as $provider)
                                                <option value="{{ $provider->id }}" {{ old('service_provider_id') == $provider->id ? 'selected' : '' }}>
                                                    {{ $provider->user->name ?? 'ID: ' . $provider->id }} 
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('service_provider_id'))
                                            <div class="mt-1 text-red-600 text-sm">
                                                {{ $errors->first('service_provider_id') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                <div>
                                    <label for="service-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del servicio</label>
                                    <input type="text" id="service-name" name="name"
                                           x-model="serviceName"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('name')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="service-category" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                    <select id="service-category" name="category_id"
                                            x-model="serviceCategoryId"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                            required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="service-description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                    <textarea id="service-description" name="description" rows="3"
                                              x-model="serviceDescription"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                              placeholder="Describe tu servicio..."></textarea>
                                    @error('description')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="service-price" class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <h2 class="text-gray-500 sm:text-sm mt-2 ml-1">$</h2>
                                            </div>
                                            <input type="text" id="service-price" 
                                                   x-bind:value="displayPrice"
                                                   x-on:input="handlePriceInput($event)"
                                                   x-on:blur="formatOnBlur()"
                                                   x-ref="priceInput"
                                                   class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                                   min="0" step="0.01">
                                            <input type="hidden" name="price" :value="rawPrice">
                                        </div>
                                        @error('price')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                    </div>

                                    <div>
                                        <label for="service-price-unit" class="block text-sm font-medium text-gray-700 mb-1">Unidad</label>
                                        <input type="text" id="service-price-unit" name="price_unit"
                                               x-model="servicePriceUnit"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                               placeholder="por hora, por sesión, etc">
                                        @error('price_unit')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="service-duration" class="block text-sm font-medium text-gray-700 mb-1">Duración estimada (Horas)</label>
                                    <input type="number" id="service-duration" name="estimated_duration"
                                           x-model="serviceEstimatedDuration"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200">
                                    @error('estimated_duration')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div class="mt-6 flex justify-center gap-3">
                                    <button type="button" @click="closeModals()"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Guardar servicio
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE EDICIÓN DE SERVICIO --}}
    <div x-show="editModalOpen" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="closeModals()"></div>
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Editar Servicio: <span x-text="currentService ? currentService.name : ''"></span></h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Formulario de edición --}}
                            <form :action="'{{ url('services') }}/' + (currentService ? currentService.id : '')" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT') {{-- Importante para el método UPDATE de Laravel --}}

                                @if(Auth::user()->role === 'admin')
                                    <div>
                                        <label for="edit-service-provider-id" class="block text-sm font-medium text-gray-700 mb-1">Proveedor de Servicio</label>
                                        <select id="edit-service-provider-id" name="service_provider_id" 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200" 
                                                x-model="serviceServiceProviderId" required> {{-- Usar la variable de estado --}}
                                            <option value="">Selecciona un proveedor</option>
                                            @foreach($serviceProviders as $provider)
                                                <option value="{{ $provider->id }}">
                                                    {{ $provider->user->name ?? 'ID: ' . $provider->id }} 
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('service_provider_id'))
                                            <div class="mt-1 text-red-600 text-sm">
                                                {{ $errors->first('service_provider_id') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div>
                                    <label for="edit-service-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del servicio</label>
                                    <input type="text" id="edit-service-name" name="name"
                                           x-model="serviceName" {{-- Usar la variable de estado --}}
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('name')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="edit-service-category" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                    <select id="edit-service-category" name="category_id"
                                            x-model="serviceCategoryId" {{-- Usar la variable de estado --}}
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                            required>
                                        <option value="">Selecciona una categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="edit-service-description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                    <textarea id="edit-service-description" name="description" rows="3"
                                              x-model="serviceDescription" {{-- Usar la variable de estado --}}
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                              placeholder="Describe tu servicio..."></textarea>
                                    @error('description')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit-service-price" class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                                        <div class="relative rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <h2 class="text-gray-500 sm:text-sm mt-2 ml-1">$</h2>
                                            </div>
                                            <input type="text" id="edit-service-price" 
                                                   x-bind:value="displayPrice" {{-- Estos ya estaban bien --}}
                                                   x-on:input="handlePriceInput($event)"
                                                   x-on:blur="formatOnBlur()"
                                                   class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                                   min="0" step="0.01">
                                            <input type="hidden" name="price" :value="rawPrice"> {{-- Estos ya estaban bien --}}
                                        </div>
                                        @error('price')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                    </div>

                                    <div>
                                        <label for="edit-service-price-unit" class="block text-sm font-medium text-gray-700 mb-1">Unidad</label>
                                        <input type="text" id="edit-service-price-unit" name="price_unit"
                                               x-model="servicePriceUnit" {{-- Usar la variable de estado --}}
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                               placeholder="por hora, por sesión, etc">
                                        @error('price_unit')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="edit-service-duration" class="block text-sm font-medium text-gray-700 mb-1">Duración estimada (Horas)</label>
                                    <input type="number" id="edit-service-duration" name="estimated_duration"
                                           x-model="serviceEstimatedDuration"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200">
                                    @error('estimated_duration')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div class="mt-6 flex justify-center gap-3">
                                    <button type="button" @click="closeModals()"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Guardar cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN --}}
    <div x-show="deleteModalOpen" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="closeModals()"></div>
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
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Confirmar Eliminación</h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar el servicio "<strong x-text="currentService ? currentService.name : ''"></strong>"? Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:justify-center sm:flex-row-reverse sm:gap-3"> {{-- Centrado y gap --}}
                    <form :action="'{{ url('services') }}/' + (currentService ? currentService.id : '')" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                    </form>
                    <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA VER DETALLES DEL SERVICIO --}}
    <div x-show="detailsModalOpen" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/30 transition-opacity" @click="closeModals()"></div>
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Detalles del Servicio</h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Nombre del Servicio</dt>
                                    <dd class="mt-1 text-base text-gray-900" x-text="currentService ? currentService.name : '-'"></dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                                    <dd class="mt-1 text-base text-gray-900" x-text="currentService && currentService.category ? currentService.category.name : '-'"></dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                                    <dd class="mt-1 text-base text-gray-900" x-text="currentService ? currentService.description : '-'"></dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Precio</dt>
                                    <dd class="mt-1 text-base text-gray-900" x-text="currentService ? '$' + parseFloat(currentService.price).toLocaleString('es-CL', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ' + currentService.price_unit : '-'"></dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Duración Estimada</dt>
                                    <dd class="mt-1 text-base text-gray-900">
                                        <span x-text="currentService ? currentService.estimated_duration : '-'"></span> 
                                        <span x-text="currentService && currentService.estimated_duration == 1 ? 'hora' : 'horas'"></span>
                                    </dd>
                                </div>
                                @if(Auth::user()->role === 'admin')
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Proveedor de Servicio</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentService && currentService.service_provider && currentService.service_provider.user ? currentService.service_provider.user.name : (currentService ? 'ID: ' + currentService.service_provider_id : '-')"></dd>
                                    </div>
                                @endif
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Creado</dt>
                                    <dd class="mt-1 text-base text-gray-900" x-text="currentService ? new Date(currentService.created_at).toLocaleDateString() : '-'"></dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                                    <dd class="mt-1 text-base text-gray-900" x-text="currentService ? new Date(currentService.updated_at).toLocaleDateString() : '-'"></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:justify-center sm:flex-row-reverse sm:gap-3">
                    <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
