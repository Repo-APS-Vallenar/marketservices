@extends('layouts.app')

@section('header')
Marketplace de Servicios
@endsection

@section('content')
{{-- Estilos para x-cloak (puedes mover esto a tu archivo CSS principal si lo deseas) --}}
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="container mx-auto py-6 px-4"
    x-data="{
        detailsModalOpen: false,
        selectedService: null,
        
        openDetailsModal(service) {
            this.selectedService = service;
            this.detailsModalOpen = true;
        },
        closeDetailsModal() {
            this.detailsModalOpen = false;
            this.selectedService = null;
        },
        
        // Nueva función para redirigir al formulario de reserva con los datos del servicio
        redirectToBookingForm() {
            if (this.selectedService) {
                // Asegura que el service_name esté codificado para URL si contiene espacios o caracteres especiales
                const bookingUrl = `/bookings/create?service_id=${this.selectedService.id}&service_name=${encodeURIComponent(this.selectedService.name)}`;
                window.location.href = bookingUrl;
            }
        }
    }"
    @keydown.escape="closeDetailsModal()">

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar de filtros -->
        <div class="w-full md:w-64 bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-4">Filtros</h2>
            <form action="{{ route('marketplace.services.index') }}" method="GET">
                <div class="space-y-4">
                    <!-- Búsqueda -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <!-- Categorías -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categorías</label>
                        <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">
                        Aplicar filtros
                    </button>
                </div>
            </form>
        </div>

        <!-- Grid de servicios -->
        <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services as $service)
                    <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col h-full">
                        <div class="p-4 flex flex-col h-full">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <h3 class="text-lg font-semibold text-gray-900 break-words">{{ $service->name }}</h3>
                                <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full max-w-[70vw] truncate">{{ $service->category->name }}</span>
                            </div>
                            <p class="mt-2 text-gray-600 line-clamp-3 break-words">{{ $service->description }}</p>
                            <div class="mt-4 flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-gray-900">${{ number_format($service->price, 0) }}</span>
                                <span class="text-gray-500 text-sm">/ {{ $service->price_unit }}</span>
                            </div>
                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 flex-1">
                                <span class="text-sm text-gray-500">Por {{ $service->serviceProvider->user->name }}</span>
                                <button
                                    type="button"
                                    data-service='@json($service->load(["category", "serviceProvider.user"]))'
                                    @click="openDetailsModal(JSON.parse($el.getAttribute('data-service')))" class="w-full sm:w-auto mt-2 sm:mt-0 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    Ver detalles
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900">No se encontraron servicios</h3>
                        <p class="mt-2 text-sm text-gray-500">Intenta con otros filtros de búsqueda</p>
                    </div>
                @endforelse
            </div>
            <!-- Paginación -->
            <div class="mt-6">
                {{ $services->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de detalles -->
    <div x-show="detailsModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative" @click.away="closeDetailsModal()">
            <button @click="closeDetailsModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <span class="sr-only">Cerrar</span>
                <!-- Icono SVG para cerrar -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <template x-if="selectedService">
                <div>
                    <!-- Encabezado -->
                    <div class="flex items-center justify-between border-b pb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900" x-text="selectedService.name"></h1>
                            <div class="mt-1 flex items-center">
                                <span class="px-2 py-1 text-sm bg-indigo-100 text-indigo-800 rounded-full" x-text="selectedService.category?.name"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-gray-900" x-text="'$' + Number(selectedService.price).toLocaleString('es-CL', {minimumFractionDigits: 0})"></div>
                            <div class="text-sm text-gray-500" x-text="'por ' + selectedService.price_unit"></div>
                        </div>
                    </div>
                    <!-- Información del proveedor -->
                    <div class="mt-6 border-b pb-4">
                        <h2 class="text-lg font-semibold mb-2">Proveedor del Servicio</h2>
                        <div class="flex items-center">
                            {{-- Considera agregar un avatar o imagen del proveedor aquí --}}
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900" x-text="selectedService.service_provider?.user?.name"></div>
                                <template x-if="selectedService.service_provider?.company_name">
                                    <div class="text-sm text-gray-500" x-text="selectedService.service_provider.company_name"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <!-- Detalles del servicio -->
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-2">Descripción del Servicio</h2>
                        <p class="text-gray-700 whitespace-pre-line" x-text="selectedService.description"></p>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <template x-if="selectedService.estimated_duration">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">Duración estimada</div>
                                    <div class="text-lg font-medium" x-text="selectedService.estimated_duration + (selectedService.estimated_duration == 1 ? ' hora' : ' horas')"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <!-- Botón de reserva -->
                    <div class="mt-8">
                        @if(Auth::check() && (auth()->user()->role === 'customer' || auth()->user()->role === 'admin'))
                            <button type="button" @click="redirectToBookingForm()"
                                class="block w-full text-center bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700">
                                Reservar este servicio
                            </button>
                        @elseif(!Auth::check())
                            <a href="{{ route('login') }}"
                               class="block w-full text-center bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700">
                                Inicia sesión para reservar
                            </a>
                        @endif
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
