<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Activos') }}
            </h2>
            <div class="flex flex-wrap gap-2 items-center">
                <a href="{{ route('assets.trash') }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    {{ __('Papelera') }}
                </a>
                <a href="{{ route('assets.users-history-index') }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9 mr-2">                    <svg class  ="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Historial Usuarios') }}
                </a>
                <!-- Dropdown Exportar PDF -->
                <div x-data="{ exportOpen: false }" class="relative">
                    <button @click="exportOpen = !exportOpen" @click.away="exportOpen = false"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        {{ __('Exportar PDF') }}
                        <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="exportOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                        style="display: none;">
                        <div class="py-1">
                            <!-- Header del dropdown -->
                            <div
                                class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                                Filtrar Exportación
                            </div>

                            <!-- Todo el Inventario -->
                            @php
                                $exportAllParams = array_merge(request()->except('export_filter'), ['export_filter' => 'all']);
                            @endphp
                            <a href="{{ route('assets.export-pdf', $exportAllParams) }}" target="_blank"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span
                                    class="w-3 h-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 mr-3"></span>
                                <span class="flex-1">Todo el Inventario</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>

                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>

                            <!-- Solo Disponibles -->
                            @php
                                $exportAvailableParams = array_merge(request()->except(['export_filter', 'estado']), ['export_filter' => 'available']);
                            @endphp
                            <a href="{{ route('assets.export-pdf', $exportAvailableParams) }}" target="_blank"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span
                                    class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)] mr-3"></span>
                                <span class="flex-1">Solo Disponibles</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>

                            <!-- Solo Asignados -->
                            @php
                                $exportAssignedParams = array_merge(request()->except(['export_filter', 'estado']), ['export_filter' => 'assigned']);
                            @endphp
                            <a href="{{ route('assets.export-pdf', $exportAssignedParams) }}" target="_blank"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span
                                    class="w-3 h-3 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)] mr-3"></span>
                                <span class="flex-1">Solo Asignados</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>

                            <!-- Solo en Mantenimiento -->
                            @php
                                $exportMaintenanceParams = array_merge(request()->except(['export_filter', 'estado']), ['export_filter' => 'maintenance']);
                            @endphp
                            <a href="{{ route('assets.export-pdf', $exportMaintenanceParams) }}" target="_blank"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span
                                    class="w-3 h-3 rounded-full bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.5)] mr-3"></span>
                                <span class="flex-1">Solo en Mantenimiento</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>

                            <!-- Solo Dados de Baja -->
                            @php
                                $exportWrittenOffParams = array_merge(request()->except(['export_filter', 'estado']), ['export_filter' => 'written_off']);
                            @endphp
                            <a href="{{ route('assets.export-pdf', $exportWrittenOffParams) }}" target="_blank"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span
                                    class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)] mr-3"></span>
                                <span class="flex-1">Solo Dados de Baja</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <button x-data="" @click="$dispatch('open-modal', 'create-asset-modal')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Nuevo') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
            deleteAction: '', 
            editingAsset: {}, 
            assignmentAsset: {},
            assignAction: '',
            editAction: '',
            cancelAssignmentAction: '',
            updateAssignmentAction: '',
            assignAction: '',
            editAction: '',
            cancelAssignmentAction: '',
            updateAssignmentAction: '',
            searchQuery: '{{ request('search', '') }}',
            showFilters: false,
            selectedWriteOffAsset: null,
            // Batch Selection State
            selectedAssets: [],
            barcodeModalOpen: false,
            barcodeSize: 'medium',
            
            toggleAll() {
                // Obtener todos los IDs de la página actual
                let allIds = [{{ $assets->pluck('id')->implode(',') }}];
                
                // Si están todos seleccionados, deseleccionar
                // Verificamos si todos los IDs de la página están en selectedAssets
                let allSelected = allIds.every(id => this.selectedAssets.includes(id));
                
                if (allSelected) {
                    // Remover los IDs de esta página de la selección (manteniendo otros si hubiera)
                    this.selectedAssets = this.selectedAssets.filter(id => !allIds.includes(id));
                } else {
                    // Agregar los IDs que faltan
                    allIds.forEach(id => {
                        if (!this.selectedAssets.includes(id)) {
                            this.selectedAssets.push(id);
                        }
                    });
                }
            }
        }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="mb-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('assets.index', request()->except(['estado', 'page'])) }}"
                    class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-gray-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ !request('estado') ? 'ring-2 ring-gray-500 ring-offset-2 dark:ring-offset-gray-900' : '' }}">
                    <div class="text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase truncate">Total</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $totalAssets }}</div>
                </a>

                <a href="{{ route('assets.index', array_merge(request()->except('page'), ['estado' => 'available'])) }}"
                    class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-green-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ request('estado') === 'available' ? 'ring-2 ring-green-500 ring-offset-2 dark:ring-offset-gray-900' : '' }}">
                    <div class="text-green-600 dark:text-green-400 text-[10px] font-bold uppercase truncate">Disponibles
                    </div>
                    <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $countDisponible }}</div>
                </a>

                <a href="{{ route('assets.index', array_merge(request()->except('page'), ['estado' => 'assigned'])) }}"
                    class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-blue-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ request('estado') === 'assigned' ? 'ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-gray-900' : '' }}">
                    <div class="text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase truncate">Asignados
                    </div>
                    <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $countAsignado }}</div>
                </a>

                <a href="{{ route('assets.index', array_merge(request()->except('page'), ['estado' => 'maintenance'])) }}"
                    class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-yellow-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ request('estado') === 'maintenance' ? 'ring-2 ring-yellow-500 ring-offset-2 dark:ring-offset-gray-900' : '' }}">
                    <div class="text-yellow-600 dark:text-yellow-400 text-[10px] font-bold uppercase truncate">
                        Mantenimiento</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $countMantenimiento }}</div>
                </a>

                <a href="{{ route('assets.index', array_merge(request()->except('page'), ['estado' => 'written_off'])) }}"
                    class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-red-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ request('estado') === 'written_off' ? 'ring-2 ring-red-500 ring-offset-2 dark:ring-offset-gray-900' : '' }}">
                    <div class="text-red-600 dark:text-red-400 text-[10px] font-bold uppercase truncate">Dados de Baja
                    </div>
                    <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $countBaja }}</div>
                </a>
            </div>

            <!-- Búsqueda -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center">
                <div class="relative w-full sm:max-w-md">
                    <input type="text" x-model="searchQuery" placeholder="Buscar por código, nombre, marca o modelo..."
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-shadow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex gap-2 w-full sm:w-auto">
                    <!-- Botón de Filtros -->
                    <button @click="showFilters = true"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-bold text-sm transition-colors flex items-center gap-2 border border-gray-300 dark:border-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('Filtros') }}
                        @php
                            $activeFiltersCount = collect([request('estado'), request('categoria')])->filter()->count();
                        @endphp
                        @if($activeFiltersCount > 0)
                            <span
                                class="bg-indigo-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $activeFiltersCount }}</span>
                        @endif
                    </button>

                    <template x-if="searchQuery || {{ $activeFiltersCount > 0 ? 'true' : 'false' }}">
                        <a href="{{ route('assets.index') }}"
                            class="px-3 py-2 text-gray-500 hover:text-red-500 transition-colors"
                            title="Limpiar Filtros">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </template>

                    <!-- Botón Generar Códigos -->
                    <div x-show="selectedAssets.length > 0" x-transition class="ml-2">
                        <button @click="$dispatch('open-modal', 'batch-barcode-modal')"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-bold text-sm transition-colors flex items-center gap-2 shadow-sm border border-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 12h16m-7 6h6M5 18v2m14-2v2">
                                </path>
                            </svg>
                            Generar
                            <span class="bg-white text-purple-600 text-[10px] px-1.5 py-0.5 rounded-full font-bold"
                                x-text="selectedAssets.length"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead class="bg-gray-800 text-gray-300">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider w-10">
                                        <input type="checkbox" @click="toggleAll()"
                                            :checked="selectedAssets.length > 0 && [{{ $assets->pluck('id')->implode(',') }}].every(id => selectedAssets.includes(id))"
                                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 cursor-pointer">
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Foto
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Código
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Categoría
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Ubicación
                                    </th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-900 text-gray-300">
                                @forelse($assets as $asset)
                                    <tr class="hover:bg-gray-800 transition duration-150"
                                        data-search="{{ strtolower($asset->codigo_interno . ' ' . $asset->nombre . ' ' . $asset->marca . ' ' . $asset->modelo . ' ' . $asset->codigo_barra) }}"
                                        x-show="!searchQuery || $el.dataset.search.split(' ').some(word => word.startsWith(searchQuery.toLowerCase()))">
                                        <!-- Checkbox -->
                                        <td class="px-5 py-4 text-sm">
                                            <input type="checkbox" :value="{{ $asset->id }}" x-model="selectedAssets"
                                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 cursor-pointer w-4 h-4">
                                        </td>

                                        <!-- Foto -->
                                        <td class="px-5 py-4 text-sm">
                                            @if($asset->foto_path)
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-600"
                                                        src="{{ Storage::url($asset->foto_path) }}" alt="{{ $asset->nombre }}">
                                                </div>
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-400 border border-gray-600">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Código -->
                                        <td class="px-5 py-4 text-sm font-bold">
                                            {{ $asset->codigo_interno }}
                                            <div class="text-[10px] text-gray-500">{{ $asset->codigo_barra }}</div>
                                        </td>

                                        <!-- Nombre -->
                                        <td class="px-5 py-4 text-sm">
                                            {{ $asset->nombre }}
                                            @if($asset->marca || $asset->modelo)
                                                <div class="text-xs text-gray-500">
                                                    {{ $asset->marca }} {{ $asset->modelo }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Categoría -->
                                        <td class="px-5 py-4 text-sm">
                                            {{ $asset->category->nombre ?? 'Sin categoría' }}
                                        </td>

                                        <!-- Estado -->
                                        <td class="px-5 py-4 text-sm">
                                            @php
                                                $statusClasses = [
                                                    'available' => 'text-green-400 bg-green-900/30 border border-green-900',
                                                    'assigned' => 'text-blue-400 bg-blue-900/30 border border-blue-900',
                                                    'maintenance' => 'text-yellow-400 bg-yellow-900/30 border border-yellow-900',
                                                    'written_off' => 'text-red-400 bg-red-900/30 border border-red-900',
                                                ];
                                                $statusLabel = [
                                                    'available' => 'DISPONIBLE',
                                                    'assigned' => 'ASIGNADO',
                                                    'maintenance' => 'MANTENIMIENTO',
                                                    'written_off' => 'DADO DE BAJA',
                                                ];

                                                // Verificar última asignación para alertas de estado
                                                $lastAssignment = $asset->assignments->sortByDesc('created_at')->first();
                                                $showWarning = false;
                                                $warningMessage = '';
                                                $warningClass = '';

                                                if ($lastAssignment && $asset->estado !== 'assigned' && $asset->estado !== 'maintenance' && $asset->estado !== 'written_off') {
                                                    // Solo mostramos advertencia si está disponible pero la última devolución fue mala

                                                    if (in_array($lastAssignment->estado_devolucion, ['bad', 'damaged', 'regular'])) {
                                                        // Verificar si ya se hizo mantención POSTERIOR a la devolución
                                                        // Usamos fecha_devolucion. Si no existe (raro si tiene estado), usamos updated_at
                                                        $returnDate = $lastAssignment->fecha_devolucion ?? $lastAssignment->updated_at;

                                                        $hasRecentMaintenance = $asset->maintenances
                                                            ->where('fecha_termino', '!=', null)
                                                            ->filter(function ($maintenance) use ($returnDate) {
                                                                $mDate = \Carbon\Carbon::parse($maintenance->fecha_termino)->startOfDay();
                                                                $rDate = \Carbon\Carbon::parse($returnDate)->startOfDay();

                                                                if ($mDate->gt($rDate)) {
                                                                    return true;
                                                                }

                                                                if ($mDate->equalTo($rDate)) {
                                                                    $mTimestamp = $maintenance->updated_at ?? $mDate->endOfDay();
                                                                    $rTimestamp = \Carbon\Carbon::parse($returnDate);
                                                                    return $mTimestamp->gt($rTimestamp);
                                                                }

                                                                return false;
                                                            })
                                                            ->count() > 0;

                                                        if (!$hasRecentMaintenance) {
                                                            if (in_array($lastAssignment->estado_devolucion, ['bad', 'damaged'])) {
                                                                $showWarning = true;
                                                                $warningMessage = $lastAssignment->estado_devolucion === 'damaged' ? 'DAÑADO' : 'MAL ESTADO';
                                                                $warningClass = 'text-red-400 bg-red-900/20 border-red-800';
                                                            } elseif ($lastAssignment->estado_devolucion === 'regular') {
                                                                $showWarning = true;
                                                                $warningMessage = 'REGULAR';
                                                                $warningClass = 'text-yellow-400 bg-yellow-900/20 border-yellow-800';
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <div class="flex flex-col items-start gap-1">
                                                <span
                                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md {{ $statusClasses[$asset->estado] ?? 'text-gray-400 bg-gray-800' }}">
                                                    {{ $statusLabel[$asset->estado] ?? strtoupper($asset->estado) }}
                                                </span>

                                                @php
                                                    $isOverdue = false;
                                                    if ($asset->estado === 'assigned' && $asset->activeAssignment && $asset->activeAssignment->fecha_estimada_devolucion) {
                                                        $now = now();
                                                        $deadline = \Carbon\Carbon::parse($asset->activeAssignment->fecha_estimada_devolucion);
                                                        // Solo si ya pasó la fecha
                                                        if ($now->gt($deadline)) {
                                                            $isOverdue = true;
                                                        }
                                                    }
                                                @endphp

                                                @if($isOverdue)
                                                    <span
                                                        class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded border text-red-400 bg-red-900/20 border-red-800 animate-pulse">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        ATRASADO
                                                    </span>
                                                @endif

                                                @if($showWarning)
                                                    <span
                                                        class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded border {{ $warningClass }} animate-pulse">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                            </path>
                                                        </svg>
                                                        {{ $warningMessage }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($asset->estado === 'assigned' && $asset->activeAssignment)
                                                <div class="text-[10px] text-blue-300 mt-1">
                                                    @if($asset->activeAssignment->user)
                                                        {{ $asset->activeAssignment->user->name }}
                                                    @elseif($asset->activeAssignment->worker)
                                                        {{ $asset->activeAssignment->worker->nombre }}
                                                    @else
                                                        {{ $asset->activeAssignment->trabajador_nombre ?? 'Desconocido' }}
                                                    @endif
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Ubicación -->
                                        <td class="px-5 py-4 text-sm">
                                            {{ $asset->ubicacion ?? 'Sin ubicación' }}
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-5 py-4 text-sm font-medium">
                                            @php

                                                $activeAssignment = $asset->activeAssignment;
                                                $assignmentData = null;

                                                if ($activeAssignment) {
                                                    $user = $activeAssignment->user;
                                                    $worker = $activeAssignment->worker;

                                                    // Determinar días restantes
                                                    $daysRemaining = null;
                                                    if ($activeAssignment->fecha_estimada_devolucion) {
                                                        $now = now();
                                                        $deadline = \Carbon\Carbon::parse($activeAssignment->fecha_estimada_devolucion);
                                                        $diff = $now->diffInDays($deadline, false);
                                                        $daysRemaining = (int) ceil($diff);
                                                    }

                                                    $assignmentData = [
                                                        'assigned_to' => $user ? $user->name : ($worker ? $worker->nombre : $activeAssignment->trabajador_nombre),
                                                        'rut' => $user ? $user->rut : ($worker ? $worker->rut : $activeAssignment->trabajador_rut),
                                                        'email' => $user ? $user->email : null,
                                                        'phone' => $user ? $user->phone : null,
                                                        'photo_url' => $user && $user->profile_photo_path ? '/storage/' . $user->profile_photo_path : null, // Asumiendo path relativo en DB
                                                        'cargo' => $user ? $user->cargo : ($worker ? $worker->cargo : $activeAssignment->trabajador_cargo),
                                                        'department' => $user ? $user->departamento : ($worker ? $worker->departamento : $activeAssignment->trabajador_departamento),
                                                        'start_date' => $activeAssignment->fecha_entrega ? $activeAssignment->fecha_entrega->format('Y-m-d') : null,
                                                        'end_date' => $activeAssignment->fecha_estimada_devolucion ? $activeAssignment->fecha_estimada_devolucion->format('Y-m-d') : null,
                                                        'days_remaining' => $daysRemaining,
                                                        'observations' => $activeAssignment->observaciones
                                                    ];
                                                }

                                                $jsAsset = [
                                                    'id' => $asset->id,
                                                    'foto_url' => $asset->foto_path ? Storage::url($asset->foto_path) : null,
                                                    'codigo_interno' => $asset->codigo_interno,
                                                    'codigo_barra' => $asset->codigo_barra,
                                                    'nombre' => $asset->nombre,
                                                    'categoria_id' => $asset->categoria_id,
                                                    'marca' => $asset->marca,
                                                    'modelo' => $asset->modelo,
                                                    'numero_serie' => $asset->numero_serie,
                                                    'estado' => $asset->estado,
                                                    'ubicacion' => $asset->ubicacion,
                                                    'fecha_adquisicion' => $asset->fecha_adquisicion?->format('Y-m-d'),
                                                    'valor_referencial' => $asset->valor_referencial ? number_format($asset->valor_referencial, 0, '', '.') : '',
                                                    'observaciones' => $asset->observaciones,
                                                    'active_assignment' => $assignmentData
                                                ];
                                                $jsonAsset = json_encode($jsAsset);
                                            @endphp
                                            <div class="flex items-center space-x-4">
                                                <!-- Resolver Alerta (Solo si hay advertencia) -->
                                                @if($showWarning)
                                                    <button
                                                        @click="
                                                                                                                                                                                                                                                                                                                                                                                    editingAsset = {{ $jsonAsset }};
                                                                                                                                                                                                                                                                                                                                                                                    $dispatch('open-modal', 'resolve-issue-modal');
                                                                                                                                                                                                                                                                                                                                                                                "
                                                        class="text-orange-500 hover:text-orange-400 transition duration-150 animate-pulse"
                                                        title="Gestionar Alerta">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                @endif

                                                <!-- Finalizar Mantención (Solo si está en mantenimiento) -->
                                                @if($asset->estado === 'maintenance')
                                                    <button
                                                        @click="
                                                                                                                                                                                                                                                                                                                                                                                    editingAsset = {{ $jsonAsset }};
                                                                                                                                                                                                                                                                                                                                                                                    $dispatch('open-modal', 'finish-maintenance-modal');
                                                                                                                                                                                                                                                                                                                                                                                "
                                                        class="text-green-500 hover:text-green-400 transition duration-150"
                                                        title="Finalizar Mantención">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                                <!-- Asignar (Solo si está disponible) -->
                                                @if($asset->estado === 'available')
                                                    <button
                                                        @click="
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        assignmentAsset = {{ $jsonAsset }};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        assignAction = '{{ route('assets.assign', $asset->id) }}';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $dispatch('open-modal', 'assign-asset-modal');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    "
                                                        class="text-blue-500 hover:text-blue-400 transition duration-150"
                                                        title="Asignar Activo">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                        </svg>
                                                    </button>
                                                @endif


                                                <!-- Ver Historial (Para todos) -->
                                                <a href="{{ route('assets.history', $asset->id) }}"
                                                    class="text-gray-500 hover:text-gray-400 transition duration-150"
                                                    title="Ver Historial Completo">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </a>

                                                <!-- Ver Detalles Asignación (Solo si está asignado) -->
                                                @if($asset->estado === 'assigned')
                                                    <button
                                                        @click="
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                assignmentAsset = {{ $jsonAsset }};
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                $dispatch('open-modal', 'view-assignment-modal');
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            "
                                                        class="text-indigo-400 hover:text-indigo-300 transition duration-150"
                                                        title="Ver Detalles de Asignación">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                @endif

                                                <!-- Código de Barras -->
                                                <a href="{{ route('assets.barcode', $asset->id) }}"
                                                    class="text-purple-400 hover:text-purple-300 transition duration-150"
                                                    title="Descargar Etiqueta PDF">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 12h16m-7 6h6M5 18v2m14-2v2">
                                                        </path>
                                                        <!-- Icono alternativo más claro de código de barras -->
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 10h18M3 14h18m-9-4v8m-7-8v8m14-8v8M3 6l18-4M3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-18 4z"
                                                            style="display:none;" />
                                                        <!-- Usando un icono simple de impresora o codigo -->
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a1 1 0 011-1h16a1 1 0 011 1v3h-2V6H5v2H3V5zm15 10v5h-5v-5h5zm2-3H4v6h2v-4h12v4h2v-6z">
                                                        </path>
                                                    </svg>
                                                </a>

                                                <!-- Ver Detalle -->
                                                <button
                                                    @click="
                                                                                                                                                                                                                                                                                                                                                            editingAsset = {{ $jsonAsset }};
                                                                                                                                                                                                                                                                                                                                                            $dispatch('open-modal', 'view-asset-modal');
                                                                                                                                                                                                                                                                                                                                                        "
                                                    class="text-green-500 hover:text-green-400 transition duration-150"
                                                    title="Ver Detalle">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Editar -->
                                                <button
                                                    @click="
                                                                                                                                                                                                                                                                                                                                                            editingAsset = {{ $jsonAsset }};
                                                                                                                                                                                                                                                                                                                                                            editAction = '{{ route('assets.update', $asset->id) }}';
                                                                                                                                                                                                                                                                                                                                                            $dispatch('open-modal', 'edit-asset-modal');
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

                                                <!-- Eliminar -->


                                                @if ($asset->estado === 'written_off')
                                                    <!-- Ver Detalle Baja -->
                                                    <button
                                                        @click="selectedWriteOffAsset = {{ Js::from($asset) }}; $dispatch('open-modal', 'write-off-details-modal');"
                                                        class="text-red-500 hover:text-red-400 transition duration-150"
                                                        title="Ver Detalle Baja">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </button>
                                                @endif

                                                <!-- Eliminar -->
                                                <button
                                                    @click="deleteAction = '{{ route('assets.destroy', $asset->id) }}'; $dispatch('open-modal', 'confirm-asset-deletion');"
                                                    class="text-gray-400 hover:text-red-500 transition duration-150"
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
                                        <td colspan="7" class="px-5 py-5 text-sm text-center text-gray-500">
                                            No hay activos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar de Filtros (Premium Design) -->
        <div x-show="showFilters" class="fixed inset-0 z-50 flex justify-end" style="display: none;">
            <!-- Backdrop -->
            <div @click="showFilters = false" x-show="showFilters" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <!-- Sidebar Content -->
            <div x-show="showFilters" x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition transform ease-in duration-300" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="relative w-80 bg-white dark:bg-gray-800 h-full shadow-2xl p-6 overflow-y-auto border-l border-gray-700">

                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                        Filtros
                    </h3>
                    <button @click="showFilters = false" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('assets.index') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <!-- Filter: Status -->
                    <div class="mb-4 border-b border-gray-700 pb-4" x-data="{ open: true }">
                        <button type="button" @click="open = !open"
                            class="flex items-center justify-between w-full text-left text-sm font-bold text-gray-400 uppercase tracking-wider mb-2 focus:outline-none">
                            <span>Estado</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="space-y-2 mt-2">
                            <label
                                class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('estado') === 'available' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                <input type="radio" name="estado" value="available" class="hidden" {{ request('estado') === 'available' ? 'checked' : '' }} onchange="this.form.submit()">
                                <span
                                    class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                                <span class="text-gray-200">Disponible</span>
                            </label>
                            <label
                                class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('estado') === 'assigned' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                <input type="radio" name="estado" value="assigned" class="hidden" {{ request('estado') === 'assigned' ? 'checked' : '' }} onchange="this.form.submit()">
                                <span
                                    class="w-3 h-3 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                                <span class="text-gray-200">Asignado</span>
                            </label>
                            <label
                                class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('estado') === 'maintenance' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                <input type="radio" name="estado" value="maintenance" class="hidden" {{ request('estado') === 'maintenance' ? 'checked' : '' }} onchange="this.form.submit()">
                                <span
                                    class="w-3 h-3 rounded-full bg-yellow-500 shadow-[0_0_10px_rgba(234,179,8,0.5)]"></span>
                                <span class="text-gray-200">En Mantenimiento</span>
                            </label>
                            <label
                                class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('estado') === 'written_off' ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                <input type="radio" name="estado" value="written_off" class="hidden" {{ request('estado') === 'written_off' ? 'checked' : '' }} onchange="this.form.submit()">
                                <span
                                    class="w-3 h-3 rounded-full bg-red-500 shadow-[0_0_10px_rgba(239,68,68,0.5)]"></span>
                                <span class="text-gray-200">Dado de Baja</span>
                            </label>
                        </div>
                    </div>

                    <!-- Filter: Category -->
                    <div class="mb-4 border-b border-gray-700 pb-4 border-none" x-data="{ open: true }">
                        <button type="button" @click="open = !open"
                            class="flex items-center justify-between w-full text-left text-sm font-bold text-gray-400 uppercase tracking-wider mb-2 focus:outline-none">
                            <span>Categoría</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open"
                            class="space-y-2 mt-2 max-h-60 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">
                            <label
                                class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('categoria') === null ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                <input type="radio" name="categoria" value="" class="hidden" {{ request('categoria') === null ? 'checked' : '' }} onchange="this.form.submit()">
                                <span class="text-gray-200 font-medium">Todas</span>
                            </label>
                            @foreach($categories as $category)
                                <label
                                    class="flex items-center space-x-3 p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-colors {{ request('categoria') == $category->id ? 'bg-indigo-900/30 border-indigo-500' : '' }}">
                                    <input type="radio" name="categoria" value="{{ $category->id }}" class="hidden" {{ request('categoria') == $category->id ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <span class="text-gray-200">{{ $category->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-700 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-all shadow-lg shadow-indigo-500/30">
                            Aplicar Filtros
                        </button>
                        @if(request('estado') || request('categoria'))
                            <a href="{{ route('assets.index', ['search' => request('search')]) }}"
                                class="w-full py-3 text-center text-gray-400 hover:text-white font-medium hover:bg-gray-700 rounded-lg transition-colors">
                                Limpiar Filtros
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Crear Activo -->
        <x-modal name="create-asset-modal" :show="$errors->any()" focusable>
            <form method="POST" action="{{ route('assets.store') }}" class="p-6 bg-gray-800 text-gray-100"
                enctype="multipart/form-data" x-data="{
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
                                        resolve(compressedFile);
                                    }, 'image/jpeg', 0.8);
                                };
                            };
                        });
                    }
                }">
                @csrf

                <h2 class="text-lg font-medium text-gray-100 mb-4">
                    {{ __('Nuevo Activo') }}
                </h2>

                <!-- Foto -->
                <div class="mb-4">
                    <x-input-label for="foto" :value="__('Foto del Activo')" class="text-gray-300" />

                    <!-- Preview -->
                    <div class="mb-3" x-show="photoPreview" style="display: none;">
                        <span
                            class="block rounded-md w-full h-40 bg-cover bg-no-repeat bg-center mx-auto border border-gray-600"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" x-on:click.prevent="$refs.photo.click()" :disabled="isCompressing"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg border border-gray-600 transition-colors shadow-sm text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isCompressing">Seleccionar Imagen</span>
                            <span x-show="isCompressing">Procesando...</span>
                        </button>
                        <span x-show="!photoPreview" class="text-xs text-gray-500">Ningún archivo seleccionado</span>
                    </div>

                    <input id="foto" type="file" name="foto" class="hidden" x-ref="photo" accept="image/*" x-on:change="
                            const file = $refs.photo.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                                
                                compressImage(file).then(compressedFile => {
                                    this.isCompressing = false;
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(compressedFile);
                                    $refs.photo.files = dataTransfer.files;
                                });
                            }
                        " />
                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <x-input-label for="nombre" :value="__('Nombre del Activo')" class="text-gray-300" />
                        <x-text-input id="nombre"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="nombre" :value="old('nombre')" required autofocus
                            placeholder="Ej: Laptop Dell Latitude 5420" />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <!-- Categoría -->
                    <div>
                        <x-input-label for="categoria_id" :value="__('Categoría')" class="text-gray-300" />
                        <select id="categoria_id" name="categoria_id"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                            required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
                    </div>

                    <!-- Estado -->
                    <div>
                        <x-input-label for="estado" :value="__('Estado')" class="text-gray-300" />
                        <select id="estado" name="estado"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="available">Disponible</option>
                            <option value="assigned">Asignado</option>
                            <option value="maintenance">En Mantenimiento</option>
                            <option value="written_off">Dado de Baja</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                    </div>

                    <!-- Marca -->
                    <div>
                        <x-input-label for="marca" :value="__('Marca')" class="text-gray-300" />
                        <x-text-input id="marca"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="marca" :value="old('marca')" placeholder="Ej: Dell" />
                        <x-input-error :messages="$errors->get('marca')" class="mt-2" />
                    </div>

                    <!-- Modelo -->
                    <div>
                        <x-input-label for="modelo" :value="__('Modelo')" class="text-gray-300" />
                        <x-text-input id="modelo"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="modelo" :value="old('modelo')" placeholder="Ej: Latitude 5420" />
                        <x-input-error :messages="$errors->get('modelo')" class="mt-2" />
                    </div>

                    <!-- Número de Serie -->
                    <div class="md:col-span-2">
                        <x-input-label for="numero_serie" :value="__('Número de Serie')" class="text-gray-300" />
                        <x-text-input id="numero_serie"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="numero_serie" :value="old('numero_serie')"
                            placeholder="Número de serie del fabricante" required />
                        <x-input-error :messages="$errors->get('numero_serie')" class="mt-2" />
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <x-input-label for="ubicacion" :value="__('Ubicación')" class="text-gray-300" />
                        <x-text-input id="ubicacion"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="ubicacion" :value="old('ubicacion')" placeholder="Ej: Oficina Central" />
                        <x-input-error :messages="$errors->get('ubicacion')" class="mt-2" />
                    </div>

                    <!-- Fecha de Adquisición -->
                    <div>
                        <x-input-label for="fecha_adquisicion" :value="__('Fecha de Adquisición')"
                            class="text-gray-300" />
                        <x-text-input id="fecha_adquisicion"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="date" name="fecha_adquisicion" :value="old('fecha_adquisicion')" />
                        <x-input-error :messages="$errors->get('fecha_adquisicion')" class="mt-2" />
                    </div>

                    <!-- Valor Referencial -->
                    <div class="md:col-span-2">
                        <x-input-label for="valor_referencial" :value="__('Valor Referencial (CLP)')"
                            class="text-gray-300" />
                        <x-text-input id="valor_referencial"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="valor_referencial" :value="old('valor_referencial')" placeholder="0"
                            x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" />
                        <x-input-error :messages="$errors->get('valor_referencial')" class="mt-2" />
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2">
                        <x-input-label for="observaciones" :value="__('Observaciones')" class="text-gray-300" />
                        <textarea id="observaciones" name="observaciones" rows="3"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                            placeholder="Observaciones adicionales...">{{ old('observaciones') }}</textarea>
                        <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button x-on:click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 border-transparent">
                        {{ __('Guardar Activo') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Editar Activo -->
        <x-modal name="edit-asset-modal" :show="false" focusable>
            <form method="POST" :action="editAction" enctype="multipart/form-data" class="p-6 bg-gray-800 text-gray-100"
                x-data="{
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
                                        resolve(compressedFile);
                                    }, 'image/jpeg', 0.8);
                                };
                            };
                        });
                    }
                }">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-medium text-gray-100 mb-4">
                    {{ __('Editar Activo') }}
                </h2>

                <!-- Foto -->
                <div class="mb-4">
                    <x-input-label for="edit_foto" :value="__('Cambiar Foto (Opcional)')" class="text-gray-300" />

                    <!-- Preview Box -->
                    <div class="mb-3" x-show="photoPreview || editingAsset.foto_url" style="display: none;">
                        <span
                            class="block rounded-md w-full h-40 bg-cover bg-no-repeat bg-center mx-auto border border-gray-600"
                            x-bind:style="'background-image: url(\'' + (photoPreview || editingAsset.foto_url) + '\');'">
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" x-on:click.prevent="$refs.photoEdit.click()" :disabled="isCompressing"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg border border-gray-600 transition-colors shadow-sm text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isCompressing">Seleccionar Imagen</span>
                            <span x-show="isCompressing">Procesando...</span>
                        </button>

                        <div x-show="!photoPreview && !editingAsset.foto_url" class="text-xs text-gray-500">Ningún
                            archivo seleccionado</div>
                        <div x-show="editingAsset.foto_url && !photoPreview" class="text-xs text-gray-400 italic">Se
                            mantendrá la foto actual si no seleccionas otra</div>
                    </div>

                    <input id="edit_foto" type="file" name="foto" class="hidden" x-ref="photoEdit" accept="image/*"
                        x-on:change="
                            const file = $refs.photoEdit.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL(file);

                                compressImage(file).then(compressedFile => {
                                    this.isCompressing = false;
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(compressedFile);
                                    $refs.photoEdit.files = dataTransfer.files;
                                });
                            }
                         " />
                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Código Interno (Solo lectura) -->
                    <div>
                        <x-input-label for="edit_codigo_interno" :value="__('Código Interno')" class="text-gray-300" />
                        <x-text-input id="edit_codigo_interno"
                            class="block mt-1 w-full bg-gray-700 border-gray-600 text-gray-400 cursor-not-allowed"
                            type="text" x-model="editingAsset.codigo_interno" disabled />
                    </div>

                    <!-- Código de Barras (Solo lectura) -->
                    <div>
                        <x-input-label for="edit_codigo_barra" :value="__('Código de Barras')" class="text-gray-300" />
                        <x-text-input id="edit_codigo_barra"
                            class="block mt-1 w-full bg-gray-700 border-gray-600 text-gray-400 cursor-not-allowed"
                            type="text" x-model="editingAsset.codigo_barra" disabled />
                    </div>

                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <x-input-label for="edit_nombre" :value="__('Nombre del Activo')" class="text-gray-300" />
                        <x-text-input id="edit_nombre"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="nombre" x-model="editingAsset.nombre" required />
                    </div>

                    <!-- Categoría -->
                    <div>
                        <x-input-label for="edit_categoria_id" :value="__('Categoría')" class="text-gray-300" />
                        <select id="edit_categoria_id" name="categoria_id" x-model="editingAsset.categoria_id"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                            required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <x-input-label for="edit_estado" :value="__('Estado')" class="text-gray-300" />
                        <select id="edit_estado" name="estado" x-model="editingAsset.estado"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="available">Disponible</option>
                            <option value="assigned">Asignado</option>
                            <option value="maintenance">En Mantenimiento</option>
                            <option value="written_off">Dado de Baja</option>
                        </select>
                    </div>

                    <!-- Marca -->
                    <div>
                        <x-input-label for="edit_marca" :value="__('Marca')" class="text-gray-300" />
                        <x-text-input id="edit_marca"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="marca" x-model="editingAsset.marca" />
                    </div>

                    <!-- Modelo -->
                    <div>
                        <x-input-label for="edit_modelo" :value="__('Modelo')" class="text-gray-300" />
                        <x-text-input id="edit_modelo"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="modelo" x-model="editingAsset.modelo" />
                    </div>

                    <!-- Número de Serie -->
                    <div class="md:col-span-2">
                        <x-input-label for="edit_numero_serie" :value="__('Número de Serie')" class="text-gray-300" />
                        <x-text-input id="edit_numero_serie"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="numero_serie" x-model="editingAsset.numero_serie" required />
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <x-input-label for="edit_ubicacion" :value="__('Ubicación')" class="text-gray-300" />
                        <x-text-input id="edit_ubicacion"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="ubicacion" x-model="editingAsset.ubicacion" />
                    </div>

                    <!-- Fecha de Adquisición -->
                    <div>
                        <x-input-label for="edit_fecha_adquisicion" :value="__('Fecha de Adquisición')"
                            class="text-gray-300" />
                        <x-text-input id="edit_fecha_adquisicion"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="date" name="fecha_adquisicion" x-model="editingAsset.fecha_adquisicion" />
                    </div>

                    <!-- Valor Referencial -->
                    <div class="md:col-span-2">
                        <x-input-label for="edit_valor_referencial" :value="__('Valor Referencial (CLP)')"
                            class="text-gray-300" />
                        <x-text-input id="edit_valor_referencial"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            type="text" name="valor_referencial" x-model="editingAsset.valor_referencial"
                            x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" />
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2">
                        <x-input-label for="edit_observaciones" :value="__('Observaciones')" class="text-gray-300" />
                        <textarea id="edit_observaciones" name="observaciones" rows="3"
                            x-model="editingAsset.observaciones"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button @click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 border-transparent">
                        {{ __('Actualizar Activo') }}
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
                    {{ __('El activo se moverá a la papelera. Podrás restaurarlo después si lo necesitas.') }}
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
        <!-- Modal Ver Detalle Activo -->
        <x-modal name="view-asset-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-2xl font-bold text-gray-100">
                        {{ __('Detalle del Activo') }}
                    </h2>
                    <button @click="$dispatch('close')" class="text-gray-400 hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna Izquierda: Foto e Información Principal -->
                    <div class="space-y-6">
                        <!-- Foto -->
                        <div
                            class="flex justify-center bg-gray-900 p-4 rounded-lg border border-gray-700 min-h-[200px] items-center">
                            <template x-if="editingAsset.foto_url">
                                <img :src="editingAsset.foto_url" class="max-h-64 rounded-lg object-contain shadow-md">
                            </template>
                            <template x-if="!editingAsset.foto_url">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm">Sin fotografía</span>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Código Interno</label>
                            <p class="text-lg font-mono text-blue-400" x-text="editingAsset.codigo_interno"></p>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Código de Barras</label>
                            <p class="text-sm font-mono text-gray-300" x-text="editingAsset.codigo_barra"></p>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Estado</label>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-md border"
                                    :class="{
                                        'text-green-400 bg-green-900/30 border-green-900': editingAsset.estado === 'available',
                                        'text-blue-400 bg-blue-900/30 border-blue-900': editingAsset.estado === 'assigned',
                                        'text-yellow-400 bg-yellow-900/30 border-yellow-900': editingAsset.estado === 'maintenance',
                                        'text-red-400 bg-red-900/30 border-red-900': editingAsset.estado === 'written_off'
                                    }"
                                    x-text="editingAsset.estado === 'available' ? 'DISPONIBLE' : 
                                           (editingAsset.estado === 'assigned' ? 'ASIGNADO' : 
                                           (editingAsset.estado === 'maintenance' ? 'MANTENIMIENTO' : 'DADO DE BAJA'))">
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Columna Derecha: Detalles -->
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Nombre</label>
                            <p class="text-gray-200 text-lg font-semibold" x-text="editingAsset.nombre"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase font-bold">Marca</label>
                                <p class="text-gray-300" x-text="editingAsset.marca || 'N/A'"></p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase font-bold">Modelo</label>
                                <p class="text-gray-300" x-text="editingAsset.modelo || 'N/A'"></p>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Número de Serie</label>
                            <p class="text-gray-300 font-mono" x-text="editingAsset.numero_serie || 'N/A'"></p>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Ubicación</label>
                            <p class="text-gray-300" x-text="editingAsset.ubicacion || 'N/A'"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase font-bold">Fecha Adquisición</label>
                                <p class="text-gray-300" x-text="editingAsset.fecha_adquisicion || 'N/A'"></p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase font-bold">Valor Referencial</label>
                                <p class="text-gray-300"
                                    x-text="editingAsset.valor_referencial ? '$ ' + editingAsset.valor_referencial : 'N/A'">
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold">Observaciones</label>
                            <p class="text-gray-400 text-sm bg-gray-900 p-3 rounded border border-gray-700 mt-1 min-h-[80px]"
                                x-text="editingAsset.observaciones || 'Sin observaciones'"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <x-secondary-button @click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cerrar') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>
        <!-- Modal Asignar Activo -->
        <x-modal name="assign-asset-modal" :show="$errors->has('tipo_asignacion')" focusable>
            <form method="POST" :action="assignAction" class="p-6 bg-gray-800 text-gray-100"
                x-data="{ assignmentType: 'user', isNewWorker: false }">
                @csrf

                <h2 class="text-lg font-medium text-gray-100 mb-4">
                    {{ __('Asignar Activo') }}
                </h2>

                <p class="mb-4 text-sm text-gray-400">
                    Estás asignando el activo: <span class="font-bold text-white"
                        x-text="assignmentAsset.nombre"></span>
                    (<span class="font-mono text-blue-400" x-text="assignmentAsset.codigo_interno"></span>)
                </p>

                <!-- Tipo de Asignación -->
                <div class="mb-4">
                    <span class="block text-sm font-medium text-gray-300 mb-2">Asignar a:</span>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipo_asignacion" value="user" x-model="assignmentType"
                                class="form-radio text-blue-600 bg-gray-900 border-gray-700">
                            <span class="ml-2 text-gray-300">Usuario del Sistema</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tipo_asignacion" value="worker" x-model="assignmentType"
                                class="form-radio text-blue-600 bg-gray-900 border-gray-700">
                            <span class="ml-2 text-gray-300">Trabajador</span>
                        </label>
                    </div>
                </div>

                <!-- Campos Usuario Sistema -->
                <div x-show="assignmentType === 'user'" class="mb-4 space-y-4">
                    <div>
                        <x-input-label for="usuario_id" :value="__('Seleccionar Usuario')" class="text-gray-300" />
                        <select id="usuario_id" name="usuario_id"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="">Seleccione un usuario...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('usuario_id')" class="mt-2" />
                    </div>
                </div>

                <!-- Campos Trabajador -->
                <div x-show="assignmentType === 'worker'" class="mb-4 space-y-4">

                    <!-- Selector de Trabajador Existente -->
                    <div x-show="!isNewWorker">
                        <x-input-label for="worker_id_select" :value="__('Seleccionar Trabajador')"
                            class="text-gray-300" />
                        <div class="flex items-center gap-2">
                            <select id="worker_id_select" name="worker_id_select"
                                class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="">Seleccione un trabajador...</option>
                                @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}">{{ $worker->nombre }} ({{ $worker->rut }})</option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error :messages="$errors->get('worker_id_select')" class="mt-2" />
                    </div>

                    <!-- Checkbox Nuevo Trabajador -->
                    <div class="flex items-center mt-2">
                        <input id="is_new_worker" type="checkbox" name="is_new_worker" value="1" x-model="isNewWorker"
                            class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="is_new_worker" class="ml-2 text-sm font-medium text-gray-300">Trabajador Nuevo
                            (Registrar)</label>
                    </div>

                    <!-- Campos Nuevo Trabajador -->
                    <div x-show="isNewWorker" class="space-y-4 border-l-2 border-blue-500 pl-4 mt-2 transition-all">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="trabajador_nombre" :value="__('Nombre Completo')"
                                    class="text-gray-300" />
                                <x-text-input id="trabajador_nombre"
                                    class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="text"
                                    name="trabajador_nombre" placeholder="Juan Pérez" />
                                <x-input-error :messages="$errors->get('trabajador_nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="trabajador_rut" :value="__('RUT')" class="text-gray-300" />
                                <x-text-input id="trabajador_rut"
                                    class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="text"
                                    name="trabajador_rut" placeholder="12.345.678-9" />
                                <x-input-error :messages="$errors->get('trabajador_rut')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="trabajador_departamento" :value="__('Departamento')"
                                    class="text-gray-300" />
                                <x-text-input id="trabajador_departamento"
                                    class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="text"
                                    name="trabajador_departamento" placeholder="Operaciones" />
                                <x-input-error :messages="$errors->get('trabajador_departamento')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="trabajador_cargo" :value="__('Cargo')" class="text-gray-300" />
                                <x-text-input id="trabajador_cargo"
                                    class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100" type="text"
                                    name="trabajador_cargo" placeholder="Supervisor" />
                                <x-input-error :messages="$errors->get('trabajador_cargo')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fechas y Observaciones -->
                <div class="space-y-4 border-t border-gray-700 pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="fecha_entrega" :value="__('Fecha Entrega (Desde)')"
                                class="text-gray-300" />
                            <x-text-input id="fecha_entrega"
                                class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100"
                                type="datetime-local" name="fecha_entrega" value="{{ now()->format('Y-m-d\TH:i') }}"
                                required />
                            <x-input-error :messages="$errors->get('fecha_entrega')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="fecha_estimada_devolucion" :value="__('Fecha Estimada (Hasta) - Opcional')" class="text-gray-300" />
                            <x-text-input id="fecha_estimada_devolucion"
                                class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100"
                                type="datetime-local" name="fecha_estimada_devolucion" />
                            <x-input-error :messages="$errors->get('fecha_estimada_devolucion')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="assign_observaciones" :value="__('Observaciones de Entrega')"
                            class="text-gray-300" />
                        <textarea id="assign_observaciones" name="observaciones" rows="2"
                            class="block mt-1 w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                            placeholder="Estado inicial, accesorios, etc..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <x-secondary-button @click="$dispatch('close')"
                        class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 border-transparent">
                        {{ __('Confirmar Asignación') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Ver Detalles de Asignación -->
        <x-modal name="view-assignment-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100">
                <template x-if="assignmentAsset && assignmentAsset.active_assignment">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <h2 class="text-2xl font-bold text-gray-100 flex items-center gap-2">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                                {{ __('Detalle de Asignación') }}
                            </h2>
                            <button @click="$dispatch('close')"
                                class="text-gray-400 hover:text-gray-200 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Columna Izquierda: Información del Activo -->
                            <div class="space-y-6">
                                <div class="bg-gray-700/50 p-4 rounded-xl border border-gray-600">
                                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Activo Asignado
                                    </h3>

                                    <div class="flex flex-col items-center mb-4">
                                        <template x-if="assignmentAsset.foto_url">
                                            <img :src="assignmentAsset.foto_url"
                                                class="h-32 w-auto object-contain rounded-lg shadow-lg bg-gray-800 p-2 border border-gray-600">
                                        </template>
                                        <template x-if="!assignmentAsset.foto_url">
                                            <div
                                                class="h-32 w-32 rounded-lg bg-gray-800 flex items-center justify-center border border-gray-600">
                                                <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="space-y-2">
                                        <p class="text-xl font-bold text-center text-white"
                                            x-text="assignmentAsset.nombre"></p>
                                        <p class="text-sm text-center text-gray-400">
                                            <span x-text="assignmentAsset.marca"></span> <span
                                                x-text="assignmentAsset.modelo"></span>
                                        </p>
                                        <div class="flex justify-center gap-2 mt-2">
                                            <span
                                                class="px-2 py-1 bg-gray-800 rounded text-xs text-blue-300 font-mono border border-blue-900"
                                                x-text="assignmentAsset.codigo_interno"></span>
                                            <span
                                                class="px-2 py-1 bg-gray-800 rounded text-xs text-gray-300 font-mono border border-gray-600"
                                                x-text="assignmentAsset.codigo_barra"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-700/30 p-4 rounded-xl border border-gray-700">
                                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        Observaciones de
                                        Asignación</h4>
                                    <p class="text-gray-300 text-sm italic"
                                        x-text="assignmentAsset.active_assignment.observations || 'Sin observaciones registradas.'">
                                    </p>
                                </div>
                            </div>

                            <!-- Columna Derecha: Información del Usuario y Fechas -->
                            <div class="space-y-6">
                                <!-- Tarjeta de Usuario/Trabajador -->
                                <div
                                    class="bg-gray-700/50 p-5 rounded-xl border border-gray-600 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-2 opacity-10">
                                        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>

                                    <h3
                                        class="text-lg font-semibold text-white mb-4 flex items-center gap-2 relative z-10">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.356 1.763-1 2.404C9.288 8.163 8.665 8 8 8S6.712 8.163 6 7.404A4.002 4.002 0 0110 6z">
                                            </path>
                                        </svg>
                                        Asignado A
                                    </h3>

                                    <div class="flex items-start gap-4 relative z-10">
                                        <div class="flex-shrink-0">
                                            <template x-if="assignmentAsset.active_assignment.photo_url">
                                                <img :src="assignmentAsset.active_assignment.photo_url"
                                                    class="h-16 w-16 rounded-full object-cover border-2 border-blue-500 shadow-sm">
                                            </template>
                                            <template x-if="!assignmentAsset.active_assignment.photo_url">
                                                <div
                                                    class="h-16 w-16 rounded-full bg-blue-900/50 flex items-center justify-center border-2 border-blue-500/50 text-blue-200 font-bold text-xl">
                                                    <span
                                                        x-text="(assignmentAsset.active_assignment.assigned_to || '?').charAt(0)"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-lg font-bold text-white truncate"
                                                x-text="assignmentAsset.active_assignment.assigned_to">
                                            </p>
                                            <p class="text-sm text-blue-300 font-mono"
                                                x-text="assignmentAsset.active_assignment.rut || 'RUT no registrado'">
                                            </p>
                                            <div class="mt-2 space-y-1">
                                                <p class="text-xs text-gray-400 flex items-center gap-1"
                                                    x-show="assignmentAsset.active_assignment.cargo">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <span x-text="assignmentAsset.active_assignment.cargo"></span>
                                                </p>
                                                <p class="text-xs text-gray-400 flex items-center gap-1"
                                                    x-show="assignmentAsset.active_assignment.department">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                    <span x-text="assignmentAsset.active_assignment.department"></span>
                                                </p>
                                                <p class="text-xs text-gray-400 flex items-center gap-1"
                                                    x-show="assignmentAsset.active_assignment.phone">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                        </path>
                                                    </svg>
                                                    <span x-text="assignmentAsset.active_assignment.phone"></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fechas y Plazos -->
                                <div class="bg-gray-700/50 p-5 rounded-xl border border-gray-600">
                                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Plazos de Asignación
                                    </h3>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-gray-400 uppercase font-bold block mb-1">Fecha
                                                Entrega</label>
                                            <div class="flex items-center gap-2 text-white">
                                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                <span x-text="assignmentAsset.active_assignment.start_date"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="text-xs text-gray-400 uppercase font-bold block mb-1">Devolución
                                                Estimada</label>
                                            <template x-if="assignmentAsset.active_assignment.end_date">
                                                <div class="flex items-center gap-2 text-white">
                                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                                    <span x-text="assignmentAsset.active_assignment.end_date"></span>
                                                </div>
                                            </template>
                                            <template x-if="!assignmentAsset.active_assignment.end_date">
                                                <span class="text-gray-500 italic">Indefinido</span>
                                            </template>
                                        </div>
                                    </div>

                                    <template x-if="assignmentAsset.active_assignment.end_date">
                                        <div class="mt-4 pt-4 border-t border-gray-600">
                                            <div class="flex justify-between items-center bg-gray-800 p-3 rounded-lg border"
                                                :class="{
                                            'border-green-700 bg-green-900/20': assignmentAsset.active_assignment.days_remaining > 5,
                                            'border-yellow-700 bg-yellow-900/20': assignmentAsset.active_assignment.days_remaining <= 5 && assignmentAsset.active_assignment.days_remaining >= 0,
                                            'border-red-700 bg-red-900/20': assignmentAsset.active_assignment.days_remaining < 0
                                        }">
                                                <span class="text-sm font-medium text-gray-300">Tiempo Restante:</span>
                                                <span class="font-bold text-lg" :class="{
                                                'text-green-400': assignmentAsset.active_assignment.days_remaining > 5,
                                                'text-yellow-400': assignmentAsset.active_assignment.days_remaining <= 5 && assignmentAsset.active_assignment.days_remaining >= 0,
                                                'text-red-400': assignmentAsset.active_assignment.days_remaining < 0
                                            }" x-text="assignmentAsset.active_assignment.days_remaining < 0 ? Math.abs(assignmentAsset.active_assignment.days_remaining) + ' días vencido' : assignmentAsset.active_assignment.days_remaining + ' días'">
                                                </span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <div>
                                <x-danger-button
                                    @click="$dispatch('open-modal', 'confirm-cancel-assignment-modal'); cancelAssignmentAction = `{{ url('/assets') }}/${assignmentAsset.id}/cancel-assignment`"
                                    class="bg-red-600 hover:bg-red-500">
                                    {{ __('Terminar Asignación') }}
                                </x-danger-button>
                            </div>
                            <div class="flex gap-3">
                                <a :href="`{{ url('/assets') }}/${assignmentAsset.id}/history`"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-gray-600 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    {{ __('Historial') }}
                                </a>
                                <button
                                    @click="$dispatch('open-modal', 'edit-assignment-modal'); updateAssignmentAction = `{{ url('/assets') }}/${assignmentAsset.id}/assignment/update`"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 focus:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Editar') }}
                                </button>
                                <x-primary-button @click="$dispatch('close')" class="bg-blue-600 hover:bg-blue-500">
                                    {{ __('Entendido') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
            </div>
            </template>
    </div>
    </x-modal>

    <!-- Modal Confirmación de Cancelación -->
    <x-modal name="confirm-cancel-assignment-modal" focusable>
        <div class="p-6 bg-gray-800 text-gray-100">
            <h2 class="text-lg font-medium text-red-500 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                {{ __('¿Terminar Asignación?') }}
            </h2>

            <p class="mb-6 text-gray-300">
                {{ __('¿Estás seguro de que deseas terminar esta asignación? El activo volverá a estar disponible para ser asignado nuevamente.') }}
            </p>

            <form method="POST" :action="cancelAssignmentAction" class="w-full" enctype="multipart/form-data" x-data="{
                    photos: [],
                    photoPreviews: [],
                    isCompressing: false,
                    maxPhotos: 5,
                    
                    async compressImage(file) {
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
                                        resolve(compressedFile);
                                    }, 'image/jpeg', 0.8);
                                };
                            };
                        });
                    },
                    
                    async handlePhotoSelection(event) {
                        const files = Array.from(event.target.files);
                        
                        if (this.photos.length + files.length > this.maxPhotos) {
                            alert(`Máximo ${this.maxPhotos} fotos permitidas`);
                            this.$refs.photoInput.value = '';
                            return;
                        }
                        
                        this.isCompressing = true;
                        
                        for (const file of files) {
                            const compressed = await this.compressImage(file);
                            this.photos.push(compressed);
                            
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.photoPreviews.push(e.target.result);
                            };
                            reader.readAsDataURL(compressed);
                        }
                        
                        this.isCompressing = false;
                        this.updateFileInput();
                    },
                    
                    removePhoto(index) {
                        this.photos.splice(index, 1);
                        this.photoPreviews.splice(index, 1);
                        this.updateFileInput();
                    },
                    
                    updateFileInput() {
                        const dataTransfer = new DataTransfer();
                        this.photos.forEach(photo => dataTransfer.items.add(photo));
                        this.$refs.photoInput.files = dataTransfer.files;
                    }
                }">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="estado_devolucion" :value="__('Estado de Devolución')" class="text-gray-300" />
                    <select id="estado_devolucion" name="estado_devolucion"
                        class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        <option value="good" selected>{{ __('Bueno') }}</option>
                        <option value="regular">{{ __('Regular') }}</option>
                        <option value="bad">{{ __('Malo') }}</option>
                        <option value="damaged">{{ __('Dañado') }}</option>
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="comentarios_devolucion" :value="__('Comentarios / Incidentes')"
                        class="text-gray-300" />
                    <textarea id="comentarios_devolucion" name="comentarios_devolucion" rows="3"
                        class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Describa el estado o cualquier incidente..."></textarea>
                </div>

                <!-- Sección de Fotos -->
                <div class="mb-6 border-t border-gray-700 pt-4">
                    <x-input-label :value="__('Fotos de Devolución (Opcional)')" class="text-gray-300 mb-3" />

                    <!-- Botón de selección y contador -->
                    <div class="flex items-center gap-3 mb-3">
                        <button type="button" x-on:click="$refs.photoInput.click()"
                            :disabled="isCompressing || photos.length >= maxPhotos"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span x-show="!isCompressing">Agregar Fotos</span>
                            <span x-show="isCompressing">Procesando...</span>
                        </button>

                        <span class="text-sm" :class="photos.length >= maxPhotos ? 'text-yellow-500' : 'text-gray-400'"
                            x-text="`${photos.length} / ${maxPhotos} fotos`">
                        </span>
                    </div>

                    <!-- Input oculto -->
                    <input type="file" name="photos[]" multiple accept="image/*" class="hidden" x-ref="photoInput"
                        x-on:change="handlePhotoSelection($event)" />

                    <!-- Grid de previsualizaciones -->
                    <div x-show="photoPreviews.length > 0" class="grid grid-cols-3 gap-3">
                        <template x-for="(preview, index) in photoPreviews" :key="index">
                            <div class="relative group">
                                <img :src="preview" alt="Preview"
                                    class="w-full h-24 object-cover rounded-lg border border-gray-600">

                                <!-- Botón eliminar -->
                                <button type="button" x-on:click="removePhoto(index)"
                                    class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full p-1 shadow-lg transition-all opacity-0 group-hover:opacity-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Puedes subir hasta 5 fotos (JPG, PNG). Las imágenes se optimizarán automáticamente.
                    </p>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <x-secondary-button @click="$dispatch('close')">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-danger-button class="bg-red-600 hover:bg-red-500">
                        {{ __('Sí, Terminar Asignación') }}
                    </x-danger-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Modal Editar Asignación -->
    <x-modal name="edit-assignment-modal" focusable>
        <div class="p-6 bg-gray-800 text-gray-100">
            <template x-if="assignmentAsset && assignmentAsset.active_assignment">
                <div>
                    <h2 class="text-lg font-medium text-white mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        {{ __('Editar Asignación') }}
                    </h2>

                    <form method="POST" :action="updateAssignmentAction">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="edit_fecha_entrega" :value="__('Fecha Entrega')"
                                    class="text-gray-300" />
                                <input id="edit_fecha_entrega" type="date" name="fecha_entrega"
                                    :value="assignmentAsset.active_assignment.start_date"
                                    class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required />
                            </div>
                            <div>
                                <x-input-label for="edit_fecha_estimada_devolucion" :value="__('Devolución Estimada')"
                                    class="text-gray-300" />
                                <input id="edit_fecha_estimada_devolucion" type="date" name="fecha_estimada_devolucion"
                                    :value="assignmentAsset.active_assignment.end_date"
                                    class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="edit_observaciones" :value="__('Observaciones')"
                                class="text-gray-300" />
                            <textarea id="edit_observaciones" name="observaciones" rows="3"
                                class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                x-text="assignmentAsset.active_assignment.observations"></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <x-secondary-button @click="$dispatch('close')">
                                {{ __('Cancelar') }}
                            </x-secondary-button>

                            <x-primary-button class="bg-blue-600 hover:bg-blue-500">
                                {{ __('Guardar Cambios') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </x-modal>

    <!-- Modal Error RUT -->
    <x-modal name="rut-error-modal" focusable>
        <div class="p-6 bg-gray-800 text-gray-100">
            <h2 class="text-lg font-medium text-red-500 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ __('RUT Duplicado') }}
            </h2>

            <p class="mb-6 text-gray-300">
                Este RUT pertenece a un <strong>USUARIO</strong> del sistema. <br>
                Por favor, selecciona la opción <strong>"Usuario del Sistema"</strong> en lugar de "Trabajador".
            </p>

            <div class="flex justify-end">
                <x-primary-button x-on:click="$dispatch('close')"
                    class="bg-red-600 hover:bg-red-700 border-transparent">
                    {{ __('Entendido') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>
    <!-- Modal Resolver Alerta -->
    <x-modal name="resolve-issue-modal" focusable>
        <div class="p-6 bg-gray-800 text-gray-100" x-data="{ resolutionType: 'maintenance' }">
            <h2 class="text-lg font-medium text-orange-500 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                {{ __('Gestionar Alerta de Activo') }}
            </h2>

            <p class="mb-4 text-sm text-gray-400">
                El activo <span class="font-bold text-white" x-text="editingAsset.nombre"></span> fue reportado con
                problemas en su última devolución.
                ¿Qué acción deseas tomar?
            </p>

            <div class="mb-6 flex space-x-4">
                <label
                    class="flex items-center p-3 rounded-lg border border-gray-700 cursor-pointer hover:bg-gray-700/50 transition-colors w-1/2"
                    :class="{'bg-indigo-900/30 border-indigo-500': resolutionType === 'maintenance'}">
                    <input type="radio" name="resolution_type" value="maintenance" x-model="resolutionType"
                        class="hidden">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full border border-gray-400 mr-3 flex items-center justify-center p-0.5"
                            :class="{'border-indigo-500': resolutionType === 'maintenance'}">
                            <div class="w-full h-full rounded-full bg-indigo-500"
                                x-show="resolutionType === 'maintenance'"></div>
                        </div>
                        <div>
                            <span class="block font-bold text-white">Enviar a Mantención</span>
                            <span class="text-xs text-gray-400">Reparación o revisión técnica</span>
                        </div>
                    </div>
                </label>

                <label
                    class="flex items-center p-3 rounded-lg border border-gray-700 cursor-pointer hover:bg-gray-700/50 transition-colors w-1/2"
                    :class="{'bg-red-900/30 border-red-500': resolutionType === 'write_off'}">
                    <input type="radio" name="resolution_type" value="write_off" x-model="resolutionType"
                        class="hidden">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full border border-gray-400 mr-3 flex items-center justify-center p-0.5"
                            :class="{'border-red-500': resolutionType === 'write_off'}">
                            <div class="w-full h-full rounded-full bg-red-500" x-show="resolutionType === 'write_off'">
                            </div>
                        </div>
                        <div>
                            <span class="block font-bold text-white">Dar de Baja</span>
                            <span class="text-xs text-gray-400">Retirar del inventario</span>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Formulario Mantención -->
            <div x-show="resolutionType === 'maintenance'" x-transition>
                <form method="POST" :action="`{{ url('/assets') }}/${editingAsset.id}/maintenance`">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="fecha_mantencion" :value="__('Fecha de Mantención')"
                            class="text-gray-300" />
                        <input id="fecha_mantencion" type="date" name="fecha_mantencion"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="motivo_mantencion" :value="__('Motivo / Descripción del Daño')"
                            class="text-gray-300" />
                        <textarea id="motivo_mantencion" name="motivo_mantencion" rows="3"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required placeholder="Detallar el daño reportado y lo que se reparará..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <x-secondary-button @click="$dispatch('close')">
                            {{ __('Cancelar') }}
                        </x-secondary-button>

                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-500">
                            {{ __('Confirmar Envío a Mantención') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Formulario Dar de Baja -->
            <div x-show="resolutionType === 'write_off'" x-transition>
                <form method="POST" :action="`{{ url('/assets') }}/${editingAsset.id}/write-off`">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="baja_fecha" :value="__('Fecha de Baja')" class="text-gray-300" />
                        <input id="baja_fecha" type="date" name="fecha"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                            value="{{ date('Y-m-d') }}" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="baja_motivo" :value="__('Motivo de la Baja')" class="text-gray-300" />
                        <textarea id="baja_motivo" name="motivo" rows="3"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500"
                            required placeholder="Explique por qué se da de baja..."></textarea>
                    </div>

                    <div class="p-4 bg-red-900/20 border border-red-800 rounded-lg mb-4">
                        <p class="text-red-300 text-sm">
                            <strong class="block mb-1">⚠️ Acción Irreversible</strong>
                            El activo cambiará a estado "Dado de Baja". Permanecerá en el sistema pero no podrá ser
                            asignado nuevamente.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <x-secondary-button @click="$dispatch('close')">
                            {{ __('Cancelar') }}
                        </x-secondary-button>

                        <x-danger-button class="bg-red-600 hover:bg-red-500">
                            {{ __('Confirmar Baja Definitiva') }}
                        </x-danger-button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>

    <!-- Modal Finalizar Mantención -->
    <x-modal name="finish-maintenance-modal" focusable>
        <div class="p-6 bg-gray-800 text-gray-100">
            <h2 class="text-lg font-medium text-green-500 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ __('Finalizar Mantención') }}
            </h2>

            <p class="mb-4 text-sm text-gray-400">
                Registra los detalles de la solución para el activo <span class="font-bold text-white"
                    x-text="editingAsset.nombre"></span>.
                Al finalizar, el activo volverá a estar <strong>DISPONIBLE</strong>.
            </p>

            <form method="POST" :action="`{{ url('/assets') }}/${editingAsset.id}/maintenance/finish`"
                enctype="multipart/form-data" x-data="{
                    photos: [],
                    photoPreviews: [],
                    isCompressing: false,
                    maxPhotos: 5,
                    
                    async compressImage(file) {
                        return new Promise((resolve) => {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const img = new Image();
                                img.onload = () => {
                                    const canvas = document.createElement('canvas');
                                    let width = img.width;
                                    let height = img.height;
                                    const maxWidth = 1920;
                                    if (width > maxWidth) {
                                        height = (height * maxWidth) / width;
                                        width = maxWidth;
                                    }
                                    canvas.width = width;
                                    canvas.height = height;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(img, 0, 0, width, height);
                                    canvas.toBlob((blob) => {
                                        resolve(new File([blob], file.name, {
                                            type: 'image/jpeg',
                                            lastModified: Date.now()
                                        }));
                                    }, 'image/jpeg', 0.8);
                                };
                                img.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        });
                    },

                    async handlePhotoSelection(event) {
                        const files = Array.from(event.target.files);
                        if (this.photos.length + files.length > this.maxPhotos) {
                            alert(`Solo puedes subir un máximo de ${this.maxPhotos} fotos`);
                            return;
                        }
                        
                        this.isCompressing = true;
                        
                        for (const file of files) {
                            const compressed = await this.compressImage(file);
                            this.photos.push(compressed);
                            
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.photoPreviews.push(e.target.result);
                            };
                            reader.readAsDataURL(compressed);
                        }
                        
                        this.isCompressing = false;
                        event.target.value = '';
                    },

                    removePhoto(index) {
                        this.photos.splice(index, 1);
                        this.photoPreviews.splice(index, 1);
                    }
                }" @submit="
                    const dataTransfer = new DataTransfer();
                    photos.forEach(photo => dataTransfer.items.add(photo));
                    $refs.photoInput.files = dataTransfer.files;
                ">
                @csrf

                <div class="mb-4">
                    <x-input-label for="fecha_termino" :value="__('Fecha de Término')" class="text-gray-300" />
                    <input id="fecha_termino" type="date" name="fecha_termino"
                        class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                        value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="mb-4">
                    <x-input-label for="detalles_solucion" :value="__('Detalles de la Solución')"
                        class="text-gray-300" />
                    <textarea id="detalles_solucion" name="detalles_solucion" rows="3"
                        class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                        required placeholder="Describe qué reparaciones se realizaron..."></textarea>
                </div>

                <div class="mb-4">
                    <x-input-label for="costo" :value="__('Costo Total (Opcional)')" class="text-gray-300" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">$</span>
                        </div>
                        <input id="costo" type="number" name="costo" min="0" placeholder="0"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-300 pl-7 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" />
                    </div>
                </div>

                {{-- Sección de Fotos --}}
                <div class="mb-4">
                    <x-input-label :value="__('Fotos del Activo (Opcional)')" class="text-gray-300" />
                    <p class="text-xs text-gray-500 mb-2">Hasta 5 fotos del activo en buen estado.</p>

                    <input type="file" name="photos[]" multiple accept="image/*" class="hidden" x-ref="photoInput"
                        @change="handlePhotoSelection">

                    <button type="button" @click="$refs.photoInput.click()" :disabled="photos.length >= maxPhotos"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Agregar Fotos
                    </button>

                    <div x-show="isCompressing" class="mt-2 text-sm text-yellow-400">
                        ⏳ Procesando imágenes...
                    </div>

                    <div x-show="photos.length > 0" class="mt-3">
                        <p class="text-sm text-gray-400 mb-2" x-text="`${photos.length} / ${maxPhotos} fotos`"></p>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <template x-for="(preview, index) in photoPreviews" :key="index">
                                <div class="relative group">
                                    <img :src="preview" class="w-full h-24 object-cover rounded border border-gray-600">
                                    <button type="button" @click="removePhoto(index)"
                                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <x-secondary-button @click="$dispatch('close')">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-primary-button class="bg-green-600 hover:bg-green-500">
                        {{ __('Finalizar y Habilitar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Modal Detalle Baja -->
    <x-modal name="write-off-details-modal" focusable>
        <div class="p-6 bg-gray-800 text-gray-100" x-data="{ assetWithDetails: {} }"
            x-effect="if(show) { assetWithDetails = selectedWriteOffAsset; }">

            <h2 class="text-lg font-medium text-gray-100 mb-4 flex items-center gap-2">
                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Detalle de Baja de Activo') }}
            </h2>

            <div class="mb-6 bg-red-900/10 border border-red-800/50 rounded-lg p-4">
                <p class="text-gray-300 text-sm mb-2">Activo:</p>
                <p class="text-xl font-bold text-white mb-1" x-text="assetWithDetails?.nombre || 'Cargando...'"></p>
                <p class="text-gray-400 font-mono text-sm" x-text="assetWithDetails?.codigo_interno || ''"></p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 uppercase">Fecha de Baja</label>
                    <p class="mt-1 text-lg text-white"
                        x-text="assetWithDetails?.write_off?.fecha ? new Date(assetWithDetails.write_off.fecha).toLocaleDateString() : 'N/A'">
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 uppercase">Motivo</label>
                    <div class="mt-1 p-3 bg-gray-900 rounded-md border border-gray-700 text-gray-200 min-h-[80px]">
                        <p x-text="assetWithDetails?.write_off?.motivo || 'Sin motivo registrado'"></p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button @click="$dispatch('close')" class="bg-gray-700 hover:bg-gray-600 text-white">
                    {{ __('Cerrar') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

    <!-- Modal Confirmación Eliminar -->
    <x-modal name="confirm-asset-deletion" focusable>
        <form method="post" :action="deleteAction" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('¿Estás seguro de que quieres eliminar este activo?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Una vez eliminado, el activo se moverá a la papelera. Podrás restaurarlo después si es necesario.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Eliminar Activo') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Generación Batch -->
    <x-modal name="batch-barcode-modal" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 12h16m-7 6h6M5 18v2m14-2v2">
                    </path>
                </svg>
                Generar Códigos de Barra
            </h2>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                Se generarán etiquetas para <span class="font-bold text-purple-600"
                    x-text="selectedAssets.length"></span> activos seleccionados.
                Elige el tamaño de la etiqueta:
            </p>

            <div class="mt-6 space-y-3">
                <!-- Opciones de Tamaño -->
                <label
                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    :class="barcodeSize === 'small' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700'">
                    <input type="radio" name="size" value="small" x-model="barcodeSize"
                        class="text-purple-600 focus:ring-purple-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Pequeño
                            (50x25mm)</span>
                        <span class="block text-xs text-gray-500">Info esencial: Nombre corto, códigos.</span>
                    </div>
                </label>

                <label
                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    :class="barcodeSize === 'medium' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700'">
                    <input type="radio" name="size" value="medium" x-model="barcodeSize"
                        class="text-purple-600 focus:ring-purple-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Mediano (70x42mm) -
                            Recomendado</span>
                        <span class="block text-xs text-gray-500">Incluye categoría y ubicación.</span>
                    </div>
                </label>

                <label
                    class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    :class="barcodeSize === 'large' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700'">
                    <input type="radio" name="size" value="large" x-model="barcodeSize"
                        class="text-purple-600 focus:ring-purple-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Grande
                            (100x50mm)</span>
                        <span class="block text-xs text-gray-500">Info completa + Número de Serie.</span>
                    </div>
                </label>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <form method="POST" action="{{ route('assets.barcodes.batch') }}" target="_blank"
                    @submit="$dispatch('close')">
                    @csrf
                    <template x-for="id in selectedAssets" :key="id">
                        <input type="hidden" name="asset_ids[]" :value="id">
                    </template>
                    <input type="hidden" name="size" :value="barcodeSize">

                    <x-primary-button class="bg-purple-600 hover:bg-purple-700">
                        {{ __('Descargar PDF') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </x-modal>
    </div>
</x-app-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rutValidation', () => ({
            // Lógica auxiliar si fuera necesario, pero lo manejamos directo en los inputs o con JS vainilla abajo para accesar al DOM facilmente
        }));
    });

    // Función formato RUT
    function formatRut(rut) {
        // Eliminar todo lo que no sea ni número ni k/K
        let value = rut.replace(/[^0-9kK]/g, '');

        if (value.length < 2) return value;

        // Separar cuerpo y dígito verificador
        let body = value.slice(0, -1);
        let dv = value.slice(-1).toUpperCase();

        // Formatear cuerpo con puntos
        rut = body.replace(/\B(?=(\d{3})+(?!\d))/g, ".") + '-' + dv;
        return rut;
    }

    // Listener para el input de RUT
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'trabajador_rut') {
            let input = e.target;
            let originalValue = input.value;
            let formatted = formatRut(originalValue);

            // Solo actualizar si cambió para evitar saltos de cursor extraños (aunque simple replace funciona bien al final)
            if (originalValue !== formatted) {
                input.value = formatted;
                // Disparar evento input para que Alpine lo note si estuviera bindeado con x-model (en este caso es name directo, pero por si acaso)
                input.dispatchEvent(new Event('input'));
            }
        }
    });

    // Listener para verificación al perder foco
    document.addEventListener('focusout', function (e) {
        if (e.target && e.target.id === 'trabajador_rut') {
            let rut = e.target.value;
            if (rut.length < 3) return; // Muy corto para validar

            fetch(`{{ route('workers.check-rut') }}?rut=${encodeURIComponent(rut)}`)
                .then(response => response.json())
                .then(data => {
                    const errorContainer = document.getElementById('rut-error-message'); // Necesitamos agregar este contenedor
                    const btnSubmit = document.querySelector('button[type="submit"]'); // O el botón de confirmar

                    // Limpiar errores previos
                    if (errorContainer) {
                        errorContainer.textContent = '';
                        errorContainer.classList.add('hidden');
                    }

                    if (data.exists_in_users) {
                        // Mostrar modal de error
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'rut-error-modal' }));
                        e.target.value = ''; // Limpiar RUT
                    } else if (data.exists_in_conductores) {
                        // Autocompletar
                        document.getElementById('trabajador_nombre').value = data.data.nombre || '';
                        document.getElementById('trabajador_departamento').value = data.data.departamento || '';
                        document.getElementById('trabajador_cargo').value = data.data.cargo || '';

                        // Notificar visualme                   nte
                        // alert('Conductor encontrado. Datos cargados automáticamente.');
                    }
                }).catch(error => console.error('Error validando RUT:', error));

        }
    });
</script>