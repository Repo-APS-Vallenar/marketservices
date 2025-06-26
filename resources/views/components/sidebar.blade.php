<div x-data="{ open: false }" class="relative">
    <!-- Botón hamburguesa solo visible en móvil -->
    <button @click="open = !open" class="md:hidden fixed top-4 left-4 z-50 bg-white rounded-full p-2 shadow focus:outline-none">
        <svg class="h-6 w-6 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Overlay para móvil -->
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black bg-opacity-40 z-40 md:hidden" x-cloak></div>

    <!-- Sidebar -->
    <aside :class="{'-translate-x-full': !open, 'translate-x-0': open}" class="w-64 h-screen bg-white text-gray-900 border-r border-indigo-100 fixed top-0 left-0 flex flex-col shadow-md transform transition-transform duration-200 ease-in-out z-50 md:translate-x-0 md:static md:block" x-cloak>
        <!-- Contenedor principal con flex-1 -->
        <div class="flex flex-col h-full">
            <!-- Logo y título -->
            <div class="flex flex-col items-center justify-center h-20 border-b border-indigo-100">
                <span class="text-3xl font-extrabold">
                    <span class="text-indigo-700">C</span><span class="text-purple-500">M</span><span class="text-indigo-700">H</span>
                </span>
                <span class="text-2xl font-bold text-indigo-700 mt-1">Menú</span>
            </div>

            <!-- Contenedor del contenido principal (flex-1 para que ocupe el espacio restante) -->
            <div class="flex-1 flex flex-col min-h-0">
                <!-- Indicador de módulo actual -->
                <div class="px-4 py-2 text-indigo-600 font-semibold text-sm border-b border-indigo-50 bg-indigo-50 text-center md:text-left md:text-sm text-base md:font-semibold font-bold">
                    @switch(true)
                        @case(request()->routeIs('dashboard'))
                            @php
                                $userRole = auth()->user()->role;
                                $dashboardText = match($userRole) {
                                    'admin' => 'Dashboard Administrador',
                                    'service_provider' => 'Dashboard Proveedor',
                                    'customer' => 'Dashboard Cliente',
                                    default => 'Dashboard'
                                };
                            @endphp
                            {{ $dashboardText }}
                            @break
                        @case(request()->routeIs('profile.edit'))
                            Perfil
                            @break
                        @case(request()->routeIs('services.*'))
                            @if(auth()->user()->role === 'admin')
                                Servicios Proveedor
                            @else
                                Servicios
                            @endif
                            @break
                        @case(request()->routeIs('marketplace.services.*'))
                            @if(auth()->user()->role === 'admin')
                                Servicios Cliente
                            @else
                                Servicios
                            @endif
                            @break
                        @case(request()->routeIs('bookings.*'))
                            Reservas
                            @break
                        @case(request()->routeIs('messages.*'))
                            Mensajes
                            @break
                        @default
                            &nbsp;
                    @endswitch
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 flex flex-col px-4 py-6 overflow-y-auto">
                    <div class="flex flex-col">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           class="block px-4 py-2 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Dashboard
                            </div>
                        </a>

                        @if(auth()->user()->role === 'admin')
                            <!-- Sección de Administración -->
                            <div class="mt-8">
                                <div class="px-4 mb-4">
                                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administración</div>
                                    <div class="mt-1 h-1 bg-gradient-to-r from-indigo-700 to-purple-500 rounded-full"></div>
                                </div>
                                
                                <a href="{{ route('admin.users.index') }}" 
                                   class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Gestionar Usuarios
                                    </div>
                                </a>
                                
                                <a href="{{ route('admin.categories.index') }}" 
                                   class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Gestionar Categorías
                                    </div>
                                </a>
                                
                                <a href="{{ route('admin.providers.pending') }}" 
                                   class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('admin.providers.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Proveedores Pendientes
                                    </div>
                                </a>
                            </div>

                            <!-- Sección de Módulos -->
                            <div class="mt-8">
                                <div class="px-4 mb-4">
                                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Módulos</div>
                                    <div class="mt-1 h-1 bg-gradient-to-r from-indigo-700 to-purple-500 rounded-full"></div>
                                </div>
                        @endif

                        <!-- Perfil -->
                        <a href="{{ route('profile.edit') }}" 
                           class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('profile.edit') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Perfil
                            </div>
                        </a>

                        <!-- Servicios -->
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('services.index') }}" 
                               class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('services.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Servicios Proveedor
                                </div>
                            </a>
                            <a href="{{ route('marketplace.services.index') }}" 
                               class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('marketplace.services.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Servicios Cliente
                                </div>
                            </a>
                        @elseif(auth()->user()->role === 'service_provider')
                            <a href="{{ route('services.index') }}" 
                               class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('services.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Servicios
                                </div>
                            </a>
                        @elseif(auth()->user()->role === 'customer')
                            <a href="{{ route('marketplace.services.index') }}" 
                               class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('marketplace.services.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    Servicios
                                </div>
                            </a>
                        @endif

                        <!-- Reservas y Mensajes -->
                        <a href="{{ route('bookings.index') }}" 
                           class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('bookings.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Reservas
                            </div>
                        </a>
                        
                        <a href="{{ route('messages.index') }}" 
                           class="block px-4 py-2 mb-1 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('messages.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                Mensajes
                            </div>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Botón Cerrar Sesión -->
            <div class="p-4 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-4 py-2 rounded hover:bg-red-100 text-red-500 hover:text-red-700 transition text-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>