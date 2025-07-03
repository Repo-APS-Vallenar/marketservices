@extends('layouts.app')

@section('header')
Proveedores Pendientes
@endsection

@section('content')
<div class="container mx-auto py-6"
    x-data="{
        detailsModalOpen: false,   // Para ver detalles del proveedor
        approveModalOpen: false,   // Para confirmar aprobación
        rejectModalOpen: false,    // Para confirmar rechazo
        currentProvider: {},       // Almacena el proveedor seleccionado

        // Función para abrir el modal de detalles
        openDetailsModal(provider) {
            this.currentProvider = {}; // Limpiar antes de cargar
            // Usar fetch para obtener los detalles completos del proveedor, incluyendo relaciones
            fetch('/admin/pending-providers/' + provider.id) // Asegúrate de que esta ruta exista
                .then(response => response.json())
                .then(data => {
                    this.currentProvider = data;
                    this.detailsModalOpen = true;
                })
                .catch(error => {
                    console.error('Error fetching provider details:', error);
                    alert('No se pudieron cargar los detalles del proveedor.');
                });
        },

        // Función para abrir el modal de aprobación
        openApproveModal(provider) {
            this.currentProvider = provider;
            this.approveModalOpen = true;
        },

        // Función para abrir el modal de rechazo
        openRejectModal(provider) {
            this.currentProvider = provider;
            this.rejectModalOpen = true;
        },

        // Función para cerrar todos los modales
        closeModals() {
            this.detailsModalOpen = false;
            this.approveModalOpen = false;
            this.rejectModalOpen = false;
            this.currentProvider = {}; // Limpiar el proveedor actual
        }
    }"
    @keydown.escape="closeModals()">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Gestión de Proveedores Pendientes</h1>
    </div>

    {{-- Los mensajes de éxito/error/validación son manejados globalmente por layouts/app.blade.php --}}

    <div class="bg-white shadow rounded overflow-hidden">
        <x-responsive-table>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 rounded-tl-md">Nombre del Proveedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Áreas de Servicio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingProviders as $provider)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $provider->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $provider->user->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $provider->phone_number ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $provider->service_areas ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                            <button @click="openDetailsModal({{ $provider }})" class="text-blue-600 hover:underline focus:outline-none">Ver Detalles</button>
                            <button @click="openApproveModal({{ $provider }})" class="text-green-600 hover:underline focus:outline-none">Aprobar</button>
                            <button @click="openRejectModal({{ $provider }})" class="text-red-600 hover:underline focus:outline-none">Rechazar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay proveedores pendientes de aprobación.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-responsive-table>
    </div>

    <!-- Modal de VER DETALLES del Proveedor -->
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
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full"
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Detalles del Proveedor: <span x-text="currentProvider.user ? currentProvider.user.name : ''"></span></h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div x-if="currentProvider.id">
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentProvider.user ? currentProvider.user.name : '-'"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentProvider.user ? currentProvider.user.email : '-'"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentProvider.phone_number || '-'"></dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentProvider.status || '-'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Biografía/Descripción</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentProvider.bio || '-'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Áreas de Servicio</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentProvider.service_areas || '-'"></dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Certificación (Opcional)</dt>
                                        <dd class="mt-1 text-base text-gray-900" x-text="currentProvider.certification || 'No proporcionada'"></dd>
                                    </div>

                                    {{-- Mostrar categorías asociadas --}}
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Categorías Ofrecidas</dt>
                                        <dd class="mt-1 text-base text-gray-900">
                                            <template x-if="currentProvider.categories && currentProvider.categories.length > 0">
                                                <ul class="list-disc list-inside">
                                                    <template x-for="category in currentProvider.categories" :key="category.id">
                                                        <li x-text="category.name"></li>
                                                    </template>
                                                </ul>
                                            </template>
                                            <template x-else>
                                                <span>No hay categorías asociadas.</span>
                                            </template>
                                        </dd>
                                    </div>

                                    {{-- Mostrar servicios si los tiene --}}
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Servicios Registrados</dt>
                                        <dd class="mt-1 text-base text-gray-900">
                                            <template x-if="currentProvider.services && currentProvider.services.length > 0">
                                                <ul class="list-disc list-inside">
                                                    <template x-for="service in currentProvider.services" :key="service.id">
                                                        <li x-text="service.name + ' ($' + service.price + ' ' + service.price_unit + ')'"></li>
                                                    </template>
                                                </ul>
                                            </template>
                                            <template x-else>
                                                <span>No hay servicios registrados.</span>
                                            </template>
                                        </dd>
                                    </div>

                                    {{-- Puedes añadir la imagen de perfil aquí si `profile_picture` es una URL --}}
                                    <div class="sm:col-span-2" x-show="currentProvider.profile_picture">
                                        <dt class="text-sm font-medium text-gray-500">Foto de Perfil</dt>
                                        <dd class="mt-1">
                                            <img :src="currentProvider.profile_picture" alt="Foto de Perfil" class="w-24 h-24 rounded-full object-cover">
                                        </dd>
                                    </div>

                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" @click="closeModals()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de CONFIRMACIÓN de Aprobación -->
    <div x-show="approveModalOpen" x-cloak
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
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Confirmar Aprobación
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que quieres aprobar al proveedor <span class="font-semibold" x-text="currentProvider.user ? currentProvider.user.name : ''"></span>?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form x-bind:action="'{{ route('admin.pending-providers.approve', ':provider_id') }}'.replace(':provider_id', currentProvider.id)" method="POST" class="inline-block">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Aprobar
                        </button>
                    </form>
                    <button type="button" @click="closeModals()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de CONFIRMACIÓN de Rechazo -->
    <div x-show="rejectModalOpen" x-cloak
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
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Confirmar Rechazo
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    ¿Estás seguro de que quieres rechazar al proveedor <span class="font-semibold" x-text="currentProvider.user ? currentProvider.user.name : ''"></span>? Esta acción no se puede deshacer.
                                </p>
                                {{-- Opcional: Campo para la razón de rechazo --}}

                                <div class="mt-4">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Razón del rechazo (opcional):</label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form x-bind:action="'{{ route('admin.pending-providers.reject', ':provider_id') }}'.replace(':provider_id', currentProvider.id)" method="POST" class="inline-block">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Rechazar
                        </button>
                    </form>
                    <button type="button" @click="closeModals()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection