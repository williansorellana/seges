<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Vehículos') }}
            </h2>
            <div class="flex flex-wrap gap-2 items-center">
                
                <!-- Search Button (Mobile) / Input (Desktop) are handled in the table section layout to be cleaner -->
                
                <a href="{{ route('vehicles.trash') }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    {{ __('Papelera') }}
                </a>
                <!-- Botón Solicitudes Pendientes -->
                <div class="relative">
                    <button x-data="" @click="$dispatch('open-modal', 'maintenance-requests-modal')"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 relative h-9">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        Solicitudes
                        @if($pendingRequests->count() > 0 || (isset($pendingReservations) && $pendingReservations->count() > 0))
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-[10px] items-center justify-center font-bold">
                                    {{ $pendingRequests->count() + ($pendingReservations ?? collect())->count() }}
                                </span>
                            </span>
                        @endif
                    </button>
                </div>

                <!-- Botón Agregar Vehículo -->
                <button x-data="" @click="$dispatch('open-modal', 'create-vehicle-modal')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                     {{ __('Nuevo') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12"
    <div class="py-12"
        x-data="{ 
            openModal: {{ $errors->any() ? 'true' : 'false' }}, 
            deleteAction: '', 
            editingVehicle: {}, 
            editAction: '', 
            viewingVehicle: {}, 
            maintenanceVehicle: {}, 
            viewingUser: null,
            filtersOpen: false,
            searchQuery: '{{ request('search', '') }}',
            getDaysRemaining(dateStr) {
                if (!dateStr) return null;
                // Crear fechas en zona horaria local para evitar errores de dia anterior
                const parts = dateStr.split('T')[0].split('-'); // Asegurar remover parte de tiempo
                const target = new Date(parts[0], parts[1] - 1, parts[2]); 
                const today = new Date();
                today.setHours(0,0,0,0);
                const diff = target - today;
                return Math.ceil(diff / (1000 * 60 * 60 * 24));
            },
            hasExpiredDocs(documents) {
                if (!documents || !Array.isArray(documents)) return false;
                const today = new Date();
                today.setHours(0,0,0,0);
                return documents.some(doc => {
                    if (!doc.expires_at) return false;
                    const parts = doc.expires_at.split('T')[0].split('-');
                    const target = new Date(parts[0], parts[1] - 1, parts[2]);
                    return target < today;
                });
            }
        }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">



            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center">
                <div class="relative w-full sm:max-w-md">
                    <input type="text" x-model="searchQuery"
                        placeholder="Buscar por patente, marca o modelo..."
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-shadow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="button" @click="filtersOpen = true" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-bold text-sm transition-colors flex items-center gap-2 border border-gray-300 dark:border-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Filtros
                        @php
                            $activeFiltersCount = collect([request('status'), request('document_status'), request('maintenance_status')])->filter()->count();
                        @endphp
                        @if($activeFiltersCount > 0)
                            <span class="bg-indigo-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $activeFiltersCount }}</span>
                        @endif
                    </button>
                    
                    <template x-if="searchQuery || {{ $activeFiltersCount > 0 ? 'true' : 'false' }}">
                        <a href="{{ route('vehicles.index') }}" class="px-3 py-2 text-gray-500 hover:text-red-500 transition-colors" title="Limpiar Filtros">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Filter Sidebar (Off-canvas) -->
            <div x-show="filtersOpen" class="fixed inset-0 z-50 flex justify-end" style="display: none;">
                <!-- Backdrop -->
                <div @click="filtersOpen = false" 
                    x-show="filtersOpen"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <!-- Sidebar Content -->
                <div x-show="filtersOpen"
                    x-transition:enter="transition transform ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition transform ease-in duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="relative w-80 bg-white dark:bg-gray-800 h-full shadow-2xl p-6 overflow-y-auto border-l border-gray-700">
                    
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            Filtros
                        </h3>
                        <button @click="filtersOpen = false" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form method="GET" action="{{ route('vehicles.index') }}">
                        <input type="hidden" name="search" :value="searchQuery">

                        <!-- Filter: Status -->
                        <div class="mb-4 border-b border-gray-700 pb-4" x-data="{ open: {{ request('status') ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left text-sm font-bold text-gray-400 uppercase tracking-wider mb-2 focus:outline-none">
                                <span>Estado</span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-collapse style="display: none;" class="space-y-2 mt-2">
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('status') === 'available' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="status" value="available" class="hidden" {{ request('status') === 'available' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                                    <span class="text-gray-200">Disponible</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('status') === 'occupied' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="status" value="occupied" class="hidden" {{ request('status') === 'occupied' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                                    <span class="text-gray-200">Reservado</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('status') === 'maintenance' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="status" value="maintenance" class="hidden" {{ request('status') === 'maintenance' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-yellow-500 shadow-[0_0_10px_rgba(234,179,8,0.5)]"></span>
                                    <span class="text-gray-200">Mantenimiento</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('status') === 'out_of_service' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="status" value="out_of_service" class="hidden" {{ request('status') === 'out_of_service' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                                    <span class="text-gray-200">Fuera de Servicio</span>
                                </label>
                            </div>
                        </div>

                        <!-- Filter: Document Status -->
                        <div class="mb-4 border-b border-gray-700 pb-4" x-data="{ open: {{ request('document_status') ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left text-sm font-bold text-gray-400 uppercase tracking-wider mb-2 focus:outline-none">
                                <span>Estado Documentos</span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-collapse style="display: none;" class="space-y-2 mt-2">
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('document_status') === 'up_to_date' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="document_status" value="up_to_date" class="hidden" {{ request('document_status') === 'up_to_date' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                                    <span class="text-gray-200">Al Día</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('document_status') === 'expired' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="document_status" value="expired" class="hidden" {{ request('document_status') === 'expired' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                                    <span class="text-gray-200">Atrasados</span>
                                </label>
                            </div>
                        </div>

                         <!-- Filter: Maintenance Status -->
                         <div class="mb-4 border-b border-gray-700 pb-4 border-none" x-data="{ open: {{ request('maintenance_status') ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full text-left text-sm font-bold text-gray-400 uppercase tracking-wider mb-2 focus:outline-none">
                                <span>Estado Mantención</span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-collapse style="display: none;" class="space-y-2 mt-2">
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('maintenance_status') === 'ok' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="maintenance_status" value="ok" class="hidden" {{ request('maintenance_status') === 'ok' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                                    <span class="text-gray-200">Al Día</span>
                                </label>
                                <label class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('maintenance_status') === 'needed' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="maintenance_status" value="needed" class="hidden" {{ request('maintenance_status') === 'needed' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="w-3 h-3 rounded-full bg-yellow-500 shadow-[0_0_10px_rgba(234,179,8,0.5)]"></span>
                                    <span class="text-gray-200">Requiere Mantención</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-700 flex flex-col gap-3">
                            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-all shadow-lg shadow-indigo-500/30">
                                Aplicar Filtros
                            </button>
                            @if(request('status'))
                                <a href="{{ route('vehicles.index', ['search' => request('search')]) }}" class="w-full py-3 text-center text-gray-400 hover:text-white font-medium hover:bg-gray-700 rounded-lg transition-colors">
                                    Limpiar Estado
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead class="bg-gray-800 text-gray-300">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Foto
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Patente
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Marca / Modelo
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Año
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Estado Doc.
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Kilometraje
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-900 text-gray-300">
                                @forelse($vehicles as $vehicle)
                                    <tr class="hover:bg-gray-800 transition duration-150"
                                        data-search="{{ strtolower($vehicle->plate . ' ' . $vehicle->brand . ' ' . $vehicle->model) }}"
                                        x-show="!searchQuery || $el.dataset.search.split(' ').some(word => word.startsWith(searchQuery.toLowerCase()))">
                                        <td class="px-5 py-4 text-sm">
                                            @if($vehicle->image_path)
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-600"
                                                        src="{{ Storage::url($vehicle->image_path) }}"
                                                        alt="{{ $vehicle->plate }}">
                                                </div>
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-400 border border-gray-600">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm font-bold">
                                            {{ $vehicle->plate }}
                                        </td>
                                        <td class="px-5 py-4 text-sm">
                                            {{ $vehicle->brand }} {{ $vehicle->model }}
                                        </td>
                                        <td class="px-5 py-4 text-sm">
                                            {{ $vehicle->year }}
                                        </td>
                                            <td class="px-5 py-4 text-sm">
                                            @php
                                                $displayStatus = $vehicle->display_status;
                                                $statusClasses = [
                                                    'available' => 'text-green-400 bg-green-900/30 border border-green-900',
                                                    'out_of_service' => 'text-red-400 bg-red-900/30 border border-red-900',
                                                    'maintenance' => 'text-yellow-400 bg-yellow-900/30 border border-yellow-900',
                                                    'occupied' => 'text-blue-400 bg-blue-900/30 border border-blue-900',
                                                ];
                                                $statusLabel = [
                                                    'available' => 'DISPONIBLE',
                                                    'out_of_service' => 'FUERA DE SERVICIO',
                                                    'maintenance' => 'MANTENIMIENTO',
                                                    'occupied' => 'RESERVADO',
                                                ];
                                            @endphp
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md {{ $statusClasses[$displayStatus] ?? 'text-gray-400 bg-gray-800' }}">
                                                {{ $statusLabel[$displayStatus] ?? strtoupper($displayStatus) }}
                                            </span>
                                            @if($displayStatus === 'occupied' && $vehicle->active_reservation)
                                                <div class="text-[10px] text-blue-300 mt-1">
                                                    Por: {{ $vehicle->active_reservation->user->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm">
                                            @if($vehicle->hasExpiredDocuments())
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md text-red-400 bg-red-900/30 border border-red-900">
                                                    ATRASADO
                                                </span>
                                            @else
                                                 <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md text-green-400 bg-green-900/30 border border-green-900">
                                                    AL DÍA
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm">
                                            <div class="flex items-center">
                                                <p class="text-gray-900 dark:text-gray-100 whitespace-no-wrap mr-2">
                                                    {{ number_format($vehicle->mileage, 0, '', '.') }} km
                                                </p>
                                                @if($vehicle->currentMaintenanceState && $vehicle->currentMaintenanceState->next_oil_change_km)
                                                    @if($vehicle->mileage >= $vehicle->currentMaintenanceState->next_oil_change_km)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 animate-pulse">
                                                            ⚠️ VENCIDO
                                                        </span>
                                                    @elseif(($vehicle->currentMaintenanceState->next_oil_change_km - $vehicle->mileage) <= 500)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                            ⚠️ PRÓXIMO
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-sm font-medium">
                                            @php
                                                $soap = $vehicle->documents->firstWhere('type', 'insurance');
                                                $permit = $vehicle->documents->firstWhere('type', 'permit');
                                                $technical = $vehicle->documents->firstWhere('type', 'technical_review');
                                                
                                                $jsVehicle = [
                                                    'id' => $vehicle->id,
                                                    'plate' => $vehicle->plate,
                                                    'serial_number' => $vehicle->serial_number,
                                                    'brand' => $vehicle->brand,
                                                    'model' => $vehicle->model,
                                                    'year' => $vehicle->year,
                                                    'mileage' => $vehicle->mileage,
                                                    'status' => $vehicle->status,
                                                    'image_url' => $vehicle->image_path ? Storage::url($vehicle->image_path) : '',
                                                    'imageUrl' => $vehicle->image_path ? Storage::url($vehicle->image_path) : '', // Duplicate for compatibility if needed
                                                    'documents' => $vehicle->documents,
                                                    'soap_expires_at' => $soap?->expires_at?->format('Y-m-d') ?? '',
                                                    'permit_expires_at' => $permit?->expires_at?->format('Y-m-d') ?? '',
                                                    'technical_expires_at' => $technical?->expires_at?->format('Y-m-d') ?? '',
                                                    'technical_expires_at' => $technical?->expires_at?->format('Y-m-d') ?? '',
                                                    'assigned_user' => ($vehicle->display_status === 'occupied' && $vehicle->effective_reservation) ? $vehicle->effective_reservation->user->name : '',
                                                    'assigned_user_rut' => ($vehicle->display_status === 'occupied' && $vehicle->effective_reservation) ? $vehicle->effective_reservation->user->rut : '',
                                                    'assigned_user_phone' => ($vehicle->display_status === 'occupied' && $vehicle->effective_reservation) ? $vehicle->effective_reservation->user->phone : '',
                                                    'fuel_type' => $vehicle->fuel_type,
                                                    'reservation' => ($vehicle->display_status === 'occupied' && $vehicle->effective_reservation) ? [
                                                        'start_date' => $vehicle->effective_reservation->start_date->format('Y-m-d H:i'),
                                                        'end_date' => $vehicle->effective_reservation->end_date->format('Y-m-d H:i'),
                                                        'destination_type' => $vehicle->effective_reservation->destination_type,
                                                        'user_name' => $vehicle->effective_reservation->user->name,
                                                        'user_email' => $vehicle->effective_reservation->user->email,
                                                        'conductor_name' => $vehicle->effective_reservation->conductor ? $vehicle->effective_reservation->conductor->name : 'Solicitante',
                                                        'days_remaining' => (int) ceil(now()->floatDiffInDays($vehicle->effective_reservation->end_date, false)),
                                                    ] : null,
                                                ];
                                                $jsonVehicle = json_encode($jsVehicle);
                                            @endphp
                                            <div class="flex items-center space-x-4">
                                                <!-- Botón Mantenimiento -->
                                                <button @click="
                                                                maintenanceVehicle = {
                                                                    id: {{ $vehicle->id }},
                                                                    status: '{{ $vehicle->status }}',
                                                                    updateStateAction: '{{ route('vehicles.maintenance.state', $vehicle) }}',
                                                                    storeRequestAction: '{{ route('vehicles.maintenance.request', $vehicle) }}',
                                                                    completeAction: '{{ route('vehicles.maintenance.complete', $vehicle) }}',
                                                                    last_oil_change_km: '{{ isset($vehicle->currentMaintenanceState->last_oil_change_km) ? number_format($vehicle->currentMaintenanceState->last_oil_change_km, 0, '', '.') : '' }}',
                                                                    next_oil_change_km: '{{ isset($vehicle->currentMaintenanceState->next_oil_change_km) ? number_format($vehicle->currentMaintenanceState->next_oil_change_km, 0, '', '.') : '' }}',
                                                                    tire_status_front: '{{ $vehicle->currentMaintenanceState->tire_status_front ?? 'good' }}',
                                                                    tire_status_rear: '{{ $vehicle->currentMaintenanceState->tire_status_rear ?? 'good' }}',
                                                                    last_service_date: '{{ $vehicle->currentMaintenanceState->last_service_date ?? '' }}',
                                                                    oil_change_due: {{ ($vehicle->currentMaintenanceState && $vehicle->currentMaintenanceState->next_oil_change_km && $vehicle->mileage >= $vehicle->currentMaintenanceState->next_oil_change_km) ? 'true' : 'false' }}
                                                                };
                                                                $dispatch('open-modal', 'maintenance-vehicle-modal');
                                                            "
                                                    class="{{ ($vehicle->currentMaintenanceState && $vehicle->currentMaintenanceState->next_oil_change_km && $vehicle->mileage >= $vehicle->currentMaintenanceState->next_oil_change_km) ? 'text-red-500 hover:text-red-400 animate-pulse' : 'text-yellow-400 hover:text-yellow-300' }} transition duration-150"
                                                    title="Mantenimiento">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Ver Detalle (General) -->
                                                <button @click="
                                                                viewingVehicle = {{ $jsonVehicle }};
                                                                $dispatch('open-modal', 'view-vehicle-modal');
                                                            "
                                                    class="text-green-400 hover:text-green-300 transition duration-150"
                                                    title="Ver Ficha Vehículo">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <!-- Ver Reserva (Solo si Ocupado) -->
                                                @if($displayStatus === 'occupied' && $vehicle->effective_reservation)
                                                <button @click="
                                                            viewingVehicle = {{ $jsonVehicle }};
                                                            $dispatch('open-modal', 'reservation-detail-modal');
                                                        "
                                                    class="text-indigo-400 hover:text-indigo-300 transition duration-150"
                                                    title="Ver Reserva Activa">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                                @endif
                                                <a href="{{ route('fuel-loads.index', ['vehicle_id' => $vehicle->id]) }}"
                                                    class="text-orange-500 hover:text-orange-400 transition duration-150"
                                                    title="Historial de Combustible">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </a>
                                                <button @click="
                                                    editingVehicle = {{ $jsonVehicle }};
                                                    editAction = '{{ route('vehicles.update', $vehicle->id) }}';
                                                    $dispatch('open-modal', 'edit-vehicle-modal');
                                                "
                                                    class="text-blue-400 hover:text-blue-300 transition duration-150"
                                                    title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                    <button
                                                        @click="$dispatch('open-modal', 'confirm-delete-modal'); deleteAction = '{{ route('vehicles.destroy', $vehicle) }}'"
                                                        class="text-red-400 hover:text-red-300 transition duration-150"
                                                        title="Eliminar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-5 text-sm text-center text-gray-500">
                                            No hay vehículos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Agregar Vehículo -->
        <x-modal name="create-vehicle-modal" :show="$errors->any()" focusable>
            <form method="POST" action="{{ route('vehicles.store') }}" class="p-6 bg-gray-800 text-gray-100"
                enctype="multipart/form-data"
                x-data="{
                    photoName: null,
                    photoPreview: null,
                    isCompressing: false,
                    async compressImage(file) {
                        this.isCompressing = true;
                        return new Promise((resolve) => {
                            const reader = new FileReader();
                            reader.readAsDataURL(file);
                            reader.onload = (event) => {
                                const img = new Image();
                                img.src = event.target.result;
                                img.onload = () => {
                                    const canvas = document.createElement('canvas');
                                    const ctx = canvas.getContext('2d');
                                    const MAX_WIDTH = 1920;
                                    let width = img.width;
                                    let height = img.height;

                                    if (width > MAX_WIDTH) {
                                        height *= MAX_WIDTH / width;
                                        width = MAX_WIDTH;
                                    }

                                    canvas.width = width;
                                    canvas.height = height;
                                    ctx.drawImage(img, 0, 0, width, height);

                                    canvas.toBlob((blob) => {
                                        const compressedFile = new File([blob], file.name, {
                                            type: 'image/jpeg',
                                            lastModified: Date.now(),
                                        });
                                        this.isCompressing = false;
                                        resolve(compressedFile);
                                    }, 'image/jpeg', 0.8);
                                };
                            };
                        });
                    }
                }">
                @csrf

                <h2 class="text-lg font-medium text-gray-100 mb-4">
                    {{ __('Nuevo Vehículo') }}
                </h2>

                <!-- Foto con Compresión -->
                <div class="mb-4 bg-gray-900 border border-gray-700 rounded-lg p-4">
                    <x-input-label for="image" :value="__('Foto del Vehículo')" class="text-gray-300 mb-2" />
                    
                    <!-- Preview -->
                    <div class="mt-2 mb-4" x-show="photoPreview" style="display: none;">
                        <span class="block rounded-md w-full h-40 bg-cover bg-no-repeat bg-center mx-auto border border-gray-600"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                         <div x-show="!photoPreview" class="text-xs text-gray-500 italic">
                            Sin foto seleccionada
                        </div>
                        
                        <button type="button" x-on:click.prevent="$refs.photo.click()" :disabled="isCompressing"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-600 disabled:opacity-25 transition">
                            <span x-show="!isCompressing">{{ __('Seleccionar Foto') }}</span>
                            <span x-show="isCompressing">{{ __('Procesando...') }}</span>
                        </button>
                    </div>

                    <input id="image" type="file" name="image" class="hidden" x-ref="photo" accept="image/*" 
                        x-on:change="
                                const file = $refs.photo.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL(file);

                                    compressImage(file).then(compressedFile => {
                                        const dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(compressedFile);
                                        $refs.photo.files = dataTransfer.files;
                                    });
                                }
                            " />
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Patente -->
                    <div>
                        <x-input-label for="plate" :value="__('Patente')" class="text-gray-300" />
                        <x-text-input id="plate"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="plate" :value="old('plate')" required autofocus
                            placeholder="Ej: AB123CD" />
                        <x-input-error :messages="$errors->get('plate')" class="mt-2" />
                    </div>

                    <!-- Nº Serie/Chasis -->
                    <div>
                        <x-input-label for="serial_number" :value="__('Nº Serie/Chasis')" class="text-gray-300" />
                        <x-text-input id="serial_number"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="serial_number" :value="old('serial_number')" placeholder="Opcional" />
                        <x-input-error :messages="$errors->get('serial_number')" class="mt-2" />
                    </div>

                    <!-- Marca -->
                    <div>
                        <x-input-label for="brand" :value="__('Marca')" class="text-gray-300" />
                        <x-text-input id="brand"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="brand" :value="old('brand')" required placeholder="Toyota" />
                        <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                    </div>

                    <!-- Modelo -->
                    <div>
                        <x-input-label for="model" :value="__('Modelo')" class="text-gray-300" />
                        <x-text-input id="model"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="model" :value="old('model')" required placeholder="Hilux" />
                        <x-input-error :messages="$errors->get('model')" class="mt-2" />
                    </div>

                    <!-- Año -->
                    <div>
                        <x-input-label for="year" :value="__('Año')" class="text-gray-300" />
                        <x-text-input id="year"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="number" name="year" :value="old('year')" required placeholder="2023" />
                        <x-input-error :messages="$errors->get('year')" class="mt-2" />
                    </div>

                    <!-- Kilometraje -->
                    <div class="md:col-span-2">
                        <x-input-label for="mileage" :value="__('Kilometraje')" class="text-gray-300" />
                        <x-text-input id="mileage"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="mileage" :value="old('mileage')" required placeholder="0" 
                            x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" />
                        <x-input-error :messages="$errors->get('mileage')" class="mt-2" />
                    </div>

                    <!-- Tipo de Combustible -->
                    <div>
                        <x-input-label for="fuel_type" :value="__('Tipo de Combustible')" class="text-gray-300" />
                        <select id="fuel_type" name="fuel_type"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="diesel">Petróleo (Diesel)</option>
                            <option value="gasoline">Bencina (Gasolina)</option>
                        </select>
                        <x-input-error :messages="$errors->get('fuel_type')" class="mt-2" />
                    </div>

                    <!-- Documentación Inicial -->
                    <div class="md:col-span-2 border-t border-gray-700 pt-4 mt-2">
                        <h3 class="text-sm font-bold text-blue-400 mb-3 uppercase">Documentación Inicial (Opcional)</h3>
                        
                        <!-- Seguro (SOAP) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="soap_file" :value="__('Seguro Obligatorio (SOAP)')" class="text-gray-400" />
                                <input id="soap_file" type="file" name="soap_file" accept=".pdf,image/*" 
                                    class="block w-full text-sm text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md mt-1" />
                            </div>
                            <div>
                                <x-input-label for="soap_expires" :value="__('Vencimiento SOAP')" class="text-gray-400" />
                                <x-text-input id="soap_expires" class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="date" name="soap_expires_at" />
                            </div>
                        </div>

                        <!-- Permiso Circulación -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="permit_file" :value="__('Permiso Circulación')" class="text-gray-400" />
                                <input id="permit_file" type="file" name="permit_file" accept=".pdf,image/*" 
                                    class="block w-full text-sm text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md mt-1" />
                            </div>
                            <div>
                                <x-input-label for="permit_expires" :value="__('Vencimiento Permiso')" class="text-gray-400" />
                                <x-text-input id="permit_expires" class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="date" name="permit_expires_at" />
                            </div>
                        </div>

                        <!-- Revisión Técnica -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="technical_file" :value="__('Revisión Técnica')" class="text-gray-400" />
                                <input id="technical_file" type="file" name="technical_file" accept=".pdf,image/*" 
                                    class="block w-full text-sm text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md mt-1" />
                            </div>
                            <div>
                                <x-input-label for="technical_expires" :value="__('Vencimiento Revisión')" class="text-gray-400" />
                                <x-text-input id="technical_expires" class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="date" name="technical_expires_at" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 border-transparent">
                        {{ __('Guardar Vehículo') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Confirmación Eliminar -->
        <x-modal name="confirm-delete-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100">
                <h2 class="text-lg font-medium text-gray-100">
                    {{ __('¿Estás seguro?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    {{ __('El vehículo se moverá a la papelera. Podrás restaurarlo después si lo necesitas.') }}
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button @click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <form method="POST" :action="deleteAction">
                        @csrf
                        @method('DELETE')
                        <x-danger-button class="ml-3">
                            {{ __('Sí, enviar a la papelera') }}
                        </x-danger-button>
                    </form>
                </div>
            </div>
        </x-modal>

        <!-- Modal Editar Vehículo -->
        <x-modal name="edit-vehicle-modal" :show="false" focusable>
            <form method="POST" :action="editAction" enctype="multipart/form-data"
                class="p-6 bg-gray-800 text-gray-100">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-medium text-gray-100 mb-4">
                    {{ __('Editar Vehículo') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Patente -->
                    <div>
                        <x-input-label for="edit_plate" :value="__('Patente')" class="text-gray-300" />
                        <x-text-input id="edit_plate"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="plate" x-model="editingVehicle.plate" required placeholder="AA123BB" />
                        <x-input-error :messages="$errors->get('plate')" class="mt-2" />
                    </div>

                    <!-- Nº Serie -->
                    <div>
                        <x-input-label for="edit_serial_number" :value="__('Nº Serie/Chasis')" class="text-gray-300" />
                        <x-text-input id="edit_serial_number"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="serial_number" x-model="editingVehicle.serial_number" placeholder="Opcional" />
                        <x-input-error :messages="$errors->get('serial_number')" class="mt-2" />
                    </div>

                    <!-- Marca -->
                    <div>
                        <x-input-label for="edit_brand" :value="__('Marca')" class="text-gray-300" />
                        <x-text-input id="edit_brand"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="brand" x-model="editingVehicle.brand" required placeholder="Toyota" />
                        <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                    </div>

                    <!-- Modelo -->
                    <div>
                        <x-input-label for="edit_model" :value="__('Modelo')" class="text-gray-300" />
                        <x-text-input id="edit_model"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="model" x-model="editingVehicle.model" required placeholder="Hilux" />
                        <x-input-error :messages="$errors->get('model')" class="mt-2" />
                    </div>

                    <!-- Año -->
                    <div>
                        <x-input-label for="edit_year" :value="__('Año')" class="text-gray-300" />
                        <x-text-input id="edit_year"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="number" name="year" x-model="editingVehicle.year" required placeholder="2023" />
                        <x-input-error :messages="$errors->get('year')" class="mt-2" />
                    </div>

                    <!-- Kilometraje -->
                    <div class="md:col-span-2">
                        <x-input-label for="edit_mileage" :value="__('Kilometraje')" class="text-gray-300" />
                        <x-text-input id="edit_mileage"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="mileage" x-model="editingVehicle.mileage" required placeholder="0" 
                            x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" />
                        <x-input-error :messages="$errors->get('mileage')" class="mt-2" />
                    </div>

                    <!-- Tipo de Combustible (Editar) -->
                    <div>
                        <x-input-label for="edit_fuel_type" :value="__('Tipo de Combustible')" class="text-gray-300" />
                        <select id="edit_fuel_type" name="fuel_type" x-model="editingVehicle.fuel_type"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="diesel">Petróleo (Diesel)</option>
                            <option value="gasoline">Bencina (Gasolina)</option>
                        </select>
                        <x-input-error :messages="$errors->get('fuel_type')" class="mt-2" />
                    </div>

                    <!-- Estado -->
                    <div class="md:col-span-2">
                        <x-input-label for="edit_status" :value="__('Estado')" class="text-gray-300" />
                        <template x-if="editingVehicle.status !== 'occupied'">
                            <select id="edit_status" name="status" x-model="editingVehicle.status"
                                class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="available">{{ __('Disponible') }}</option>
                                <option value="out_of_service">{{ __('Fuera de Servicio') }}</option>
                                <option value="maintenance">{{ __('En Mantenimiento') }}</option>
                            </select>
                        </template>
                        <template x-if="editingVehicle.status === 'occupied'">
                            <div>
                                <input type="hidden" name="status" value="occupied">
                                <div class="block mt-1 w-full bg-blue-900/20 border border-blue-500/50 text-blue-200 rounded-md shadow-sm p-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span class="font-bold">RESERVADO (En Uso)</span>
                                </div>
                                <p class="mt-1 text-xs text-blue-400">
                                    El estado no se puede editar manualmente mientras el vehículo está en uso. 
                                    Debe finalizar la reserva para liberarlo.
                                </p>
                            </div>
                        </template>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <!-- Foto (Opcional) -->
                    <div class="md:col-span-2">
                        <x-input-label for="edit_image" :value="__('Actualizar Foto (Opcional)')"
                            class="text-gray-300" />
                        <input id="edit_image" type="file" name="image" accept="image/*"
                            class="block w-full text-sm text-gray-400
                             file:mr-4 file:py-2 file:px-4
                             file:rounded-md file:border-0
                             file:text-sm file:font-semibold
                             file:bg-blue-600 file:text-white
                             hover:file:bg-blue-700
                             cursor-pointer bg-gray-900 border border-gray-700 rounded-md focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                        <p class="mt-1 text-xs text-gray-500">Dejar vacío para mantener la actual.</p>
                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    </div>

                    <!-- Documentación -->
                    <div class="md:col-span-2 border-t border-gray-700 pt-4 mt-2">
                        <h3 class="text-sm font-bold text-blue-400 mb-3 uppercase">Actualizar Documentación</h3>
                        
                        <!-- Seguro (SOAP) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="edit_soap_file" :value="__('Seguro Obligatorio (SOAP)')" class="text-gray-400" />
                                <input id="edit_soap_file" type="file" name="soap_file" accept=".pdf,image/*" 
                                    class="block w-full text-sm text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit_soap_expires" :value="__('Vencimiento SOAP')" class="text-gray-400" 
                                    ::class="{
                                        'text-red-500 font-bold': getDaysRemaining(editingVehicle.soap_expires_at) !== null && getDaysRemaining(editingVehicle.soap_expires_at) < 0,
                                        'text-yellow-500 font-bold': getDaysRemaining(editingVehicle.soap_expires_at) !== null && getDaysRemaining(editingVehicle.soap_expires_at) >= 0 && getDaysRemaining(editingVehicle.soap_expires_at) <= 7
                                    }" />
                                <x-text-input id="edit_soap_expires" class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" 
                                    ::class="{
                                        'border-red-500 focus:border-red-500 ring-1 ring-red-500': getDaysRemaining(editingVehicle.soap_expires_at) !== null && getDaysRemaining(editingVehicle.soap_expires_at) < 0,
                                        'border-yellow-500 focus:border-yellow-500 ring-1 ring-yellow-500': getDaysRemaining(editingVehicle.soap_expires_at) !== null && getDaysRemaining(editingVehicle.soap_expires_at) >= 0 && getDaysRemaining(editingVehicle.soap_expires_at) <= 7
                                    }" 
                                    type="date" name="soap_expires_at" x-model="editingVehicle.soap_expires_at" />
                                <template x-if="getDaysRemaining(editingVehicle.soap_expires_at) !== null">
                                    <div>
                                        <span x-show="getDaysRemaining(editingVehicle.soap_expires_at) < 0" class="text-xs text-red-500 font-bold mt-1 block">⚠️ VENCIDO</span>
                                        <span x-show="getDaysRemaining(editingVehicle.soap_expires_at) >= 0 && getDaysRemaining(editingVehicle.soap_expires_at) <= 7" class="text-xs text-yellow-500 font-bold mt-1 block" x-text="'⚠️ Faltan ' + getDaysRemaining(editingVehicle.soap_expires_at) + ' días'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Permiso Circulación -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="edit_permit_file" :value="__('Permiso Circulación')" class="text-gray-400" />
                                <input id="edit_permit_file" type="file" name="permit_file" accept=".pdf,image/*" 
                                    class="block w-full text-sm text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit_permit_expires" :value="__('Vencimiento Permiso')" class="text-gray-400" 
                                    ::class="{
                                        'text-red-500 font-bold': getDaysRemaining(editingVehicle.permit_expires_at) !== null && getDaysRemaining(editingVehicle.permit_expires_at) < 0,
                                        'text-yellow-500 font-bold': getDaysRemaining(editingVehicle.permit_expires_at) !== null && getDaysRemaining(editingVehicle.permit_expires_at) >= 0 && getDaysRemaining(editingVehicle.permit_expires_at) <= 7
                                    }" />
                                <x-text-input id="edit_permit_expires" class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" 
                                    ::class="{
                                        'border-red-500 focus:border-red-500 ring-1 ring-red-500': getDaysRemaining(editingVehicle.permit_expires_at) !== null && getDaysRemaining(editingVehicle.permit_expires_at) < 0,
                                        'border-yellow-500 focus:border-yellow-500 ring-1 ring-yellow-500': getDaysRemaining(editingVehicle.permit_expires_at) !== null && getDaysRemaining(editingVehicle.permit_expires_at) >= 0 && getDaysRemaining(editingVehicle.permit_expires_at) <= 7
                                    }"
                                    type="date" name="permit_expires_at" x-model="editingVehicle.permit_expires_at"/>
                                <template x-if="getDaysRemaining(editingVehicle.permit_expires_at) !== null">
                                    <div>
                                        <span x-show="getDaysRemaining(editingVehicle.permit_expires_at) < 0" class="text-xs text-red-500 font-bold mt-1 block">⚠️ VENCIDO</span>
                                        <span x-show="getDaysRemaining(editingVehicle.permit_expires_at) >= 0 && getDaysRemaining(editingVehicle.permit_expires_at) <= 7" class="text-xs text-yellow-500 font-bold mt-1 block" x-text="'⚠️ Faltan ' + getDaysRemaining(editingVehicle.permit_expires_at) + ' días'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Revisión Técnica -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="edit_technical_file" :value="__('Revisión Técnica')" class="text-gray-400" />
                                <input id="edit_technical_file" type="file" name="technical_file" accept=".pdf,image/*" 
                                    class="block w-full text-sm text-gray-400 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md mt-1" />
                            </div>
                            <div>
                                <x-input-label for="edit_technical_expires" :value="__('Vencimiento Revisión')" class="text-gray-400" 
                                    ::class="{
                                        'text-red-500 font-bold': getDaysRemaining(editingVehicle.technical_expires_at) !== null && getDaysRemaining(editingVehicle.technical_expires_at) < 0,
                                        'text-yellow-500 font-bold': getDaysRemaining(editingVehicle.technical_expires_at) !== null && getDaysRemaining(editingVehicle.technical_expires_at) >= 0 && getDaysRemaining(editingVehicle.technical_expires_at) <= 7
                                    }" />
                                <x-text-input id="edit_technical_expires" class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" 
                                    ::class="{
                                        'border-red-500 focus:border-red-500 ring-1 ring-red-500': getDaysRemaining(editingVehicle.technical_expires_at) !== null && getDaysRemaining(editingVehicle.technical_expires_at) < 0,
                                        'border-yellow-500 focus:border-yellow-500 ring-1 ring-yellow-500': getDaysRemaining(editingVehicle.technical_expires_at) !== null && getDaysRemaining(editingVehicle.technical_expires_at) >= 0 && getDaysRemaining(editingVehicle.technical_expires_at) <= 7
                                    }" 
                                    type="date" name="technical_expires_at" x-model="editingVehicle.technical_expires_at" />
                                <template x-if="getDaysRemaining(editingVehicle.technical_expires_at) !== null">
                                    <div>
                                        <span x-show="getDaysRemaining(editingVehicle.technical_expires_at) < 0" class="text-xs text-red-500 font-bold mt-1 block">⚠️ VENCIDO</span>
                                        <span x-show="getDaysRemaining(editingVehicle.technical_expires_at) >= 0 && getDaysRemaining(editingVehicle.technical_expires_at) <= 7" class="text-xs text-yellow-500 font-bold mt-1 block" x-text="'⚠️ Faltan ' + getDaysRemaining(editingVehicle.technical_expires_at) + ' días'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button @click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 border-transparent">
                        {{ __('Actualizar Vehículo') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Ver Detalle Vehículo -->
        <x-modal name="view-vehicle-modal" :show="false" focusable zIndex="z-[60]">
            <div class="p-6 bg-gray-800 text-gray-100">
                <h2 class="text-xl font-bold text-gray-100 mb-6 border-b border-gray-700 pb-2">
                    {{ __('Detalle del Vehículo') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Foto Grande -->
                    <div
                        class="flex flex-col items-center justify-center bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <template x-if="viewingVehicle.imageUrl">
                            <img :src="viewingVehicle.imageUrl" alt="Foto Vehículo"
                                class="w-full h-64 object-cover rounded-md shadow-lg">
                        </template>
                        <template x-if="!viewingVehicle.imageUrl">
                            <div
                                class="w-full h-64 flex items-center justify-center bg-gray-800 text-gray-500 rounded-md">
                                <span class="text-sm">Sin imagen disponible</span>
                            </div>
                        </template>
                    </div>

                    <!-- Datos -->
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-widest">Patente</span>
                            <span class="text-2xl font-bold text-white tracking-wider"
                                x-text="viewingVehicle.plate"></span>
                        </div>
                        
                        <template x-if="viewingVehicle.serial_number">
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Nº Serie/Chasis</span>
                                <span class="text-base text-gray-300 font-mono tracking-wider"
                                    x-text="viewingVehicle.serial_number"></span>
                            </div>
                        </template>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Marca</span>
                                <span class="text-lg text-gray-200" x-text="viewingVehicle.brand"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Modelo</span>
                                <span class="text-lg text-gray-200" x-text="viewingVehicle.model"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Año</span>
                                <span class="text-lg text-gray-200" x-text="viewingVehicle.year"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Kilometraje</span>
                                <span class="text-lg text-gray-200" x-text="Number(viewingVehicle.mileage).toLocaleString('es-CL') + ' km'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Combustible</span>
                                <span class="text-lg font-bold" 
                                    :class="viewingVehicle.fuel_type === 'diesel' ? 'text-yellow-400' : 'text-green-400'"
                                    x-text="viewingVehicle.fuel_type === 'diesel' ? 'PETRÓLEO' : 'BENCINA'"></span>
                            </div>
                            <div>

                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-widest mb-1">Estado</span>
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md"
                                :class="{
                                    'bg-green-900 text-green-200': viewingVehicle.status === 'available',
                                    'bg-red-900 text-red-200': viewingVehicle.status === 'out_of_service',
                                    'bg-yellow-900 text-yellow-200': viewingVehicle.status === 'maintenance',
                                    'bg-blue-900 text-blue-200': viewingVehicle.status === 'occupied'
                                }"
                                x-text="viewingVehicle.status === 'available' ? 'DISPONIBLE' : (viewingVehicle.status === 'out_of_service' ? 'FUERA DE SERVICIO' : (viewingVehicle.status === 'maintenance' ? 'MANTENIMIENTO' : 'RESERVADO'))">
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-widest mb-1">Estado Doc.</span>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md"
                                  :class="hasExpiredDocs(viewingVehicle.documents) ? 'text-red-400 bg-red-900/30 border border-red-900' : 'text-green-400 bg-green-900/30 border border-green-900'"
                                  x-text="hasExpiredDocs(viewingVehicle.documents) ? 'ATRASADO' : 'AL DÍA'">
                            </span>
                        </div>

                    </div>
                </div>
                        
                        <template x-if="viewingVehicle.status === 'occupied' && viewingVehicle.assigned_user">
                            <div class="mt-4 border-t border-gray-700 pt-3">
                                <span class="block text-xs text-blue-400 uppercase tracking-widest mb-2 font-bold">Datos de Asignación</span>
                                <div class="bg-gray-800 rounded p-3 space-y-2 border border-blue-900/30">
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-400">Nombre:</span>
                                        <span class="text-sm font-bold text-white max-w-[150px] truncate" x-text="viewingVehicle.assigned_user"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-400">RUT:</span>
                                        <span class="text-sm text-gray-200" x-text="viewingVehicle.assigned_user_rut || 'No registra'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-xs text-gray-400">Teléfono:</span>
                                        <span class="text-sm text-gray-200 font-mono" x-text="viewingVehicle.assigned_user_phone || 'No registra'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-100 mb-4 border-b border-gray-700 pb-2">Documentación</h3>
                    
                    <!-- Lista de Documentos -->
                    <div class="mb-6">
                        <template x-if="viewingVehicle.documents && viewingVehicle.documents.length > 0">
                            <ul class="space-y-2">
                                <template x-for="doc in viewingVehicle.documents" :key="doc.id">
                                    <li class="flex items-center justify-between bg-gray-900 p-3 rounded-md border border-gray-700">
                                        <div class="flex items-center">
                                            <span x-text="doc.type === 'insurance' ? '🛡️ Seguro' : (doc.type === 'technical_review' ? '🔧 Revisión Técnica' : '📄 Permiso Circulación')" 
                                                  class="text-sm font-medium text-gray-200 mr-2"></span>
                                            <span x-text="'(Vence: ' + (doc.expires_at ? doc.expires_at.split('T')[0].split('-').reverse().join('/') : '') + ')'" class="text-xs text-gray-400"></span>
                                            <span x-show="getDaysRemaining(doc.expires_at) !== null && getDaysRemaining(doc.expires_at) < 0" class="ml-2 text-xs font-bold text-red-500 bg-red-900/30 px-2 py-0.5 rounded">VENCIDO</span>
                                            <span x-show="getDaysRemaining(doc.expires_at) !== null && getDaysRemaining(doc.expires_at) >= 0 && getDaysRemaining(doc.expires_at) <= 7" class="ml-2 text-xs font-bold text-yellow-500 bg-yellow-900/30 px-2 py-0.5 rounded" x-text="'⚠️ ' + getDaysRemaining(doc.expires_at) + ' días'"></span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a :href="'/storage/' + doc.file_path" target="_blank" class="text-blue-400 hover:text-blue-300 text-sm">Ver</a>
                                            <!-- Delete button logic requires a form, might be complex in view-only modal -->
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="!viewingVehicle.documents || viewingVehicle.documents.length === 0">
                            <p class="text-gray-500 text-sm italic">No hay documentos registrados.</p>
                        </template>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <x-secondary-button @click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600 w-full md:w-auto justify-center">
                        {{ __('Cerrar') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>

        <!-- Modal Mantenimiento -->
        <x-modal name="maintenance-vehicle-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100" x-data="{ tab: 'status' }">
                <h2 class="text-xl font-bold text-gray-100 mb-6 border-b border-gray-700 pb-2">
                    {{ __('Gestión de Mantenimiento') }}
                </h2>

                <!-- Tabs Navigation -->
                <div class="flex space-x-4 mb-6 border-b border-gray-700">
                    <button @click="tab = 'status'"
                        :class="{ 'border-b-2 border-blue-500 text-blue-400': tab === 'status', 'text-gray-400 hover:text-gray-200': tab !== 'status' }"
                        class="pb-2 text-sm font-medium transition-colors duration-150">
                        Estado Técnico
                    </button>
                    <button @click="tab = 'request'"
                        :class="{ 'border-b-2 border-blue-500 text-blue-400': tab === 'request', 'text-gray-400 hover:text-gray-200': tab !== 'request' }"
                        class="pb-2 text-sm font-medium transition-colors duration-150">
                        Solicitar Mantención
                    </button>
                </div>

                <!-- Tab: Estado Técnico -->
                <div x-show="tab === 'status'">
                    <form id="update-maintenance-state-form" method="POST" :action="maintenanceVehicle.updateStateAction">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-4">Aceite y Servicios</h3>
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="last_oil_change" :value="__('Último Cambio Aceite (km)')"
                                            class="text-gray-400" />
                                        <x-text-input id="last_oil_change"
                                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100"
                                            type="text" name="last_oil_change_km"
                                            x-model="maintenanceVehicle.last_oil_change_km"
                                            x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                            x-bind:disabled="maintenanceVehicle.oil_change_due && !['maintenance', 'workshop'].includes(maintenanceVehicle.status)"
                                            x-bind:class="{'opacity-50 cursor-not-allowed': maintenanceVehicle.oil_change_due && !['maintenance', 'workshop'].includes(maintenanceVehicle.status)}"
                                            placeholder="0" />
                                    </div>
                                    <div>
                                        <x-input-label for="next_oil_change" :value="__('Próximo Cambio (km)')"
                                            class="text-gray-400"
                                            ::class="{ 'text-red-500 font-bold': maintenanceVehicle.oil_change_due }" />
                                        <div class="relative">
                                            <x-text-input id="next_oil_change"
                                                class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100"
                                                ::class="{ 'border-red-500 ring-1 ring-red-500': maintenanceVehicle.oil_change_due }"
                                                type="text" name="next_oil_change_km"
                                                x-model="maintenanceVehicle.next_oil_change_km"
                                                x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                                x-bind:disabled="maintenanceVehicle.oil_change_due && !['maintenance', 'workshop'].includes(maintenanceVehicle.status)"
                                                x-bind:class="{'opacity-50 cursor-not-allowed': maintenanceVehicle.oil_change_due && !['maintenance', 'workshop'].includes(maintenanceVehicle.status)}"
                                                placeholder="10.000" />
                                            <template x-if="maintenanceVehicle.oil_change_due">
                                                <span
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-red-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <x-input-label for="last_service_date" :value="__('Fecha Última Revisión')"
                                            class="text-gray-400" />
                                        <x-text-input id="last_service_date"
                                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100"
                                            type="date" name="last_service_date"
                                            x-model="maintenanceVehicle.last_service_date" />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-semibold text-gray-300 mb-4">Estado Neumáticos</h3>
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="tire_front" :value="__('Delanteros')"
                                            class="text-gray-400" />
                                        <select id="tire_front" name="tire_status_front"
                                            x-model="maintenanceVehicle.tire_status_front"
                                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="good">🟢 Bueno</option>
                                            <option value="fair">🟡 Regular</option>
                                            <option value="poor">🔴 Malo (Cambiar)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="tire_rear" :value="__('Traseros')" class="text-gray-400" />
                                        <select id="tire_rear" name="tire_status_rear"
                                            x-model="maintenanceVehicle.tire_status_rear"
                                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="good">🟢 Bueno</option>
                                            <option value="fair">🟡 Regular</option>
                                            <option value="poor">🔴 Malo (Cambiar)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="mt-8 flex justify-end space-x-3">
                        <x-secondary-button @click="$dispatch('close')"
                            class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                            {{ __('Cancelar') }}
                        </x-secondary-button>

                        <template x-if="!['maintenance', 'workshop'].includes(maintenanceVehicle.status)">
                            <x-primary-button form="update-maintenance-state-form" 
                                class="bg-blue-600 hover:bg-blue-700 border-transparent">
                                {{ __('Actualizar Estado') }}
                            </x-primary-button>
                        </template>

                        <template x-if="maintenanceVehicle.status === 'maintenance' || maintenanceVehicle.status === 'workshop'">
                            <x-primary-button form="update-maintenance-state-form" 
                                ::formaction="maintenanceVehicle.completeAction"
                                class="bg-green-600 hover:bg-green-700 border-transparent ml-auto">
                                ✅ {{ __('Finalizar Mantenimiento') }}
                            </x-primary-button>
                        </template>
                    </div>
                </div>

                <!-- Tab: Solicitar Mantención -->
                <div x-show="tab === 'request'">
                    <form method="POST" :action="maintenanceVehicle.storeRequestAction">
                        @csrf
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="req_type" :value="__('Tipo de Solicitud')" class="text-gray-400" />
                                <select id="req_type" name="type" required
                                    class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="oil">🛢️ Cambio de Aceite</option>
                                    <option value="tires">🛞 Cambio de Neumáticos</option>
                                    <option value="mechanics">🔧 Mecánica General</option>
                                    <option value="general">📋 Otro / Inspección</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="req_desc" :value="__('Descripción Detallada')"
                                    class="text-gray-400" />
                                <textarea id="req_desc" name="description" rows="4" required
                                    class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Describe el problema o los detalles del servicio requerido..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <x-secondary-button @click="$dispatch('close')"
                                class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                                {{ __('Cancelar') }}
                            </x-secondary-button>
                            <x-primary-button class="bg-yellow-600 hover:bg-yellow-700 border-transparent">
                                {{ __('Enviar Solicitud') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </x-modal>
        <!-- Modal Solicitudes Pendientes -->
        <x-modal name="maintenance-requests-modal" :show="false" focusable maxWidth="6xl">
            <div class="p-6 bg-gray-800 text-gray-100">
                <h2 class="text-lg font-medium text-gray-100 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    {{ __('Solicitudes Pendientes') }}
                </h2>

                <!-- Sección de Reservas de Vehículos -->
                <div class="mb-8">
                    <h3 class="text-md font-bold text-indigo-400 mb-3 uppercase tracking-wider">Reservas de Vehículos</h3>
                    @if(isset($pendingReservations) && $pendingReservations->count() > 0)
                        <div class="overflow-x-auto bg-gray-900 rounded-lg shadow mb-4">
                            <table class="min-w-full divide-y divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Foto</th>
                                        <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuario</th>
                                        <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Vehículo</th>
                                        <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Destino</th>
                                        <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Desde</th>
                                        <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Hasta</th>
                                        <th class="px-4 py-3 bg-gray-900 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-800 divide-y divide-gray-700">
                                    @foreach($pendingReservations as $reservation)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-100">
                                                <button @click="viewingUser = { 
                                                    name: '{{ $reservation->user->name }}', 
                                                    email: '{{ $reservation->user->email }}', 
                                                    photo: '{{ $reservation->user->profile_photo_path ? asset('storage/' . $reservation->user->profile_photo_path) : null }}',
                                                    initial: '{{ substr($reservation->user->name, 0, 1) }}'
                                                }; $dispatch('open-modal', 'user-details-modal')" 
                                                class="focus:outline-none transform hover:scale-110 transition-transform duration-200">
                                                    @if ($reservation->user->profile_photo_path)
                                                        <img class="h-12 w-12 rounded-full object-cover border-2 border-transparent hover:border-indigo-500" src="{{ asset('storage/' . $reservation->user->profile_photo_path) }}" alt="{{ $reservation->user->name }}" />
                                                    @else
                                                        <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-800 font-bold ml-1 text-lg border-2 border-transparent hover:border-indigo-500">
                                                            {{ substr($reservation->user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </button>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-100">
                                                <div>{{ $reservation->user->name }}</div>
                                                @if($reservation->conductor)
                                                    <div class="text-xs text-yellow-500 mt-1 flex items-center">
                                                        <span class="mr-1">👉 Para:</span>
                                                        <span class="font-bold">{{ $reservation->conductor->nombre }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                                @php
                                                    $soap = $reservation->vehicle->documents->firstWhere('type', 'insurance');
                                                    $permit = $reservation->vehicle->documents->firstWhere('type', 'permit');
                                                    $technical = $reservation->vehicle->documents->firstWhere('type', 'technical_review');
                                                    
                                                    $reqJsVehicle = [
                                                        'id' => $reservation->vehicle->id,
                                                        'plate' => $reservation->vehicle->plate,
                                                        'serial_number' => $reservation->vehicle->serial_number,
                                                        'brand' => $reservation->vehicle->brand,
                                                        'model' => $reservation->vehicle->model,
                                                        'year' => $reservation->vehicle->year,
                                                        'mileage' => $reservation->vehicle->mileage,
                                                        'status' => $reservation->vehicle->status,
                                                        'image_url' => $reservation->vehicle->image_path ? Storage::url($reservation->vehicle->image_path) : '',
                                                        'imageUrl' => $reservation->vehicle->image_path ? Storage::url($reservation->vehicle->image_path) : '',
                                                        'documents' => $reservation->vehicle->documents,
                                                        'assigned_user' => $reservation->user->name, // Emulate assignment for this view
                                                        'assigned_user_rut' => $reservation->user->rut ?? '',
                                                        'assigned_user_phone' => $reservation->user->phone ?? '',
                                                    ];
                                                    $reqJsonVehicle = json_encode($reqJsVehicle);
                                                @endphp
                                                <div class="flex items-center">
                                                    <button @click="viewingVehicle = {{ $reqJsonVehicle }}; $dispatch('open-modal', 'view-vehicle-modal')" 
                                                            class="focus:outline-none transform hover:scale-110 transition-transform duration-200 cursor-pointer"
                                                            title="Ver Detalle Grande">
                                                        @if($reservation->vehicle->image_path)
                                                            <img src="{{ asset('storage/' . $reservation->vehicle->image_path) }}" alt="Vehículo" class="h-10 w-10 rounded-full object-cover mr-3 border border-gray-600 hover:border-blue-500">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center mr-3 border border-gray-600 hover:border-blue-500">
                                                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </button>
                                                    <div>
                                                        <div class="font-bold text-white">{{ $reservation->vehicle->brand }} {{ $reservation->vehicle->model }}</div>
                                                        <div class="text-xs text-gray-500 font-mono">{{ $reservation->vehicle->plate }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                                @if($reservation->destination_type === 'outside')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Fuera Ciudad</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Local</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                                {{ $reservation->start_date->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                                {{ $reservation->end_date->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <form action="{{ route('requests.approve', $reservation->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            Aprobar
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('requests.reject', $reservation->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 disabled:opacity-25 transition">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            Rechazar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic mb-4">No hay reservas pendientes.</p>
                    @endif
                </div>

                <div class="border-t border-gray-700 pt-4">
                    <h3 class="text-md font-bold text-yellow-500 mb-3 uppercase tracking-wider">Mantenimiento</h3>
                @if($pendingRequests->isEmpty())
                    <p class="text-gray-400 text-center py-8">No hay solicitudes de mantenimiento.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Vehículo</th>
                                    <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tipo</th>
                                    <th class="px-4 py-3 bg-gray-900 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Descripción</th>
                                    <th class="px-4 py-3 bg-gray-900 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @foreach($pendingRequests as $req)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-100">
                                            {{ $req->vehicle ? $req->vehicle->brand : 'Vehículo Eliminado' }} 
                                            {{ $req->vehicle ? $req->vehicle->model : '' }} <br>
                                            <span class="text-xs text-gray-500">{{ $req->vehicle ? $req->vehicle->plate : '' }}</span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                            @switch($req->type)
                                                @case('oil') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Aceite</span> @break
                                                @case('tires') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Neumáticos</span> @break
                                                @case('mechanics') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Mecánica</span> @break
                                                @default <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">General</span>
                                            @endswitch
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-300 max-w-xs truncate" title="{{ $req->description }}">
                                            {{ $req->description }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <form method="POST" action="/maintenance/requests/{{ $req->id }}/accept">
                                                @csrf
                                                <button type="submit" class="text-green-500 hover:text-green-400 font-bold hover:underline">
                                                    ACEPTAR
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <x-secondary-button @click="$dispatch('close')" class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cerrar') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>

        <!-- Modal Detalles del Usuario -->
        <x-modal name="user-details-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100 text-center">
                <h2 class="text-xl font-bold text-gray-100 mb-6">Detalles del Solicitante</h2>
                
                <div class="flex flex-col items-center justify-center mb-6">
                    <template x-if="viewingUser && viewingUser.photo">
                        <img :src="viewingUser.photo" alt="Profile" class="h-32 w-32 rounded-full object-cover border-4 border-indigo-500 shadow-lg mb-4">
                    </template>
                    <template x-if="viewingUser && !viewingUser.photo">
                         <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-800 font-bold text-4xl border-4 border-indigo-500 shadow-lg mb-4">
                            <span x-text="viewingUser.initial"></span>
                        </div>
                    </template>
                    
                    <h3 class="text-2xl font-bold text-white mb-1" x-text="viewingUser ? viewingUser.name : ''"></h3>
                    <p class="text-gray-400" x-text="viewingUser ? viewingUser.email : ''"></p>
                </div>

                <div class="mt-6 flex justify-center">
                    <x-secondary-button @click="$dispatch('close')" class="bg-indigo-600 text-white hover:bg-indigo-500 border-transparent px-8 py-2">
                        {{ __('Cerrar') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>
        <!-- Modal Gestionar Documentos -->
        <x-modal name="manage-documents-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100" x-data="{ activeTab: 'list' }">
                <h2 class="text-xl font-bold text-gray-100 mb-6 border-b border-gray-700 pb-2">
                    {{ __('Gestión Documental') }}
                </h2>

                <div class="mb-6 bg-gray-900 p-4 rounded-lg">
                     <p class="text-sm text-gray-400">Vehículo: <span class="font-bold text-white" x-text="viewingVehicle.brand + ' ' + viewingVehicle.model"></span></p>
                     <p class="text-sm text-gray-400">Patente: <span class="font-bold text-white" x-text="viewingVehicle.plate"></span></p>
                </div>

                <!-- Tabs -->
                <div class="flex space-x-4 mb-4 border-b border-gray-700">
                    <button @click="activeTab = 'list'" :class="{ 'border-b-2 border-blue-500 text-blue-400': activeTab === 'list', 'text-gray-400': activeTab !== 'list' }" class="pb-2 text-sm font-medium">Documentos Actuales</button>
                    <button @click="activeTab = 'upload'" :class="{ 'border-b-2 border-blue-500 text-blue-400': activeTab === 'upload', 'text-gray-400': activeTab !== 'upload' }" class="pb-2 text-sm font-medium">Subir Nuevo</button>
                </div>

                <!-- Lista -->
                <div x-show="activeTab === 'list'">
                    <template x-if="viewingVehicle.documents && viewingVehicle.documents.length > 0">
                        <ul class="space-y-3">
                            <template x-for="doc in viewingVehicle.documents" :key="doc.id">
                                <li class="flex items-center justify-between bg-gray-700 p-3 rounded border border-gray-600">
                                    <div>
                                        <p class="text-sm font-bold text-gray-200" x-text="doc.type === 'insurance' ? 'Seguro' : (doc.type === 'technical_review' ? 'Revisión Técnica' : 'Permiso Circulación')"></p>
                                        <p class="text-xs text-gray-400">
                                            Vence: <span x-text="doc.expires_at ? doc.expires_at.split('T')[0].split('-').reverse().join('/') : ''"></span>
                                            <span x-show="getDaysRemaining(doc.expires_at) !== null && getDaysRemaining(doc.expires_at) < 0" class="ml-2 text-xs font-bold text-red-500 bg-red-900/30 px-2 py-0.5 rounded">VENCIDO</span>
                                            <span x-show="getDaysRemaining(doc.expires_at) !== null && getDaysRemaining(doc.expires_at) >= 0 && getDaysRemaining(doc.expires_at) <= 7" class="ml-2 text-xs font-bold text-yellow-500 bg-yellow-900/30 px-2 py-0.5 rounded" x-text="'⚠️ ' + getDaysRemaining(doc.expires_at) + ' días'"></span>
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a :href="'/storage/' + doc.file_path" target="_blank" class="text-blue-400 hover:text-blue-300 text-xs uppercase font-bold">Ver</a>
                                        
                                        <form method="POST" :action="'/vehicles/documents/' + doc.id">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 text-xs uppercase font-bold bg-transparent border-0 cursor-pointer">Eliminar</button>
                                        </form>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="!viewingVehicle.documents || viewingVehicle.documents.length === 0">
                        <div class="text-center py-8 text-gray-500">No hay documentos cargados.</div>
                    </template>
                </div>

                <!-- Subir -->
                <div x-show="activeTab === 'upload'">
                    <form method="POST" :action="'/vehicles/' + viewingVehicle.id + '/documents'" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="doc_type" :value="__('Tipo de Documento')" class="text-gray-400" />
                            <select id="doc_type" name="type" required class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="insurance">Seguro Obligatorio (SOAP)</option>
                                <option value="technical_review">Revisión Técnica</option>
                                <option value="permit">Permiso de Circulación</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="doc_expires" :value="__('Fecha de Vencimiento')" class="text-gray-400" />
                            <x-text-input id="doc_expires" class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="date" name="expires_at" required />
                        </div>

                        <div>
                            <x-input-label for="doc_file" :value="__('Archivo (PDF/Imagen)')" class="text-gray-400" />
                            <input id="doc_file" type="file" name="file" required accept=".pdf,image/*" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md" />
                        </div>

                        <div class="pt-4 flex justify-end">
                            <x-primary-button class="bg-blue-600 hover:bg-blue-700">Subir Documento</x-primary-button>
                        </div>
                    </form>
                </div>

                <div class="mt-8 flex justify-end">
                    <x-secondary-button @click="$dispatch('close')" class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cerrar') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>
    </div>

    <!-- Modal Detalle de Reserva -->
    <x-modal name="reservation-detail-modal" :show="false" focusable>
        <div class="p-6 bg-gray-800 text-gray-100" x-data="{
            getDates() {
                if (!viewingVehicle || !viewingVehicle.reservation) return [];
                // Fix date string parsing for cross-browser safety if needed, but YYYY-MM-DD usually works
                const start = new Date(viewingVehicle.reservation.start_date.replace(/-/g, '/')); 
                const end = new Date(viewingVehicle.reservation.end_date.replace(/-/g, '/'));
                const days = [];
                // Limit to avoiding infinite loops if dates are bad
                let current = new Date(start);
                let safeGuard = 0;
                while (current <= end && safeGuard < 365) {
                    days.push(new Date(current));
                    current.setDate(current.getDate() + 1);
                    safeGuard++;
                }
                return days;
            }
        }">
            <div class="flex justify-between items-start mb-6 border-b border-gray-700 pb-4">
                <h2 class="text-xl font-bold text-gray-100">
                    <span class="text-indigo-400">📅</span> Detalle de Reserva
                </h2>
                <div class="text-right">
                    <div class="text-sm text-gray-400">Vehículo</div>
                    <div class="font-bold text-white" x-text="viewingVehicle.brand + ' ' + viewingVehicle.model"></div>
                    <div class="text-xs text-gray-500" x-text="viewingVehicle.plate"></div>
                </div>
            </div>
            
            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-6 mb-8 bg-gray-900/50 p-4 rounded-xl border border-gray-700">
                <div>
                    <label class="text-xs text-indigo-400 uppercase font-bold tracking-wider mb-1 block">Solicitante</label>
                    <div class="font-bold text-lg text-white" x-text="viewingVehicle.reservation?.user_name"></div>
                    <div class="text-xs text-gray-500" x-text="viewingVehicle.reservation?.user_email"></div>
                </div>
                <div>
                    <label class="text-xs text-indigo-400 uppercase font-bold tracking-wider mb-1 block">Conductor Asignado</label>
                    <div class="font-bold text-lg text-yellow-500" x-text="viewingVehicle.reservation?.conductor_name"></div>
                </div>
                <div>
                    <label class="text-xs text-indigo-400 uppercase font-bold tracking-wider mb-1 block">Tipo de Viaje</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="viewingVehicle.reservation?.destination_type === 'outside' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'">
                        <span x-text="viewingVehicle.reservation?.destination_type === 'outside' ? 'Fuera de Ciudad' : 'Local'"></span>
                    </span>
                </div>
                <div>
                    <label class="text-xs text-indigo-400 uppercase font-bold tracking-wider mb-1 block">Días Restantes</label>
                    <div class="font-bold text-2xl font-mono" 
                        :class="viewingVehicle.reservation?.days_remaining < 1 ? 'text-red-500' : 'text-green-400'" 
                        x-text="viewingVehicle.reservation?.days_remaining >= 0 ? viewingVehicle.reservation?.days_remaining + ' días' : 'Vencido'"></div>
                </div>
            </div>

            <!-- Timeline / Mini Calendar -->
            <div class="mb-6">
                <div class="flex justify-between items-end mb-3">
                     <label class="text-xs text-gray-400 uppercase font-bold tracking-wider">Cronograma de Uso</label>
                     <span class="text-xs text-gray-500" x-text="'Desde: ' + (viewingVehicle.reservation?.start_date.split(' ')[0] || '') + ' Hasta: ' + (viewingVehicle.reservation?.end_date.split(' ')[0] || '')"></span>
                </div>
               
                <div class="flex space-x-2 overflow-x-auto pb-4 custom-scrollbar px-1">
                    <template x-for="date in getDates()" :key="date.getTime()">
                        <div class="flex-shrink-0 w-14 h-16 rounded-lg flex flex-col items-center justify-center border transition-all duration-300"
                            :class="{
                                'bg-indigo-600 border-indigo-500 text-white shadow-lg scale-110 z-10': date.toDateString() === new Date().toDateString(),
                                'bg-gray-700 border-gray-600 text-gray-400 opacity-60': date < new Date().setHours(0,0,0,0),
                                'bg-gray-800 border-gray-600 text-gray-300': date > new Date()
                            }">
                            <span class="text-[9px] uppercase tracking-tighter" x-text="date.toLocaleDateString('es-ES', { weekday: 'short' }).slice(0,3)"></span>
                            <span class="text-lg font-bold leading-none my-0.5" x-text="date.getDate()"></span>
                            <span class="text-[8px]" x-show="date.toDateString() === new Date().toDateString()">HOY</span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex justify-end pt-4 border-t border-gray-700">
                <x-secondary-button @click="$dispatch('close')" class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                    {{ __('Cerrar') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</x-app-layout>