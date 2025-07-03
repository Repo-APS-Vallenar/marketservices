@extends('layouts.app')

@section('header')
Reservas
@endsection

@section('content')
{{-- Estilos para x-cloak (puedes mover esto a tu archivo CSS principal si lo deseas) --}}
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="container mx-auto py-6"
    x-data="{
        // Estados de los modales
        editBookingModalOpen: false,
        deleteBookingModalOpen: false,
        cancelBookingModalOpen: false,
        detailsBookingModalOpen: false,
        contactModalOpen: false,
        confirmActionModalOpen: false, // Nuevo modal para confirmar acciones de proveedor
        currentBooking: null, // Almacena la reserva seleccionada
        actionType: '', // 'confirm' o 'reject'

        // Variables de estado para los campos del formulario de edición de reserva
        bookingScheduledAt: '',
        bookingNotes: '',
        bookingStatus: '',

        // Variables de estado para el modal de contactar
        recipientId: null,
        recipientName: '',
        messageContent: '',

        // Traducciones de estado para Alpine.js
        statusTranslations: {
            'pending': 'Pendiente',
            'confirmed': 'Confirmada',
            'cancelled': 'Cancelada',
            'completed': 'Completada',
            'rescheduled': 'Reprogramada'
        },

        // Funciones para abrir modales específicos
        openEditBookingModal(booking) {
            this.currentBooking = booking;
            this.bookingScheduledAt = new Date(booking.scheduled_at).toISOString().slice(0, 16);
            this.bookingNotes = booking.notes;
            this.bookingStatus = booking.status;
            this.editBookingModalOpen = true;
        },
        openDeleteBookingModal(booking) {
            this.currentBooking = booking;
            this.deleteBookingModalOpen = true;
        },
        openCancelBookingModal(booking) {
            this.currentBooking = booking;
            this.cancelBookingModalOpen = true;
        },
        openDetailsBookingModal(booking) {
            this.currentBooking = booking;
            this.detailsBookingModalOpen = true;
            // AÑADIDO PARA DEPURACIÓN: Muestra el objeto de la reserva en la consola del navegador
            console.log('Datos de la reserva en el modal de detalles:', booking); 
        },
        openContactModal(booking) {
            this.currentBooking = booking;
            this.messageContent = '';

            @if(Auth::check())
                @if(Auth::user()->role === 'customer')
                    this.recipientId = booking.service.service_provider.user.id;
                    this.recipientName = booking.service.service_provider.user.name;
                @elseif(Auth::user()->role === 'service_provider')
                    this.recipientId = booking.customer.id;
                    this.recipientName = booking.customer.name;
                @elseif(Auth::user()->role === 'admin')
                    this.recipientId = booking.customer.id;
                    this.recipientName = booking.customer.name;
                @else
                    this.recipientId = null;
                    this.recipientName = 'Usuario desconocido';
                @endif
            @else
                this.recipientId = null;
                this.recipientName = 'Invitado';
            @endif
            
            this.contactModalOpen = true;
        },
        openConfirmActionModal(booking, type) { // 'type' puede ser 'confirm' o 'reject'
            this.currentBooking = booking;
            this.actionType = type;
            this.confirmActionModalOpen = true;
        },

        // Función para cerrar todos los modales y limpiar el estado
        closeModals() {
            this.editBookingModalOpen = false;
            this.deleteBookingModalOpen = false;
            this.cancelBookingModalOpen = false;
            this.detailsBookingModalOpen = false;
            this.contactModalOpen = false;
            this.confirmActionModalOpen = false; // Cerrar también el nuevo modal
            this.currentBooking = null;
            this.actionType = ''; // Limpiar el tipo de acción
            this.bookingScheduledAt = '';
            this.bookingNotes = '';
            this.bookingStatus = '';
            this.recipientId = null;
            this.recipientName = '';
            this.messageContent = '';
        }
    }"
    @keydown.escape="closeModals()">

    <h1 class="text-2xl font-bold mb-4">Reservas</h1>

    {{-- Los mensajes de éxito y error ahora son manejados globalmente por layouts/app.blade.php --}}
    {{-- @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

    <div class="bg-white shadow rounded overflow-hidden">
        <x-responsive-table>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50 rounded-tl-md">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Servicio</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Estado</th>
                        <th class="px-6 py-3 bg-gray-50 rounded-tr-md">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                        <tr>
                            {{-- Si el usuario es proveedor, muestra el nombre del cliente; si es cliente, muestra el suyo --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(Auth::user()->role === 'service_provider')
                                    {{ $booking->customer->name ?? 'N/A' }}
                                @else
                                    {{ $booking->customer->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $booking->service->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $booking->scheduled_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusTranslations = [
                                        'pending' => 'Pendiente',
                                        'confirmed' => 'Confirmada',
                                        'cancelled' => 'Cancelada',
                                        'completed' => 'Completada',
                                        'rescheduled' => 'Reprogramada',
                                    ];
                                @endphp
                                {{ $statusTranslations[$booking->status] ?? ucfirst($booking->status) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                {{-- Botón Ver (ahora abre modal) --}}
                                <button @click="openDetailsBookingModal({{ $booking }})" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</button>
                                
                                {{-- Botón Contactar (ahora abre modal) - Lógica de visibilidad mejorada --}}
                                @if(Auth::check())
                                    @if(Auth::id() === $booking->customer_id || Auth::user()->role === 'admin')
                                        {{-- Cliente o Admin: Siempre pueden contactar --}}
                                        <button @click="openContactModal({{ $booking }})" class="text-blue-600 hover:text-blue-900 mr-2">Contactar</button>
                                    @elseif(Auth::user()->role === 'service_provider' && $booking->service->service_provider_id === Auth::user()->serviceProvider->id && $booking->status === 'pending')
                                        {{-- Proveedor: Solo si la reserva es suya y está pendiente --}}
                                        <button @click="openContactModal({{ $booking }})" class="text-blue-600 hover:text-blue-900 mr-2">Contactar</button>
                                    @endif
                                @endif

                                {{-- Botones de acción para el PROVEEDOR DE SERVICIOS (o Admin) --}}
                                @if(Auth::check() && (Auth::user()->role === 'service_provider' && $booking->service->service_provider_id === Auth::user()->serviceProvider->id) || Auth::user()->role === 'admin')
                                    @if($booking->status === 'pending')
                                        <button @click="openConfirmActionModal({{ $booking }}, 'confirm')" class="text-green-600 hover:text-green-900 mr-2">Aceptar</button>
                                        <button @click="openConfirmActionModal({{ $booking }}, 'reject')" class="text-red-600 hover:text-red-900 mr-2">Rechazar</button>
                                    @endif
                                @endif

                                {{-- Botones Editar y Eliminar (solo para el cliente de la reserva o admin) --}}
                                @if(Auth::check() && (Auth::id() === $booking->customer_id || Auth::user()->role === 'admin'))
                                    <button @click="openEditBookingModal({{ $booking }})" class="text-green-600 hover:text-green-900 mr-2">Editar</button>
                                    <button @click="openDeleteBookingModal({{ $booking }})" class="text-red-600 hover:text-red-900 mr-2">Eliminar</button>
                                @endif
                                
                                {{-- Botón Cancelar (solo para el cliente de la reserva o admin, si la reserva no está ya cancelada o completada) --}}
                                @if(Auth::check() && (Auth::id() === $booking->customer_id || Auth::user()->role === 'admin') && $booking->status !== 'cancelled' && $booking->status !== 'completed')
                                    <button @click="openCancelBookingModal({{ $booking }})" class="text-orange-600 hover:text-orange-900">Cancelar</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay reservas para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-responsive-table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>

    {{-- MODAL DE EDICIÓN DE RESERVA --}}
    <div x-show="editBookingModalOpen" x-cloak
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Editar Reserva: <span x-text="currentBooking ? currentBooking.service.name : ''"></span></h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form :action="'{{ url('bookings') }}/' + (currentBooking ? currentBooking.id : '')" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')

                                {{-- Campos ocultos para asegurar que service_id y status siempre se envíen --}}
                                <input type="hidden" name="service_id" :value="currentBooking?.service_id || ''">
                                <input type="hidden" name="status" :value="bookingStatus">

                                <div>
                                    <label for="edit-booking-scheduled-at" class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora</label>
                                    <input type="datetime-local" id="edit-booking-scheduled-at" name="scheduled_at"
                                           x-model="bookingScheduledAt"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                           required>
                                    @error('scheduled_at')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div>
                                    <label for="edit-booking-notes" class="block text-sm font-medium text-gray-700 mb-1">Notas adicionales</label>
                                    <textarea id="edit-booking-notes" name="notes" rows="3"
                                              x-model="bookingNotes"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                              placeholder="Notas adicionales..."></textarea>
                                    @error('notes')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                {{-- Campo de estado (solo visible y editable para admin o service_provider) --}}
                                @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'service_provider'))
                                    <div>
                                        <label for="edit-booking-status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                        <select id="edit-booking-status" name="status"
                                                x-model="bookingStatus"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                                required>
                                            <option value="pending">Pendiente</option>
                                            <option value="confirmed">Confirmada</option>
                                            <option value="cancelled">Cancelada</option>
                                            <option value="completed">Completada</option>
                                            <option value="rescheduled">Reprogramada</option>
                                        </select>
                                        @error('status')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                    </div>
                                @else
                                    {{-- Si no es admin o proveedor, mostrar el estado actual como texto --}}
                                    <div class="mb-4">
                                        <label class="block text-gray-700 mb-1">Estado actual</label>
                                        <div class="px-3 py-2 border rounded-md bg-gray-100 text-gray-900" x-text="statusTranslations[bookingStatus] || bookingStatus"></div>
                                    </div>
                                @endif

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

    {{-- MODAL DE ELIMINACIÓN DE RESERVA --}}
    <div x-show="deleteBookingModalOpen" x-cloak
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
                            <p class="text-sm text-gray-500">¿Estás seguro de que deseas eliminar la reserva para el servicio "<strong x-text="currentBooking ? currentBooking.service.name : ''"></strong>"? Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:justify-center sm:flex-row-reverse sm:gap-3">
                    <form :action="'{{ url('bookings') }}/' + (currentBooking ? currentBooking.id : '')" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                            Eliminar
                        </button>
                    </form>
                    <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- NUEVO MODAL DE CANCELACIÓN DE RESERVA (para clientes/admin) --}}
    <div x-show="cancelBookingModalOpen" x-cloak
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Confirmar Cancelación</h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500">¿Estás seguro de que deseas cancelar la reserva para el servicio "<strong x-text="currentBooking ? currentBooking.service.name : ''"></strong>"? Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:justify-center sm:flex-row-reverse sm:gap-3">
                    <form :action="'{{ url('bookings') }}/' + (currentBooking ? currentBooking.id : '')" method="POST">
                        @csrf
                        @method('PUT') {{-- Usar el método PUT para actualizar --}}
                        <input type="hidden" name="status" value="cancelled"> {{-- Campo oculto para cambiar el estado --}}
                        
                        {{-- También necesitas enviar los campos requeridos por la validación del update,
                             aunque no se muestren en el formulario, para evitar errores de validación.
                             Los precargamos con los valores actuales de la reserva. --}}
                        <input type="hidden" name="service_id" :value="currentBooking ? currentBooking.service_id : ''">
                        <input type="hidden" name="scheduled_at" :value="currentBooking ? currentBooking.scheduled_at : ''">
                        <input type="hidden" name="notes" :value="currentBooking ? currentBooking.notes : ''">

                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                            Confirmar Cancelación
                        </button>
                    </form>
                    <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Volver
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE DETALLES DE RESERVA (DISEÑO MEJORADO) --}}
    <div x-show="detailsBookingModalOpen" x-cloak
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
                <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 sm:mt-0 sm:text-left w-full">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">
                                <h3 class="text-2xl leading-6 font-extrabold text-gray-900">Detalle de Reserva #<span x-text="currentBooking ? currentBooking.id : ''"></span></h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="space-y-6">
                                {{-- Sección de Servicio --}}
                                <div class="bg-blue-50 p-4 rounded-lg shadow-sm flex items-center gap-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1l-.75-3M3 13h18M5 17h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <dt class="text-sm font-semibold text-blue-700">Servicio</dt>
                                        <dd class="mt-0.5 text-lg font-bold text-gray-900" x-text="currentBooking?.service?.name || '-'"></dd>
                                    </div>
                                </div>

                                {{-- Sección de Proveedor (condicional para mostrar "Tu Servicio" si es el proveedor logueado) --}}
                                <div class="bg-green-50 p-4 rounded-lg shadow-sm flex items-center gap-4">
                                    <svg xmlns="http://www.w3.org/2000/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <dt class="text-sm font-semibold text-green-700">Proveedor</dt>
                                        <dd class="mt-0.5 text-lg font-bold text-gray-900">
                                            <template x-if="$store?.userRole === 'service_provider' && currentBooking?.service?.service_provider_id == $store?.serviceProviderId">
                                                Tu Servicio
                                            </template>
                                            <template x-if="!($store?.userRole === 'service_provider' && currentBooking?.service?.service_provider_id == $store?.serviceProviderId)">
                                                <span x-text="currentBooking?.service?.service_provider?.user?.name || '-'"></span>
                                            </template>
                                        </dd>
                                    </div>
                                </div>

                                {{-- Sección de Fecha y Hora --}}
                                <div class="bg-yellow-50 p-4 rounded-lg shadow-sm flex items-center gap-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <dt class="text-sm font-semibold text-yellow-700">Fecha y hora</dt>
                                        <dd class="mt-0.5 text-lg font-bold text-gray-900" x-text="currentBooking ? new Date(currentBooking.scheduled_at).toLocaleString('es-CL', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'"></dd>
                                    </div>
                                </div>

                                {{-- Sección de Estado --}}
                                <div class="bg-purple-50 p-4 rounded-lg shadow-sm flex items-center gap-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <dt class="text-sm font-semibold text-purple-700">Estado</dt>
                                        <dd class="mt-0.5 text-lg font-bold text-gray-900" x-text="statusTranslations[currentBooking?.status] || currentBooking?.status || '-'"></dd>
                                    </div>
                                </div>

                                {{-- Sección de Notas --}}
                                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                    <dt class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h10m-9 4h4M9 10V4a1 1 0 011-1h4a1 1 0 011 1v6m-3 4h.01M17 16H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2z" />
                                        </svg>
                                        Notas
                                    </dt>
                                    <dd class="mt-1 text-base text-gray-800 whitespace-pre-line" x-text="currentBooking?.notes || 'Sin notas.'"></dd>
                                </div>

                                {{-- Información de Contacto del Proveedor (Condicional) --}}
                                {{-- Se muestra si la reserva está confirmada o completada --}}
                                <template x-if="currentBooking && (currentBooking.status === 'confirmed' || currentBooking.status === 'completed')">
                                    <div class="bg-indigo-50 p-4 rounded-lg shadow-sm space-y-2">
                                        <h4 class="text-base font-bold text-indigo-700 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2H5a2 2 0 01-2-2V5z" />
                                            </svg>
                                            Contacto Directo del Proveedor
                                        </h4>
                                        <p class="text-sm text-gray-800">
                                            <strong class="font-medium">Teléfono:</strong> 
                                            <span x-text="currentBooking?.service?.service_provider?.phone_number || 'No disponible'"></span>
                                        </p>
                                        <p class="text-sm text-gray-800">
                                            <strong class="font-medium">Correo:</strong> 
                                            <span x-text="currentBooking?.service?.service_provider?.user?.email || 'No disponible'"></span>
                                        </p>
                                    </div>
                                </template>

                                {{-- Datos del Cliente (visible para admin y service_provider) --}}
                                @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'service_provider'))
                                <div class="bg-red-50 p-4 rounded-lg shadow-sm space-y-2">
                                    <h4 class="text-base font-bold text-red-700 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM12 14a1 1 0 01-1 1h-1a1 1 0 00-1 1v2a1 1 0 001 1h3a1 1 0 001-1v-2a1 1 0 00-1-1h-1a1 1 0 01-1-1z" />
                                        </svg>
                                        Datos del Cliente
                                    </h4>
                                    <p class="text-sm text-gray-800">
                                        <strong class="font-medium">Nombre:</strong> 
                                        <span x-text="currentBooking?.customer?.name || '-'"></span>
                                    </p>
                                    <p class="text-sm text-gray-800">
                                        <strong class="font-medium">Correo:</strong> 
                                        <span x-text="currentBooking?.customer?.email || 'No disponible'"></span>
                                    </p>
                                    {{-- Si el cliente tuviera un campo de teléfono en el modelo User o en un perfil de cliente relacionado, se podría añadir aquí --}}
                                    {{-- <p class="text-sm text-gray-800">
                                        <strong class="font-medium">Teléfono:</strong> 
                                        <span x-text="currentBooking?.customer?.phone_number || 'No disponible'"></span>
                                    </p> --}}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 sm:px-8 sm:flex sm:justify-end sm:gap-3 rounded-b-lg">
                    <button type="button" @click="closeModals()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm transition-colors duration-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE CONTACTAR --}}
    <div x-show="contactModalOpen" x-cloak
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4">Contactar a <span x-text="recipientName"></span></h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form action="{{ route('messages.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="recipient_id" :value="recipientId">

                                <div>
                                    <label for="message-content" class="block text-sm font-medium text-gray-700 mb-1">Tu mensaje</label>
                                    <textarea id="message-content" name="message_content" rows="5"
                                              x-model="messageContent"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200"
                                              placeholder="Escribe tu mensaje aquí..." required></textarea>
                                    @error('message_content')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
                                </div>

                                <div class="mt-6 flex justify-center gap-3">
                                    <button type="button" @click="closeModals()"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                        Enviar Mensaje
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- NUEVO MODAL DE CONFIRMACIÓN DE ACCIÓN DE PROVEEDOR (Aceptar/Rechazar) --}}
    <div x-show="confirmActionModalOpen" x-cloak
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
                                <h3 class="text-xl leading-6 font-bold text-gray-900 mt-4" x-text="actionType === 'confirm' ? 'Confirmar Reserva' : 'Rechazar Reserva'"></h3>
                                <button @click="closeModals()" class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full p-1 transition-colors duration-200">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500">
                                ¿Estás seguro de que deseas 
                                <span x-text="actionType === 'confirm' ? 'aceptar' : 'rechazar'"></span> 
                                la reserva para el servicio "<strong x-text="currentBooking?.service?.name || ''"></strong>" del cliente "<strong x-text="currentBooking?.customer?.name || ''"></strong>"?
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:justify-center sm:flex-row-reverse sm:gap-3">
                    <form :action="'{{ url('bookings') }}/' + (currentBooking ? currentBooking.id : '')" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" :value="actionType === 'confirm' ? 'confirmed' : 'cancelled'">
                        
                        {{-- Campos ocultos para pasar la validación del update, con valores precargados --}}
                        <input type="hidden" name="service_id" :value="currentBooking?.service_id || ''">
                        <input type="hidden" name="scheduled_at" :value="currentBooking?.scheduled_at || ''">
                        <input type="hidden" name="notes" :value="currentBooking?.notes || ''">

                        <button type="submit" 
                                :class="actionType === 'confirm' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                            <span x-text="actionType === 'confirm' ? 'Confirmar' : 'Rechazar'"></span>
                        </button>
                    </form>
                    <button type="button" @click="closeModals()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Volver
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
