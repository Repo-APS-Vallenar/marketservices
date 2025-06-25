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
        <div class="flex-1 flex flex-col">
            <!-- Logo y título de menú siempre visibles -->
            <div class="flex flex-col items-center justify-center h-20 border-b border-indigo-100">
                <span class="text-3xl font-extrabold">
                    <span class="text-indigo-700">C</span><span class="text-purple-500">M</span><span class="text-indigo-700">H</span>
                </span>
                <span class="text-2xl font-bold text-indigo-700 mt-1">Menú</span>
            </div>
            <!-- Indicador de módulo actual -->
            <div class="px-4 py-2 text-indigo-600 font-semibold text-sm border-b border-indigo-50 bg-indigo-50 text-center md:text-left md:text-sm text-base md:font-semibold font-bold">
                @switch(true)
                    @case(request()->routeIs('dashboard'))
                        Dashboard Proveedor
                        @break
                    @case(request()->routeIs('profile.edit'))
                        Perfil
                        @break
                    @case(request()->routeIs('services.*'))
                        Servicios
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
            <nav class="flex flex-col px-4 py-6 flex-1">
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('dashboard') }}" 
                       class="block px-4 py-2 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}" 
                       class="block px-4 py-2 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('profile.edit') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                        Perfil
                    </a>
                    @if(auth()->user() && (auth()->user()->role === 'service_provider' || auth()->user()->role === 'admin'))
                    <a href="{{ route('services.index') }}" 
                       class="block px-4 py-2 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('services.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                        Servicios
                    </a>
                    @endif
                    <a href="{{ route('bookings.index') }}" 
                       class="block px-4 py-2 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('bookings.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                        Reservas
                    </a>
                    <a href="{{ route('messages.index') }}" 
                       class="block px-4 py-2 rounded hover:bg-indigo-100 hover:text-indigo-700 transition {{ request()->routeIs('messages.*') ? 'bg-indigo-100 text-indigo-700 font-semibold' : '' }}">
                        Mensajes
                    </a>
                </div>
            </nav>
        </div>
        <div class="mt-12 mb-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <div class="flex justify-center">
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block px-4 py-2 rounded hover:bg-red-100 text-red-500 hover:text-red-700 transition text-center">
                        Cerrar sesión
                    </a>
                </div>
            </form>
        </div>
    </aside>
</div> 