<div x-cloak x-show="mobileSidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/50 md:hidden"
    @click="mobileSidebarOpen = false"></div>

<aside x-cloak x-data="{ 
        open: true, 
        vehicleMenu: {{ request()->routeIs('vehicles.*', 'conductores.*', 'requests.*', 'admin.returns.*') ? 'true' : 'false' }},
        roomMenu: {{ request()->routeIs('rooms.*', 'reservations.*') ? 'true' : 'false' }},
        assetMenu: {{ request()->routeIs('assets.*', 'workers.*') ? 'true' : 'false' }},
        renditionMenu: {{ request()->routeIs('route-plannings.*', 'renditions.index', 'renditions.create', 'renditions.show', 'renditions.history') && !request()->routeIs('renditions.approvals', 'renditions.controlling', 'renditions.finances', 'renditions.reports') ? 'true' : 'false' }},
        financesMenu: {{ request()->routeIs('renditions.approvals', 'renditions.controlling', 'renditions.finances', 'renditions.history', 'renditions.reports') ? 'true' : 'false' }}
    }" :class="{
        'w-64': open, 
        'w-20': !open,
        '-translate-x-full': !mobileSidebarOpen,
        'translate-x-0': mobileSidebarOpen
    }"
    class="fixed inset-y-0 left-0 z-50 flex-shrink-0 h-screen bg-gray-900 border-r border-gray-800 transition-all duration-300 ease-in-out flex flex-col pt-0 md:sticky md:top-0 md:translate-x-0">

    <div class="h-16 flex items-center justify-between px-4 bg-gray-900 border-b border-gray-800">
        <div class="flex flex-col items-start overflow-hidden" :class="{'items-center': !open}">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/dimak-logo.png') }}" alt="Dimak Logo"
                    class="object-contain transition-all duration-300" :class="open ? 'h-10' : 'h-8'" />
            </a>
            <!-- Version Info, LUEGO MAS ADELANTE COMBIENE CAMBIARLO A CONFIG/APP, PERO AHORA ES SEGURO DEJARLO ACA. -->
            <span x-show="open" class="mt-0.5 text-[11px] text-gray-500 tracking-wide">
                Sistema V{{ env('APP_VERSION', '1.8') }}
            </span>
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

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'viewer', 'worker', 'driver']))
                        <a href="{{ route('vehicles.dashboard') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('vehicles.dashboard') ? "'text-white bg-gray-800'" : "''" }}">
                            Panel de Vehículos
                        </a>
                    @endif

                    @if(Auth::user()->role === 'supervisor')
                        <a href="{{ route('vehicles.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('vehicles.index') ? "'text-white bg-gray-800'" : "''" }}">
                            Gestión de Vehículos
                        </a>
                        <a href="{{ route('requests.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('requests.index') ? "'text-white bg-gray-800'" : "''" }}">
                            Gestión de Solicitudes
                        </a>

                        <a href="{{ route('conductores.index') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('conductores.*') ? "'text-white bg-gray-800'" : "''" }}">
                            Conductores
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'worker', 'driver']))
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

                    @if(Auth::user()->role === 'supervisor')
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

                    @if(in_array(Auth::user()->role, ['admin', 'supervisor', 'worker', 'driver']))
                        <a href="{{ route('reservations.my_reservations') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('reservations.my_reservations') ? "'text-white bg-gray-800'" : "''" }}">
                            Mis Reservas
                        </a>
                    @endif

                    @if(Auth::user()->role === 'supervisor')

                        <a href="{{ route('reservations.create_external') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm rounded-md transition-colors duration-200 group"
                            :class="{{ request()->routeIs('reservations.create_external') ? "'text-white bg-gray-800'" : "'text-gray-400 hover:text-white hover:bg-gray-800'" }}">
                            Reserva Manual
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
                            Panel de Activos
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
                        <a href="{{ route('asset-categories.index') }}"
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800">
                            Categorías
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

        <!-- Módulo Rendiciones -->
        @if(Auth::user()->hasModuleAccess('renditions') || in_array(Auth::user()->role, ['admin']))
            <div>
                <button
                    @click="if(!open) { open = true; setTimeout(() => renditionMenu = true, 100); } else { renditionMenu = !renditionMenu; }"
                    class="w-full flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-800 hover:text-white group focus:outline-none justify-between"
                    :class="{'justify-center': !open, 'bg-gray-800 text-white': renditionMenu}">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span x-show="open" class="ml-3 whitespace-nowrap font-medium" x-transition:enter="delay-75">Módulo Rendiciones</span>
                    </div>
                    <svg x-show="open" class="w-4 h-4 transition-transform duration-200" :class="{'rotate-90': renditionMenu}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open && renditionMenu" x-collapse class="space-y-1 bg-gray-800/50 mt-1 rounded-md overflow-hidden">
                    <a href="{{ route('route-plannings.index') }}" wire:navigate
                        class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                        :class="{{ request()->routeIs('route-plannings.index') ? "'text-white bg-gray-800'" : "''" }}">
                        Mis solicitudes
                    </a>
                    <a href="{{ route('renditions.index') }}" wire:navigate
                        class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                        :class="{{ request()->routeIs('renditions.index') ? "'text-white bg-gray-800'" : "''" }}">
                        Mis rendiciones
                    </a>
                    @if(!in_array(Auth::user()->role, ['admin', 'jefatura']) && !in_array(Auth::user()->departamento, ['Finanzas', 'Controlling']))
                        <a href="{{ route('renditions.history') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('renditions.history') ? "'text-white bg-gray-800'" : "''" }}">
                            Historial
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Módulo Finanzas / Aprobaciones -->
        @if(in_array(Auth::user()->role, ['admin', 'jefatura']) || in_array(Auth::user()->departamento, ['Finanzas', 'Controlling']))
            <div>
                <button
                    @click="if(!open) { open = true; setTimeout(() => financesMenu = true, 100); } else { financesMenu = !financesMenu; }"
                    class="w-full flex items-center px-2 py-2 text-gray-300 rounded-md hover:bg-gray-800 hover:text-white group focus:outline-none justify-between"
                    :class="{'justify-center': !open, 'bg-gray-800 text-white': financesMenu}">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="open" class="ml-3 whitespace-nowrap font-medium" x-transition:enter="delay-75">Módulo Finanzas</span>
                    </div>
                    <svg x-show="open" class="w-4 h-4 transition-transform duration-200" :class="{'rotate-90': financesMenu}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div x-show="open && financesMenu" x-collapse class="space-y-1 bg-gray-800/50 mt-1 rounded-md overflow-hidden">
                    @if(in_array(Auth::user()->role, ['admin', 'jefatura']))
                        <a href="{{ route('renditions.approvals') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('renditions.approvals') ? "'text-white bg-gray-800'" : "''" }}">
                            Aprobaciones jefatura
                        </a>
                    @endif
                    
                    @if(in_array(Auth::user()->role, ['admin']) || Auth::user()->departamento === 'Controlling')
                        <a href="{{ route('renditions.controlling') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('renditions.controlling') ? "'text-white bg-gray-800'" : "''" }}">
                            Panel Controlling
                        </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['admin']) || Auth::user()->departamento === 'Finanzas')
                        <a href="{{ route('renditions.finances') }}" wire:navigate
                            class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                            :class="{{ request()->routeIs('renditions.finances') ? "'text-white bg-gray-800'" : "''" }}">
                            Panel Finanzas
                        </a>
                    @endif
                    
                    <a href="{{ route('renditions.history') }}" wire:navigate
                        class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                        :class="{{ request()->routeIs('renditions.history') ? "'text-white bg-gray-800'" : "''" }}">
                        Historial
                    </a>
                    <a href="{{ route('renditions.reports') }}" wire:navigate
                        class="flex items-center pl-11 pr-2 py-2 text-sm text-gray-400 rounded-md hover:text-white hover:bg-gray-800"
                        :class="{{ request()->routeIs('renditions.reports') ? "'text-white bg-gray-800'" : "''" }}">
                        Reportes Rendiciones
                    </a>
                </div>
            </div>
        @endif

    </nav>
</aside>
