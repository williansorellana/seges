<div x-show="mobileSidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/50 md:hidden"
    @click="mobileSidebarOpen = false"></div>

<aside x-data="{ 
        open: true, 
        vehicleMenu: {{ request()->routeIs('dashboard', 'vehicles.*', 'conductores.*', 'requests.*', 'admin.returns.*') ? 'true' : 'false' }}, 
        roomMenu: {{ request()->routeIs('rooms.*', 'reservations.*') ? 'true' : 'false' }},
        assetMenu: {{ request()->routeIs('assets.*', 'workers.*') ? 'true' : 'false' }}
    }" :class="{
        'w-64': open, 
        'w-20': !open,
        '-translate-x-full': !mobileSidebarOpen,
        'translate-x-0': mobileSidebarOpen
    }"
    class="fixed inset-y-0 left-0 z-50 flex-shrink-0 h-screen bg-gray-900 border-r border-gray-800 transition-all duration-300 ease-in-out flex flex-col pt-0 md:sticky md:top-0 md:translate-x-0">

    <div class="h-16 flex items-center justify-between px-4 bg-gray-900 border-b border-gray-800">
        <div class="flex items-center space-x-2" :class="{'justify-center w-full': !open}">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/dimak-logo.png') }}" alt="Dimak Logo"
                    class="object-contain transition-all duration-300" :class="open ? 'h-10' : 'h-8'" />
            </a>
        </div>
        <button @click="open = !open" x-show="open" class="text-gray-400 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <div x-show="!open" class="flex justify-center py-4 border-b border-gray-800">
        <button @click="open = !open" class="text-gray-400 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-700">

        @if(Auth::user()->role === 'admin')
            <a href="{{ route('users.index') }}" wire:navigate
                class="flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-800 hover:text-white group"
                :class="{'justify-center': !open, 'bg-gray-800 text-white': {{ request()->routeIs('users.*') ? 'true' : 'false' }}}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="open" class="ml-3 whitespace-nowrap" x-transition:enter="delay-75">Usuarios</span>
            </a>
        @endif

        @if(Auth::user()->hasModuleAccess('vehicles'))
            <div>
                <button
                    @click="if(!open) { open = true; setTimeout(() => vehicleMenu = true, 100); } else { vehicleMenu = !vehicleMenu; }"
                    class="w-full flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-800 hover:text-white group focus:outline-none justify-between"
                    :class="{'justify-center': !open, 'bg-gray-800 text-white': vehicleMenu}">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                        </svg>
                        <span x-show="open" class="ml-3 whitespace-nowrap font-medium" x-transition:enter="delay-75">Módulo
                            Vehículos</span>
                    </div>
                    <svg x-show="open" class="w-4 h-4 transition-transform duration-200" :class="{'rotate-90': vehicleMenu}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open && vehicleMenu" x-collapse
                    class="space-y-1 bg-gray-800/50 mt-1 rounded-md overflow-hidden">

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'viewer']))
                        <a href="{{ route('dashboard') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('dashboard') ? "'text-white bg-gray-800'" : "''" }}">
                            Dashboard
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor']))
                        <a href="{{ route('vehicles.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('vehicles.*') ? "'text-white bg-gray-800'" : "''" }}">
                            Gestión de Vehículos
                        </a>
                        <a href="{{ route('conductores.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('conductores.*') ? "'text-white bg-gray-800'" : "''" }}">
                            Conductores
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'worker']))
                        <a href="{{ route('requests.create') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('requests.create') ? "'text-white bg-gray-800'" : "''" }}">
                            Solicitar Vehículo
                        </a>
                        <a href="{{ route('requests.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('requests.index') ? "'text-white bg-gray-800'" : "''" }}">
                            Mis Reservas
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin', 'secretaria', 'supervisor']))
                        <a href="{{ route('external-people.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('external-people.*') ? "'text-white bg-gray-800'" : "''" }}">
                            Personas Externas
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @if(Auth::user()->hasModuleAccess('rooms'))
            <div>
                <button
                    @click="if(!open) { open = true; setTimeout(() => roomMenu = true, 100); } else { roomMenu = !roomMenu; }"
                    class="w-full flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-800 hover:text-white group focus:outline-none justify-between"
                    :class="{'justify-center': !open, 'bg-gray-800 text-white': roomMenu}">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                        </svg>
                        <span x-show="open" class="ml-3 whitespace-nowrap font-medium" x-transition:enter="delay-75">Módulo
                            Salas</span>
                    </div>
                    <svg x-show="open" class="w-4 h-4 transition-transform duration-200" :class="{'rotate-90': roomMenu}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open && roomMenu" x-collapse class="space-y-1 bg-gray-800/50 mt-1 rounded-md overflow-hidden">

                    {{-- Todo el mundo puede ver el catálogo de salas --}}
                    <a href="{{ route('reservations.catalog') }}" wire:navigate
                        class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                        :class="{{ request()->routeIs('reservations.catalog') ? "'text-white bg-gray-800'" : "''" }}">
                        Ver Salas
                    </a>

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'worker']))
                        <a href="{{ route('reservations.my_reservations') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('reservations.my_reservations') ? "'text-white bg-gray-800'" : "''" }}">
                            Mis Reservas
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor']))

                        <a href="{{ route('reservations.create_external') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm rounded-md transition-colors duration-200 group"
                            :class="{{ request()->routeIs('reservations.create_external') ? "'text-white bg-gray-800'" : "'text-gray-400 hover:text-white hover:bg-gray-800'" }}">
                            Hacer Reserva
                        </a>

                        <a href="{{ route('rooms.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('rooms.*') ? "'text-white bg-gray-800'" : "''" }}">
                            Gestión de Salas
                        </a>

                        <a href="{{ route('rooms.history') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('rooms.history') ? "'text-white bg-gray-800'" : "''" }}">
                            Historial de Reservas
                        </a>

                        <a href="{{ route('rooms.agenda') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800 transition-colors"
                            :class="{{ request()->routeIs('rooms.agenda') ? "'text-white bg-gray-800'" : "''" }}">
                            Gestión de Reservas
                        </a>

                    @endif
                </div>
            </div>
        @endif

        <!-- Módulo Activos -->

        <!-- Sidebar del modulo de rendiciones, ver donde ponerlo entre todo esto XD
        <li>
            <a href="{{ route('rendiciones.dashboard') }}">
                    Rendiciones
            </a>
        </li>
        me sorprende que no tira nada, lo detecta como documentado, osea lo es, pero XD-->

        <!-- Módulo Activos -->
        @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'viewer']) && Auth::user()->hasModuleAccess('assets'))
            <div>
                <button
                    @click="if(!open) { open = true; setTimeout(() => assetMenu = true, 100); } else { assetMenu = !assetMenu; }"
                    class="w-full flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-800 hover:text-white group focus:outline-none justify-between"
                    :class="{'justify-center': !open, 'bg-gray-800 text-white': assetMenu}">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span x-show="open" class="ml-3 whitespace-nowrap font-medium" x-transition:enter="delay-75">Módulo
                            Activos</span>
                    </div>
                    <svg x-show="open" class="w-4 h-4 transition-transform duration-200" :class="{'rotate-90': assetMenu}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open && assetMenu" x-collapse class="space-y-1 bg-gray-800/50 mt-1 rounded-md overflow-hidden">
                    @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'viewer']))
                        <a href="{{ route('assets.dashboard') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('assets.dashboard') ? "'text-white bg-gray-800'" : "''" }}">
                            Dashboard
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor']))
                        <a href="{{ route('assets.reports.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('assets.reports.*') ? "'text-white bg-gray-800'" : "''" }}">
                            Reportes
                        </a>
                        <a href="{{ route('assets.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ (request()->routeIs('assets.*') && !request()->routeIs('assets.dashboard') && !request()->routeIs('assets.reports.*')) ? "'text-white bg-gray-800'" : "''" }}">
                            Gestión de Activos
                        </a>
                        <a href="{{ route('workers.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('workers.*') ? "'text-white bg-gray-800'" : "''" }}">
                            Trabajadores
                        </a>
                    @endif
                </div>
            </div>
        @endif



    </nav>
</aside>