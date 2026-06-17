<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Vehículos') }}
            </h2>
            <div class="flex flex-wrap gap-3 items-center">
                <!-- Papelera -->
                <a href="{{ route('vehicles.trash') }}"
                    class="inline-flex items-center px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all duration-300 hover:-translate-y-0.5 group shadow-lg shadow-rose-500/30 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('Papelera') }}
                </a>

                <!-- Import Flatpickr -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

                <style>
                    /* Standard Seges Search Label Style */
                    .search-label {
                      display: flex !important;
                      align-items: center;
                      box-sizing: border-box;
                      position: relative;
                      border: 1px solid #334155;
                      border-radius: 1rem;
                      overflow: hidden;
                      background: #0f172a;
                      padding: 10px 14px;
                      cursor: text;
                      transition: all 0.3s ease;
                      width: 100% !important;
                      min-height: 48px;
                    }
                    .search-label:hover { border-color: #475569; }
                    .search-label:focus-within { 
                        background: #020617; 
                        border-color: #3b82f6; 
                        box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
                    }
                    .search-label input, .search-label select { 
                        outline: none; 
                        width: 100%; 
                        border: none !important; 
                        background: none !important; 
                        color: #f1f5f9; 
                        font-size: 0.875rem;
                        font-weight: 600;
                        padding: 0;
                        box-shadow: none !important;
                        appearance: none;
                    }
                    .search-label input::placeholder { color: #475569; }
                    .slash-icon { 
                        margin-left: 8px;
                        border: 1px solid #334155; 
                        background: linear-gradient(-225deg, #1e293b, #334155); 
                        border-radius: 6px; 
                        text-align: center; 
                        box-shadow: inset 0 -2px 0 0 #0f172a, inset 0 0 1px 1px #475569, 0 1px 2px 1px rgba(0, 0, 0, 0.4); 
                        color: #64748b;
                        font-size: 10px; 
                        font-weight: 900;
                        width: 20px; 
                        height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                    }
                    
                    /* Custom Flatpickr Overrides */
                    .flatpickr-calendar.dark {
                        background: #0f172a !important;
                        border: 1px solid #1e293b !important;
                        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
                        border-radius: 1.5rem !important;
                    }
                    .flatpickr-day.selected {
                        background: #3b82f6 !important;
                        border-color: #3b82f6 !important;
                    }
                </style>

                <!-- Historial -->
                <a href="{{ route('vehicles.users-history-index') }}"
                    class="inline-flex items-center px-4 py-2 bg-slate-800 text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-700 hover:text-white transition-all duration-300 hover:-translate-y-0.5 group shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Historial Usuarios') }}
                </a>

                <!-- Solicitudes -->
                <div class="relative">
                    <button x-data="" @click="$dispatch('open-modal', 'maintenance-requests-modal')"
                        class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-xs font-semibold rounded-lg hover:bg-amber-500 transition-all duration-300 hover:-translate-y-0.5 group shadow-md shadow-amber-500/20 relative cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Solicitudes
                        @if($pendingRequests->count() > 0 || (isset($pendingReservations) && $pendingReservations->count() > 0))
                            <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-white text-[10px] items-center justify-center font-bold shadow-sm">
                                    {{ $pendingRequests->count() + ($pendingReservations ?? collect())->count() }}
                                </span>
                            </span>
                        @endif
                    </button>
                </div>

                <!-- Nuevo -->
                <button x-data="" @click="$dispatch('open-modal', 'create-vehicle-modal')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-500 transition-all duration-300 hover:-translate-y-0.5 group shadow-lg shadow-blue-500/30 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Nuevo') }}
                </button>
            </div>
        </div>
    </x-slot>

    
    <div class="py-12"
        x-data="{ 
            openModal: {{ $errors->any() ? 'true' : 'false' }}, 
            deleteAction: '', 
            editingVehicle: {}, 
            editAction: '', 
            viewingVehicle: {}, 
            maintenanceVehicle: {}, 
            viewingUser: null,
            viewingCompanions: [],
            rejectionRequestId: null,
            rejectionUrl: '',
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
            },
            init() {
                @if(request('open_requests'))
                    this.$dispatch('open-modal', 'maintenance-requests-modal');
                @endif
            }
        }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="mb-8 grid grid-cols-2 md:grid-cols-5 gap-4">
                <a href="{{ route('vehicles.index', request()->except(['status', 'page'])) }}" wire:navigate
                   class="relative group bg-slate-800/40 border border-slate-700/50 p-4 rounded-2xl transition-all duration-300 hover:bg-slate-800 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/40 cursor-pointer {{ !request('status') ? 'ring-2 ring-blue-500 ring-offset-4 ring-offset-slate-900 border-blue-500/50' : '' }}">
                    <div class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Total</div>
                    <div class="text-2xl font-black text-white">{{ $totalVehicles }}</div>
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" /></svg>
                    </div>
                </a>

                <a href="{{ route('vehicles.index', array_merge(request()->except('page'), ['status' => 'available'])) }}" wire:navigate
                   class="relative group bg-slate-800/40 border border-emerald-500/20 p-4 rounded-2xl transition-all duration-300 hover:bg-emerald-500/10 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-900/20 cursor-pointer {{ request('status') === 'available' ? 'ring-2 ring-emerald-500 ring-offset-4 ring-offset-slate-900 border-emerald-500/50' : '' }}">
                    <div class="text-emerald-400 text-[10px] font-bold uppercase tracking-widest mb-1">Disponibles</div>
                    <div class="text-2xl font-black text-white">{{ $countDisponible }}</div>
                    <div class="absolute top-0 right-0 p-3 text-emerald-500/20 group-hover:text-emerald-500/40 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </a>

                <a href="{{ route('vehicles.index', array_merge(request()->except('page'), ['status' => 'occupied'])) }}" wire:navigate
                   class="relative group bg-slate-800/40 border border-blue-500/20 p-4 rounded-2xl transition-all duration-300 hover:bg-blue-500/10 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-900/20 cursor-pointer {{ request('status') === 'occupied' ? 'ring-2 ring-blue-500 ring-offset-4 ring-offset-slate-900 border-blue-500/50' : '' }}">
                    <div class="text-blue-400 text-[10px] font-bold uppercase tracking-widest mb-1">En Uso</div>
                    <div class="text-2xl font-black text-white">{{ $countAsignado }}</div>
                    <div class="absolute top-0 right-0 p-3 text-blue-500/20 group-hover:text-blue-500/40 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </a>

                <a href="{{ route('vehicles.index', array_merge(request()->except('page'), ['status' => 'maintenance'])) }}" wire:navigate
                   class="relative group bg-slate-800/40 border border-amber-500/20 p-4 rounded-2xl transition-all duration-300 hover:bg-amber-500/10 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-900/20 cursor-pointer {{ request('status') === 'maintenance' ? 'ring-2 ring-amber-500 ring-offset-4 ring-offset-slate-900 border-amber-500/50' : '' }}">
                    <div class="text-amber-400 text-[10px] font-bold uppercase tracking-widest mb-1">Mantenimiento</div>
                    <div class="text-2xl font-black text-white">{{ $countMantenimiento }}</div>
                    <div class="absolute top-0 right-0 p-3 text-amber-500/20 group-hover:text-amber-500/40 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                </a>

                <a href="{{ route('vehicles.index', array_merge(request()->except('page'), ['status' => 'out_of_service'])) }}" wire:navigate
                   class="relative group bg-slate-800/40 border border-rose-500/20 p-4 rounded-2xl transition-all duration-300 hover:bg-rose-500/10 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-900/20 cursor-pointer {{ request('status') === 'out_of_service' ? 'ring-2 ring-rose-500 ring-offset-4 ring-offset-slate-900 border-rose-500/50' : '' }}">
                    <div class="text-rose-400 text-[10px] font-bold uppercase tracking-widest mb-1">Fuera Servicio</div>
                    <div class="text-2xl font-black text-white">{{ $countFueraDeServicio }}</div>
                    <div class="absolute top-0 right-0 p-3 text-rose-500/20 group-hover:text-rose-500/40 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </a>
            </div>

            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center">
                <div class="relative w-full sm:max-w-md group">
                    <input type="text" x-model="searchQuery"
                        placeholder="Buscar por patente, marca o modelo..."
                        class="w-full bg-[#0f172a] border border-slate-700 text-slate-100 rounded-xl pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all shadow-inner placeholder-slate-500 font-medium">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-500 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex gap-3 w-full sm:w-auto relative" x-data="{ filtersOpen: false }" @click.away="filtersOpen = false">
                    <button type="button" @click="filtersOpen = !filtersOpen" 
                        class="px-5 py-2.5 bg-slate-800 border border-slate-700 text-slate-300 rounded-xl hover:bg-slate-700 hover:text-white font-bold text-sm transition-all flex items-center gap-2 shadow-sm group cursor-pointer relative">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Filtros
                        @php
                            $activeFiltersCount = collect([request('status'), request('document_status'), request('maintenance_status')])->filter()->count();
                        @endphp
                        @if($activeFiltersCount > 0)
                            <span class="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full font-black shadow-lg shadow-blue-500/40">{{ $activeFiltersCount }}</span>
                        @endif
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': filtersOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="filtersOpen" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="absolute right-0 top-full mt-3 w-72 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl z-[60] p-4 backdrop-blur-xl"
                        style="display: none;">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-800">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Opciones de Filtro</span>
                            @if($activeFiltersCount > 0)
                                <a href="{{ route('vehicles.index') }}" wire:navigate class="text-[9px] font-black text-rose-500 hover:text-rose-400 uppercase tracking-tighter">Limpiar Todo</a>
                            @endif
                        </div>

                        <!-- Filter Sections -->
                        <div class="space-y-6">
                            <!-- Estado -->
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Estado del Vehículo</label>
                                <div class="grid grid-cols-1 gap-1.5">
                                    @php
                                        $statuses = [
                                            'available' => ['label' => 'Disponible', 'color' => 'bg-emerald-500'],
                                            'occupied' => ['label' => 'Reservado', 'color' => 'bg-blue-500'],
                                            'maintenance' => ['label' => 'Mantenimiento', 'color' => 'bg-amber-500'],
                                            'out_of_service' => ['label' => 'Fuera de Servicio', 'color' => 'bg-rose-500'],
                                        ];
                                    @endphp
                                    @foreach($statuses as $value => $info)
                                        <a href="{{ request('status') === $value ? route('vehicles.index', request()->except(['status', 'page'])) : route('vehicles.index', array_merge(request()->query(), ['status' => $value, 'page' => 1])) }}" 
                                           wire:navigate
                                           class="flex items-center justify-between p-2 rounded-xl transition-all cursor-pointer {{ request('status') === $value ? 'bg-blue-600/20 border border-blue-500/30' : 'hover:bg-slate-800 border border-transparent' }}">
                                            <div class="flex items-center gap-3">
                                                <span class="w-2 h-2 rounded-full {{ $info['color'] }} shadow-[0_0_8px_rgba(0,0,0,0.5)]"></span>
                                                <span class="text-xs font-bold {{ request('status') === $value ? 'text-white' : 'text-slate-400' }}">{{ $info['label'] }}</span>
                                            </div>
                                            @if(request('status') === $value)
                                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Documentación -->
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Documentación</label>
                                <div class="grid grid-cols-1 gap-1.5">
                                    <a href="{{ request('document_status') === 'up_to_date' ? route('vehicles.index', request()->except(['document_status', 'page'])) : route('vehicles.index', array_merge(request()->query(), ['document_status' => 'up_to_date', 'page' => 1])) }}" 
                                       wire:navigate
                                       class="flex items-center justify-between p-2 rounded-xl transition-all cursor-pointer {{ request('document_status') === 'up_to_date' ? 'bg-emerald-600/20 border border-emerald-500/30' : 'hover:bg-slate-800 border border-transparent' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-xs font-bold {{ request('document_status') === 'up_to_date' ? 'text-white' : 'text-slate-400' }}">Documentos al Día</span>
                                        </div>
                                        @if(request('document_status') === 'up_to_date')
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </a>
                                    <a href="{{ request('document_status') === 'expired' ? route('vehicles.index', request()->except(['document_status', 'page'])) : route('vehicles.index', array_merge(request()->query(), ['document_status' => 'expired', 'page' => 1])) }}" 
                                       wire:navigate
                                       class="flex items-center justify-between p-2 rounded-xl transition-all cursor-pointer {{ request('document_status') === 'expired' ? 'bg-rose-600/20 border border-rose-500/30' : 'hover:bg-slate-800 border border-transparent' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                            <span class="text-xs font-bold {{ request('document_status') === 'expired' ? 'text-white' : 'text-slate-400' }}">Documentos Vencidos</span>
                                        </div>
                                        @if(request('document_status') === 'expired')
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </a>
                                </div>
                            </div>

                            <!-- Mantención -->
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Mantención</label>
                                <div class="grid grid-cols-1 gap-1.5">
                                    <a href="{{ request('maintenance_status') === 'ok' ? route('vehicles.index', request()->except(['maintenance_status', 'page'])) : route('vehicles.index', array_merge(request()->query(), ['maintenance_status' => 'ok', 'page' => 1])) }}" 
                                       wire:navigate
                                       class="flex items-center justify-between p-2 rounded-xl transition-all cursor-pointer {{ request('maintenance_status') === 'ok' ? 'bg-emerald-600/20 border border-emerald-500/30' : 'hover:bg-slate-800 border border-transparent' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-xs font-bold {{ request('maintenance_status') === 'ok' ? 'text-white' : 'text-slate-400' }}">Mecánica al Día</span>
                                        </div>
                                        @if(request('maintenance_status') === 'ok')
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </a>
                                    <a href="{{ request('maintenance_status') === 'needed' ? route('vehicles.index', request()->except(['maintenance_status', 'page'])) : route('vehicles.index', array_merge(request()->query(), ['maintenance_status' => 'needed', 'page' => 1])) }}" 
                                       wire:navigate
                                       class="flex items-center justify-between p-2 rounded-xl transition-all cursor-pointer {{ request('maintenance_status') === 'needed' ? 'bg-amber-600/20 border border-amber-500/30' : 'hover:bg-slate-800 border border-transparent' }}">
                                        <div class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            <span class="text-xs font-bold {{ request('maintenance_status') === 'needed' ? 'text-white' : 'text-slate-400' }}">Requiere Atención</span>
                                        </div>
                                        @if(request('maintenance_status') === 'needed')
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <template x-if="searchQuery || {{ $activeFiltersCount > 0 ? 'true' : 'false' }}">
                        <a href="{{ route('vehicles.index') }}" wire:navigate class="px-3 py-2 text-slate-500 hover:text-rose-500 transition-colors flex items-center cursor-pointer" title="Limpiar Filtros">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    </template>
                </div>
            </div>



            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-900/80 text-slate-500 border-b border-slate-800">
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Foto</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Patente</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Marca / Modelo</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Año</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Estado Operativo</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Documentación</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Kilometraje</th>
                                    <th class="px-6 py-5 text-right text-[9px] font-black uppercase tracking-[0.2em]">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 bg-slate-950/20">
                                @forelse($vehicles as $vehicle)
                                    @php $jsonVehicle = $vehicle->load(['currentMaintenanceState', 'documents', 'reservations.user', 'reservations.companions', 'reservations.conductor'])->append(['display_status', 'active_reservation', 'effective_reservation'])->toJson(); @endphp
                                    <tr class="hover:bg-slate-900/40 transition-all duration-300 group {{ request('highlight_id') == $vehicle->id ? 'bg-blue-600/5 border-l-4 border-l-blue-600' : '' }}"
                                        data-search="{{ strtolower($vehicle->plate . ' ' . $vehicle->brand . ' ' . $vehicle->model) }}"
                                        x-show="!searchQuery || $el.dataset.search.split(' ').some(word => word.startsWith(searchQuery.toLowerCase()))">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($vehicle->image_path)
                                                <div class="h-12 w-12 flex-shrink-0 group-hover:scale-110 transition-all duration-500 relative">
                                                    <div class="absolute -inset-1 bg-blue-600/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                    <img class="relative h-12 w-12 rounded-2xl object-cover border border-slate-800 shadow-2xl"
                                                        src="{{ Storage::url($vehicle->image_path) }}"
                                                        alt="{{ $vehicle->plate }}">
                                                </div>
                                            @else
                                                <div class="h-12 w-12 rounded-2xl bg-slate-900 flex items-center justify-center text-[9px] font-black text-slate-600 border border-slate-800 group-hover:border-slate-700 transition-colors uppercase">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-xs font-black text-white bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-800 shadow-inner group-hover:border-blue-500/30 transition-colors">{{ $vehicle->plate }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-white group-hover:text-blue-400 transition-colors">{{ $vehicle->brand }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $vehicle->model }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-black">
                                            {{ $vehicle->year }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $displayStatus = $vehicle->display_status;
                                                $statusClasses = [
                                                    'available' => 'text-emerald-400 bg-emerald-500/5 border-emerald-500/20 shadow-emerald-500/5',
                                                    'out_of_service' => 'text-rose-400 bg-rose-500/5 border-rose-500/20 shadow-rose-500/5',
                                                    'maintenance' => 'text-amber-400 bg-amber-500/5 border-amber-500/20 shadow-amber-500/5',
                                                    'occupied' => 'text-blue-400 bg-blue-500/5 border-blue-500/20 shadow-blue-500/5',
                                                ];
                                                $statusLabel = [
                                                    'available' => 'Disponible',
                                                    'out_of_service' => 'F. Servicio',
                                                    'maintenance' => 'Mantención',
                                                    'occupied' => 'Reservado',
                                                ];
                                            @endphp
                                            <span class="px-3 py-1.5 inline-flex text-[9px] font-black rounded-xl border {{ $statusClasses[$displayStatus] ?? 'text-slate-400 bg-slate-900' }} uppercase tracking-widest shadow-inner">
                                                {{ $statusLabel[$displayStatus] ?? strtoupper($displayStatus) }}
                                            </span>
                                            @if($displayStatus === 'occupied' && $vehicle->active_reservation)
                                                <div class="text-[9px] text-blue-500 font-black uppercase tracking-tighter mt-1.5 opacity-60">
                                                    {{ $vehicle->active_reservation->user->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($vehicle->hasExpiredDocuments())
                                                <span class="px-3 py-1.5 inline-flex text-[9px] font-black rounded-xl text-rose-400 bg-rose-500/5 border border-rose-500/20 uppercase tracking-widest shadow-inner">
                                                    Vencida
                                                </span>
                                            @else
                                                 <span class="px-3 py-1.5 inline-flex text-[9px] font-black rounded-xl text-emerald-400 bg-emerald-500/5 border border-emerald-500/20 uppercase tracking-widest shadow-inner">
                                                    Vigente
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <span class="text-white text-sm font-black tracking-tight">
                                                    {{ number_format($vehicle->mileage, 0, '', '.') }} <span class="text-[10px] text-slate-600 font-black uppercase">km</span>
                                                </span>
                                                @if($vehicle->currentMaintenanceState && $vehicle->currentMaintenanceState->next_oil_change_km)
                                                    @if($vehicle->mileage >= $vehicle->currentMaintenanceState->next_oil_change_km)
                                                        <div class="w-2 h-2 rounded-full bg-rose-500 animate-pulse shadow-[0_0_8px_rgba(244,63,94,0.6)]"></div>
                                                    @elseif(($vehicle->currentMaintenanceState->next_oil_change_km - $vehicle->mileage) <= 500)
                                                        <div class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]"></div>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- Mantenimiento -->
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
                                                                     oil_change_due: {{ ($vehicle->currentMaintenanceState && $vehicle->currentMaintenanceState->next_oil_change_km && $vehicle->mileage >= $vehicle->currentMaintenanceState->next_oil_change_km) ? 'true' : 'false' }}
                                                                 };
                                                                 $dispatch('open-modal', 'maintenance-vehicle-modal');
                                                             "
                                                    class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 {{ ($vehicle->currentMaintenanceState && $vehicle->currentMaintenanceState->next_oil_change_km && $vehicle->mileage >= $vehicle->currentMaintenanceState->next_oil_change_km) ? 'text-rose-500 animate-pulse border-rose-500/30' : 'text-amber-500 hover:text-white hover:bg-amber-600 hover:border-amber-500 shadow-lg hover:shadow-amber-600/20' }} transition-all duration-300 flex items-center justify-center cursor-pointer"
                                                    title="Mantenimiento">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                </button>
                                                
                                                <div class="h-4 w-[1px] bg-slate-800"></div>

                                                <!-- Ficha -->
                                                <button @click="viewingVehicle = {{ $jsonVehicle }}; viewingVehicle.reservation = viewingVehicle.active_reservation || viewingVehicle.effective_reservation || null; $dispatch('open-modal', 'view-vehicle-modal');"
                                                    class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-emerald-500 hover:text-white hover:bg-emerald-600 hover:border-emerald-500 shadow-lg hover:shadow-emerald-600/20 transition-all duration-300 flex items-center justify-center cursor-pointer"
                                                    title="Ver Ficha">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                </button>

                                                <!-- Editar -->
                                                <button @click="editingVehicle = {{ $jsonVehicle }}; editAction = '{{ route('vehicles.update', $vehicle->id) }}'; $dispatch('open-modal', 'edit-vehicle-modal');"
                                                    class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-blue-500 hover:text-white hover:bg-blue-600 hover:border-blue-500 shadow-lg hover:shadow-blue-600/20 transition-all duration-300 flex items-center justify-center cursor-pointer"
                                                    title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </button>

                                                <!-- Eliminar -->
                                                <button @click="$dispatch('open-modal', 'confirm-delete-modal'); deleteAction = '{{ route('vehicles.destroy', $vehicle) }}'"
                                                    class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-rose-500 hover:text-white hover:bg-rose-600 hover:border-rose-500 shadow-lg hover:shadow-rose-600/20 transition-all duration-300 flex items-center justify-center cursor-pointer"
                                                    title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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


        <x-modal name="create-vehicle-modal" :show="$errors->any()" focusable maxWidth="3xl">
            <form method="POST" action="{{ route('vehicles.store') }}" class="p-6"
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
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Nuevo Vehículo') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Registro oficial en flota
                </p>

                <div class="mt-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar" x-data="{ photoPreview: null, isCompressing: false }">
                    
                    <!-- Foto del Vehículo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Fotografía Principal') }}</label>
                        <div class="flex flex-col sm:flex-row items-center gap-5">
                            <div class="relative">
                                <template x-if="!photoPreview">
                                    <div class="relative w-40 h-28 bg-[#1e293b] rounded-lg border border-slate-700 flex items-center justify-center text-slate-500">
                                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                </template>
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="relative w-40 h-28 object-cover rounded-lg border border-slate-700 shadow-md">
                                </template>
                            </div>

                            <div class="flex-1 space-y-3">
                                <p class="text-xs text-slate-400">Sube una imagen nítida del vehículo para facilitar su identificación visual en el sistema.</p>
                                <button type="button" @click="$refs.photo.click()" :disabled="isCompressing" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg shadow-md shadow-blue-500/20 hover:bg-blue-500 hover:-translate-y-0.5 transition-all cursor-pointer flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                    <span x-show="!isCompressing">{{ __('Seleccionar Foto') }}</span>
                                    <span x-show="isCompressing">{{ __('Procesando...') }}</span>
                                </button>
                            </div>
                        </div>
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

                    <!-- Datos Técnicos -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-medium text-gray-300 border-b border-slate-800 pb-2">
                            {{ __('Especificaciones Técnicas') }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Patente</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="text" name="plate" required placeholder="Ej: AB123CD" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Nº Serie/Chasis</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="text" name="serial_number" placeholder="Opcional" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Kilometraje</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="text" name="mileage" required placeholder="0" x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Marca</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="text" name="brand" required placeholder="Toyota" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Modelo</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="text" name="model" required placeholder="Hilux" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Año</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="number" name="year" required placeholder="2023" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                                </div>
                            </div>
                            <div class="relative" x-data="{ openFuel: false, selectedFuel: '{{ old('fuel_type', 'diesel') }}', fuelLabel: '{{ old('fuel_type') == 'gasoline' ? 'Bencina (Gasolina)' : 'Petróleo (Diesel)' }}', fuels: [{v:'diesel',l:'Petróleo (Diesel)'},{v:'gasoline',l:'Bencina (Gasolina)'}] }">
                                <label class="block text-sm font-medium text-gray-300 mb-1">Combustible</label>
                                <input type="hidden" name="fuel_type" x-model="selectedFuel">
                                <button type="button" @click="openFuel = !openFuel" @click.away="openFuel = false" class="w-full flex items-center justify-between border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 hover:border-slate-600 transition-colors text-left focus:outline-none focus:border-blue-500">
                                    <span x-text="fuelLabel" class="text-slate-100 text-sm"></span>
                                    <svg class="w-4 h-4 text-slate-500 transition-transform" :class="openFuel ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <ul x-show="openFuel" x-transition class="absolute z-50 w-full mt-1 bg-[#1e293b] shadow-lg rounded-lg py-1 text-sm ring-1 ring-slate-700 overflow-auto" style="display:none;">
                                    <template x-for="f in fuels" :key="f.v">
                                        <li @click="selectedFuel = f.v; fuelLabel = f.l; openFuel = false" class="text-gray-200 cursor-pointer select-none py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors" :class="selectedFuel === f.v ? 'bg-blue-600/20 text-blue-400' : ''">
                                            <span x-text="f.l"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Documentación -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-medium text-gray-300 border-b border-slate-800 pb-2">
                            {{ __('Documentación Legal Inicial') }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SOAP -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Seguro Obligatorio (SOAP)') }}</label>
                                    <input type="file" name="soap_file" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all border border-slate-700 bg-[#1e293b] p-1.5 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Vencimiento SOAP') }}</label>
                                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                        <input type="text" name="soap_expires_at" class="flatpickr-date w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" placeholder="Seleccionar fecha">
                                    </div>
                                </div>
                            </div>

                            <!-- Permiso Circulación -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Permiso de Circulación') }}</label>
                                    <input type="file" name="permit_file" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all border border-slate-700 bg-[#1e293b] p-1.5 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Vencimiento Permiso') }}</label>
                                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                        <input type="text" name="permit_expires_at" class="flatpickr-date w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" placeholder="Seleccionar fecha">
                                    </div>
                                </div>
                            </div>

                            <!-- Revisión Técnica -->
                            <div class="space-y-4 md:col-span-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Certificado de Revisión Técnica') }}</label>
                                        <input type="file" name="technical_file" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all border border-slate-700 bg-[#1e293b] p-1.5 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Vencimiento Revisión Técnica') }}</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                            <input type="text" name="technical_expires_at" class="flatpickr-date w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" placeholder="Seleccionar fecha">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close')" class="px-5 py-2.5 bg-rose-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-rose-500/30 hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                        {{ __('Guardar Vehículo') }}
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Confirmación Eliminar -->
        <x-modal name="confirm-delete-modal" :show="false" focusable>
            <div class="p-0 bg-slate-900 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-800 shadow-2xl relative">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-rose-600/5 rounded-full blur-[80px] pointer-events-none"></div>

                <div class="p-10 text-center">
                    <div class="w-20 h-20 rounded-[2rem] bg-rose-500/10 text-rose-500 flex items-center justify-center border border-rose-500/20 shadow-inner mx-auto mb-8 animate-bounce-subtle">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    
                    <h3 class="text-2xl font-black text-white tracking-tight uppercase mb-4">{{ __('¿Confirmar Eliminación?') }}</h3>
                    <p class="text-slate-400 text-sm font-medium max-w-xs mx-auto leading-relaxed">
                        {{ __('El vehículo se moverá a la papelera. Podrás restaurarlo después si lo necesitas.') }}
                    </p>
                </div>

                <div class="px-8 py-6 bg-slate-950/50 backdrop-blur-md border-t border-slate-800 flex items-center justify-center gap-4">
                    <button type="button" @click="$dispatch('close')" class="px-8 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                        {{ __('Cancelar') }}
                    </button>
                    
                    <form method="POST" :action="deleteAction">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-10 py-3 bg-rose-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-rose-600/20 hover:bg-rose-500 hover:-translate-y-1 transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" /></svg>
                            {{ __('Eliminar Vehículo') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        <!-- Modal Editar Vehículo -->
        <x-modal name="edit-vehicle-modal" :show="false" focusable maxWidth="3xl">
            <form method="POST" :action="editAction" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Editar Vehículo') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Actualización de ficha técnica
                </p>

                <div class="mt-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar" x-data="{ photoPreview: null }">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Patente</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="text" name="plate" id="edit_plate" x-model="editingVehicle.plate" required placeholder="AA123BB" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nº Serie/Chasis</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="text" name="serial_number" id="edit_serial_number" x-model="editingVehicle.serial_number" placeholder="Opcional" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Kilometraje</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="text" name="mileage" id="edit_mileage" x-model="editingVehicle.mileage" required placeholder="0" x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Marca</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="text" name="brand" id="edit_brand" x-model="editingVehicle.brand" required placeholder="Toyota" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Modelo</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="text" name="model" id="edit_model" x-model="editingVehicle.model" required placeholder="Hilux" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Año</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="number" name="year" id="edit_year" x-model="editingVehicle.year" required placeholder="2023" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative" x-data="{ openFuel: false, fuels: [{v:'diesel',l:'Petróleo (Diesel)'},{v:'gasoline',l:'Bencina (Gasolina)'}] }">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Combustible</label>
                        <input type="hidden" name="fuel_type" x-model="editingVehicle.fuel_type">
                        <button type="button" @click="openFuel = !openFuel" @click.away="openFuel = false" class="w-full flex items-center justify-between border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 hover:border-slate-600 transition-colors text-left focus:outline-none focus:border-blue-500">
                            <span x-text="editingVehicle.fuel_type === 'diesel' ? 'Petróleo (Diesel)' : (editingVehicle.fuel_type === 'gasoline' ? 'Bencina (Gasolina)' : 'Seleccionar')" class="text-slate-100 text-sm"></span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" :class="openFuel ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="openFuel" x-transition class="absolute z-50 w-full mt-1 bg-[#1e293b] shadow-lg rounded-lg py-1 text-sm ring-1 ring-slate-700 overflow-auto" style="display:none;">
                            <template x-for="f in fuels" :key="f.v">
                                <li @click="editingVehicle.fuel_type = f.v; openFuel = false" class="text-gray-200 cursor-pointer select-none py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors" :class="editingVehicle.fuel_type === f.v ? 'bg-blue-600/20 text-blue-400' : ''">
                                    <span x-text="f.l"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Estado Operativo</label>
                        <template x-if="editingVehicle.status !== 'occupied'">
                            <div class="relative" x-data="{ openStatus: false, statuses: [{v:'available',l:'Disponible'},{v:'out_of_service',l:'Fuera de Servicio'},{v:'maintenance',l:'En Mantención'},{v:'workshop',l:'En Taller'}] }">
                                <input type="hidden" name="status" x-model="editingVehicle.status">
                                <button type="button" @click="openStatus = !openStatus" @click.away="openStatus = false" class="w-full flex items-center justify-between border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 hover:border-slate-600 transition-colors text-left focus:outline-none focus:border-blue-500">
                                    <span x-text="editingVehicle.status === 'available' ? 'Disponible' : (editingVehicle.status === 'out_of_service' ? 'Fuera de Servicio' : (editingVehicle.status === 'maintenance' ? 'En Mantención' : (editingVehicle.status === 'workshop' ? 'En Taller' : 'Seleccionar')))" class="text-slate-100 text-sm"></span>
                                    <svg class="w-4 h-4 text-slate-500 transition-transform" :class="openStatus ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <ul x-show="openStatus" x-transition class="absolute z-50 w-full mt-1 bg-[#1e293b] shadow-lg rounded-lg py-1 text-sm ring-1 ring-slate-700 overflow-auto" style="display:none;">
                                    <template x-for="s in statuses" :key="s.v">
                                        <li @click="editingVehicle.status = s.v; openStatus = false" class="text-gray-200 cursor-pointer select-none py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors" :class="editingVehicle.status === s.v ? 'bg-blue-600/20 text-blue-400' : ''">
                                            <span x-text="s.l"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                        <template x-if="editingVehicle.status === 'occupied'">
                            <div class="relative group/assigned">
                                <input type="hidden" name="status" value="occupied">
                                <div class="px-4 py-3 bg-blue-600/10 border border-blue-500/30 rounded-2xl flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                    <span class="text-xs font-black text-blue-400 uppercase tracking-widest">En Uso (Reservado)</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Documentación -->
                <div class="space-y-4">
                    <h3 class="text-sm font-medium text-gray-300 border-b border-slate-800 pb-2">
                        {{ __('Actualizar Documentación') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Seguro (SOAP) -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Seguro Obligatorio (SOAP)') }}</label>
                                <input type="file" name="soap_file" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all border border-slate-700 bg-[#1e293b] p-1.5 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Vencimiento SOAP') }}</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="text" name="soap_expires_at" x-model="editingVehicle.soap_expires_at" class="flatpickr-date w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" placeholder="Seleccionar fecha">
                                </div>
                            </div>
                        </div>

                        <!-- Permiso Circulación -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Permiso de Circulación') }}</label>
                                <input type="file" name="permit_file" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all border border-slate-700 bg-[#1e293b] p-1.5 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Vencimiento Permiso') }}</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <input type="text" name="permit_expires_at" x-model="editingVehicle.permit_expires_at" class="flatpickr-date w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" placeholder="Seleccionar fecha">
                                </div>
                            </div>
                        </div>

                        <!-- Revisión Técnica -->
                        <div class="space-y-4 md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Certificado de Revisión Técnica') }}</label>
                                    <input type="file" name="technical_file" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all border border-slate-700 bg-[#1e293b] p-1.5 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">{{ __('Vencimiento Revisión Técnica') }}</label>
                                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                        <input type="text" name="technical_expires_at" x-model="editingVehicle.technical_expires_at" class="flatpickr-date w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" placeholder="Seleccionar fecha">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="$dispatch('close')" class="px-5 py-2.5 bg-rose-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-rose-500/30 hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                        {{ __('Actualizar Vehículo') }}
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Ver Detalle Vehículo -->
        <x-modal name="view-vehicle-modal" :show="false" focusable zIndex="z-[60]" maxWidth="4xl">
            <div class="p-0 bg-slate-900 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-800 shadow-2xl relative">
                <!-- Header con Imagen de Fondo -->
                <div class="relative h-48 bg-slate-900/50">
                    <template x-if="viewingVehicle.imageUrl">
                        <img :src="viewingVehicle.imageUrl" alt="Foto Vehículo" class="w-full h-full object-cover opacity-40 transition-opacity duration-700">
                    </template>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
                    
                    <div class="absolute bottom-8 left-10">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center border border-blue-500/30 shadow-inner backdrop-blur-sm">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <h2 class="text-3xl font-black text-white tracking-tighter uppercase" x-text="viewingVehicle.plate"></h2>
                                <p class="text-[11px] text-slate-400 font-black uppercase tracking-[0.2em] mt-1" x-text="viewingVehicle.brand + ' ' + viewingVehicle.model"></p>
                            </div>
                        </div>
                    </div>

                    <button @click="$dispatch('close')" class="absolute top-6 right-6 w-10 h-10 bg-slate-900/50 hover:bg-slate-800 rounded-xl text-slate-400 hover:text-white transition-all flex items-center justify-center cursor-pointer backdrop-blur-md border border-slate-700/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-10 space-y-12 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <!-- Especificaciones Técnicas -->
                        <div class="space-y-8">
                            <h3 class="text-[11px] font-black text-blue-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-8 h-[1px] bg-blue-500/30"></span>
                                Especificaciones Técnicas
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-800/50 shadow-inner group/item">
                                    <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 group-hover/item:text-blue-400 transition-colors">Año Fab.</span>
                                    <span class="text-base font-black text-white" x-text="viewingVehicle.year"></span>
                                </div>
                                <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-800/50 shadow-inner group/item">
                                    <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 group-hover/item:text-blue-400 transition-colors">Kilometraje</span>
                                    <span class="text-base font-black text-white" x-text="Number(viewingVehicle.mileage).toLocaleString('es-CL') + ' KM'"></span>
                                </div>
                                <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-800/50 shadow-inner group/item">
                                    <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 group-hover/item:text-blue-400 transition-colors">Combustible</span>
                                    <span class="text-sm font-black uppercase" :class="viewingVehicle.fuel_type === 'diesel' ? 'text-amber-400' : 'text-emerald-400'" x-text="viewingVehicle.fuel_type === 'diesel' ? 'Diesel' : 'Gasolina'"></span>
                                </div>
                                <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-800/50 shadow-inner group/item">
                                    <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 group-hover/item:text-blue-400 transition-colors">Estado Actual</span>
                                    <span class="text-sm font-black uppercase" 
                                          :class="{
                                              'text-emerald-400': viewingVehicle.status === 'available',
                                              'text-rose-400': viewingVehicle.status === 'out_of_service',
                                              'text-amber-400': viewingVehicle.status === 'maintenance',
                                              'text-blue-400': viewingVehicle.status === 'occupied'
                                          }"
                                          x-text="viewingVehicle.status === 'available' ? 'Disponible' : (viewingVehicle.status === 'out_of_service' ? 'F. Servicio' : (viewingVehicle.status === 'maintenance' ? 'Mantención' : 'Reservado'))"></span>
                                </div>
                                <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-800/50 shadow-inner col-span-2 group/item">
                                    <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5 group-hover/item:text-blue-400 transition-colors">Nº Serie / Chasis</span>
                                    <span class="text-sm font-mono font-bold text-slate-300 break-all" x-text="viewingVehicle.serial_number || 'No Registrado'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Estado Operativo / Documentación -->
                        <div class="space-y-8">
                            <h3 class="text-[11px] font-black text-emerald-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                <span class="w-8 h-[1px] bg-emerald-500/30"></span>
                                Control de Documentos
                            </h3>

                            <div class="space-y-4">
                                <template x-for="doc in (viewingVehicle.documents || [])" :key="doc.id">
                                    <div class="flex items-center justify-between bg-slate-950/40 p-5 rounded-[1.5rem] border border-slate-800/50 hover:border-blue-500/30 transition-all group">
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-900 group-hover:bg-blue-600/10 group-hover:text-blue-500 transition-colors flex items-center justify-center shadow-inner border border-slate-800">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <div class="text-[11px] font-black text-white uppercase tracking-widest" x-text="doc.type === 'insurance' ? 'SOAP' : (doc.type === 'technical_review' ? 'Rev. Técnica' : 'Permiso Circ.')"></div>
                                                <div class="text-[10px] font-bold mt-1" :class="getDaysRemaining(doc.expires_at) < 0 ? 'text-rose-500' : 'text-slate-500'" x-text="'Vencimiento: ' + (doc.expires_at ? doc.expires_at.split('T')[0].split('-').reverse().join('/') : '---')"></div>
                                            </div>
                                        </div>
                                        <a :href="'/storage/' + doc.file_path" target="_blank" class="w-10 h-10 bg-blue-600/10 text-blue-500 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center cursor-pointer shadow-lg hover:shadow-blue-600/20">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                    </div>
                                </template>
                                
                                <template x-if="!viewingVehicle.documents || viewingVehicle.documents.length === 0">
                                    <div class="text-center py-12 border-2 border-dashed border-slate-800 rounded-[2.5rem] bg-slate-950/20">
                                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest italic">Sin documentación adjunta</p>
                                    </div>
                                </template>
                            </div>

                            <!-- Usuario Asignado (si aplica) -->
                            <template x-if="viewingVehicle.status === 'occupied' && viewingVehicle.assigned_user">
                                <div class="pt-6 border-t border-slate-800">
                                    <h4 class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                        Usuario Asignado
                                    </h4>
                                    <div class="bg-rose-500/5 border border-rose-500/10 rounded-2xl p-5 flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-500 font-black text-xl border border-rose-500/20">
                                            <span x-text="viewingVehicle.assigned_user.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-white" x-text="viewingVehicle.assigned_user"></div>
                                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5" x-text="'RUT: ' + (viewingVehicle.assigned_user_rut || 'N/A')"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-950/50 backdrop-blur-md border-t border-slate-800 flex items-center justify-end">
                    <button @click="$dispatch('close')" class="px-8 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                        {{ __('Cerrar Ficha Técnica') }}
                    </button>
                </div>
            </div>
        </x-modal>


        <!-- Modal Mantenimiento -->
        <x-modal name="maintenance-vehicle-modal" :show="false" focusable maxWidth="4xl">
            <div class="p-0 bg-slate-900 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-800 shadow-2xl relative" x-data="{ tab: 'status' }">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/50 backdrop-blur-md sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-inner">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase">{{ __('Centro de Mantenimiento') }}</h3>
                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">Control preventivo y correctivo</p>
                        </div>
                    </div>
                    <button type="button" @click="$dispatch('close')" class="text-slate-500 hover:text-white transition-colors cursor-pointer p-2 hover:bg-slate-800 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Tabs Navigation -->
                <div class="px-8 py-2 bg-slate-900/30 border-b border-slate-800 flex gap-6 overflow-x-auto">
                    <button @click="tab = 'status'" :class="tab === 'status' ? 'text-blue-400 border-blue-500' : 'text-slate-500 border-transparent hover:text-slate-300'" class="px-4 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all cursor-pointer whitespace-nowrap">
                        Estado Actual
                    </button>
                    <button @click="tab = 'request'" :class="tab === 'request' ? 'text-blue-400 border-blue-500' : 'text-slate-500 border-transparent hover:text-slate-300'" class="px-4 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all cursor-pointer whitespace-nowrap">
                        Solicitar Mantención
                    </button>
                    <button @click="tab = 'history'" :class="tab === 'history' ? 'text-blue-400 border-blue-500' : 'text-slate-500 border-transparent hover:text-slate-300'" class="px-4 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all cursor-pointer whitespace-nowrap">
                        Historial de OT
                    </button>
                    <button @click="tab = 'pending'" :class="tab === 'pending' ? 'text-blue-400 border-blue-500' : 'text-slate-500 border-transparent hover:text-slate-300'" class="px-4 py-3 text-[10px] font-black uppercase tracking-widest border-b-2 transition-all cursor-pointer whitespace-nowrap relative">
                        Solicitudes
                        @if($pendingRequests->count() > 0)
                            <span class="absolute top-2 -right-1 w-2 h-2 bg-rose-500 rounded-full"></span>
                        @endif
                    </button>
                </div>

                 <!-- Tab: Estado Actual -->
                <div x-show="tab === 'status'" x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <form id="update-maintenance-state-form" method="POST" :action="maintenanceVehicle.updateStateAction">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-8">
                                <h3 class="text-[11px] font-black text-blue-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                    <span class="w-8 h-[1px] bg-blue-500/30"></span>
                                    Aceite y Servicios
                                </h3>
                                
                                <div class="space-y-6">
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Último Cambio Aceite (km)</label>
                                        <div class="search-label">
                                            <input type="text" name="last_oil_change_km" x-model="maintenanceVehicle.last_oil_change_km" 
                                                x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                                x-bind:disabled="maintenanceVehicle.oil_change_due && !['maintenance', 'workshop'].includes(maintenanceVehicle.status)"
                                                placeholder="0">
                                            <kbd class="slash-icon">/</kbd>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors" ::class="{ 'text-rose-500': maintenanceVehicle.oil_change_due }">Próximo Cambio (km)</label>
                                        <div class="search-label" :class="maintenanceVehicle.oil_change_due ? 'border-rose-500/50 bg-rose-500/5' : ''">
                                            <input type="text" name="next_oil_change_km" x-model="maintenanceVehicle.next_oil_change_km"
                                                x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                                x-bind:disabled="maintenanceVehicle.oil_change_due && !['maintenance', 'workshop'].includes(maintenanceVehicle.status)"
                                                placeholder="10.000">
                                            <kbd class="slash-icon">/</kbd>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-8">
                                <h3 class="text-[11px] font-black text-emerald-400 uppercase tracking-[0.2em] flex items-center gap-3">
                                    <span class="w-8 h-[1px] bg-emerald-500/30"></span>
                                    Estado Neumáticos
                                </h3>
                                
                                <div class="space-y-6">
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Eje Delantero</label>
                                        <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-2 focus-within:border-blue-500 transition-all shadow-inner">
                                            <select name="tire_status_front" x-model="maintenanceVehicle.tire_status_front" class="w-full bg-transparent border-none text-white text-sm font-bold py-3 px-2 focus:ring-0 cursor-pointer">
                                                <option value="good" class="bg-slate-900">🟢 Bueno</option>
                                                <option value="fair" class="bg-slate-900">🟡 Regular</option>
                                                <option value="poor" class="bg-slate-900">🔴 Malo (Cambiar)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Eje Trasero</label>
                                        <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-2 focus-within:border-blue-500 transition-all shadow-inner">
                                            <select name="tire_status_rear" x-model="maintenanceVehicle.tire_status_rear" class="w-full bg-transparent border-none text-white text-sm font-bold py-3 px-2 focus:ring-0 cursor-pointer">
                                                <option value="good" class="bg-slate-900">🟢 Bueno</option>
                                                <option value="fair" class="bg-slate-900">🟡 Regular</option>
                                                <option value="poor" class="bg-slate-900">🔴 Malo (Cambiar)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="mt-12 flex justify-end gap-4 pt-8 border-t border-slate-800">
                        <button type="button" @click="$dispatch('close')" class="px-6 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                            {{ __('Cancelar') }}
                        </button>

                        <template x-if="!['maintenance', 'workshop'].includes(maintenanceVehicle.status)">
                            <button type="submit" form="update-maintenance-state-form" class="px-8 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-600/20 hover:bg-blue-500 hover:-translate-y-1 transition-all cursor-pointer">
                                {{ __('Actualizar Ficha Técnica') }}
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Tab: Solicitar Mantención -->
                <div x-show="tab === 'request'" x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <form method="POST" :action="maintenanceVehicle.storeRequestAction" class="space-y-8">
                        @csrf
                        <div class="space-y-6">
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Tipo de Solicitud</label>
                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-2 focus-within:border-blue-500 transition-all shadow-inner">
                                    <select name="type" required class="w-full bg-transparent border-none text-white text-sm font-bold py-3 px-2 focus:ring-0 cursor-pointer">
                                        <option value="oil" class="bg-slate-900">🛢️ Cambio de Aceite</option>
                                        <option value="tires" class="bg-slate-900">🛞 Cambio de Neumáticos</option>
                                        <option value="mechanics" class="bg-slate-900">🔧 Mecánica General</option>
                                        <option value="general" class="bg-slate-900">📋 Otro / Inspección</option>
                                    </select>
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Descripción Detallada</label>
                                <div class="border border-slate-800 rounded-3xl bg-slate-950 p-6 focus-within:border-blue-500 transition-all shadow-inner">
                                    <textarea name="description" rows="5" required class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-medium resize-none leading-relaxed" placeholder="Describe el problema o los detalles del servicio requerido..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-4 pt-8 border-t border-slate-800">
                            <button type="button" @click="$dispatch('close')" class="px-6 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="submit" class="px-10 py-3 bg-amber-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-amber-600/20 hover:bg-amber-500 hover:-translate-y-1 transition-all flex items-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                {{ __('Enviar Solicitud') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Historial (Placeholder for now) -->
                <div x-show="tab === 'history'" x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <div class="text-center py-20">
                        <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4 border border-slate-700">
                            <svg class="w-8 h-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-slate-500 font-black text-[10px] uppercase tracking-widest italic">Historial de órdenes en desarrollo</p>
                    </div>
                </div>

                <!-- Tab: Pendientes (Placeholder for now) -->
                <div x-show="tab === 'pending'" x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <div class="text-center py-20">
                        <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4 border border-slate-700">
                            <svg class="w-8 h-8 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <p class="text-slate-500 font-black text-[10px] uppercase tracking-widest italic">Solicitudes pendientes en desarrollo</p>
                    </div>
                </div>
            </div>
        </x-modal>
        <!-- Modal Solicitudes Pendientes -->
        <x-modal name="maintenance-requests-modal" :show="false" focusable maxWidth="5xl">
            <div class="p-0 bg-slate-900 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-800 shadow-2xl relative">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/50 backdrop-blur-md sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center border border-amber-500/20 shadow-inner">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase">{{ __('Solicitudes Pendientes') }}</h3>
                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">Control de aprobaciones administrativas</p>
                        </div>
                    </div>
                    <button type="button" @click="$dispatch('close')" class="text-slate-500 hover:text-white transition-colors cursor-pointer p-2 hover:bg-slate-800 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-8 space-y-10 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <!-- Sección de Reservas -->
                    <div class="space-y-6">
                        <h4 class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-[1px] bg-indigo-500/30"></span>
                            Reservas de Vehículos
                        </h4>
                        
                        @if(isset($pendingReservations) && $pendingReservations->count() > 0)
                            <div class="overflow-hidden border border-slate-800 rounded-[2rem] bg-slate-950/50 shadow-2xl">
                                <table class="min-w-full divide-y divide-slate-800">
                                    <thead>
                                        <tr class="bg-slate-900/50">
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">Solicitante</th>
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">Vehículo</th>
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">Destino</th>
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">Periodo</th>
                                            <th class="px-6 py-4 text-right text-[9px] font-black text-slate-500 uppercase tracking-widest">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/50">
                                        @foreach($pendingReservations as $reservation)
                                            <tr class="hover:bg-slate-800/30 transition-colors group">
                                                <td class="px-6 py-5 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div class="relative">
                                                            @if ($reservation->user->profile_photo_path)
                                                                <img class="h-10 w-10 rounded-2xl object-cover ring-2 ring-slate-800" src="{{ asset('storage/' . $reservation->user->profile_photo_path) }}" alt="" />
                                                            @else
                                                                <div class="h-10 w-10 rounded-2xl bg-indigo-600/10 flex items-center justify-center text-indigo-400 font-black text-sm border border-indigo-500/20 ring-2 ring-slate-800">
                                                                    {{ substr($reservation->user->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-black text-white group-hover:text-indigo-400 transition-colors">{{ $reservation->user->name }}</div>
                                                            <div class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $reservation->user->cargo ?? 'Personal' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 whitespace-nowrap">
                                                    <div class="flex items-center gap-2">
                                                        <div class="text-sm font-black text-white uppercase">{{ $reservation->vehicle->brand }}</div>
                                                        <div class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] font-mono text-slate-500 font-black">{{ $reservation->vehicle->plate }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5">
                                                    <div class="flex flex-col">
                                                        <div class="text-sm font-bold text-slate-300 truncate max-w-[150px]">{{ $reservation->destination }}</div>
                                                        <div class="text-[9px] uppercase font-black tracking-widest mt-0.5 {{ $reservation->destination_type === 'outside' ? 'text-amber-500' : 'text-blue-500' }}">
                                                            {{ $reservation->destination_type === 'outside' ? 'Comisión Fuera Ciudad' : 'Operación Local' }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 whitespace-nowrap">
                                                    <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
                                                        {{ $reservation->start_date->format('d M, H:i') }}
                                                    </div>
                                                    <div class="text-[9px] font-bold text-slate-600 mt-0.5">
                                                        Hasta {{ $reservation->end_date->format('d M, H:i') }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 whitespace-nowrap text-right">
                                                    <div class="flex justify-end gap-3">
                                                        <form action="{{ route('requests.approve', $reservation->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="w-10 h-10 bg-emerald-600/10 text-emerald-500 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center cursor-pointer group/btn" title="Aprobar">
                                                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            </button>
                                                        </form>
                                                        <button type="button" 
                                                            @click="rejectionUrl = '{{ route('requests.reject', $reservation->id) }}'; $dispatch('open-modal', 'reject-request-modal')"
                                                            class="w-10 h-10 bg-rose-600/10 text-rose-500 rounded-2xl hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center cursor-pointer group/btn" title="Rechazar">
                                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12 border-2 border-dashed border-slate-800 rounded-[2.5rem] bg-slate-950/20">
                                <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest italic italic">No hay reservas pendientes de aprobación</p>
                            </div>
                        @endif
                    </div>

                    <!-- Sección de Mantenimiento -->
                    <div class="space-y-6 pt-6 border-t border-slate-800">
                        <h4 class="text-[11px] font-black text-amber-400 uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-8 h-[1px] bg-amber-500/30"></span>
                            Solicitudes de Mantenimiento
                        </h4>
                        
                        @if($pendingRequests->isEmpty())
                            <div class="text-center py-12 border-2 border-dashed border-slate-800 rounded-[2.5rem] bg-slate-950/20">
                                <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest italic">No hay servicios técnicos solicitados</p>
                            </div>
                        @else
                            <div class="overflow-hidden border border-slate-800 rounded-[2rem] bg-slate-950/50 shadow-2xl">
                                <table class="min-w-full divide-y divide-slate-800">
                                    <thead>
                                        <tr class="bg-slate-900/50">
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">Vehículo</th>
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">Tipo de Servicio</th>
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-500 uppercase tracking-widest">Descripción</th>
                                            <th class="px-6 py-4 text-right text-[9px] font-black text-slate-500 uppercase tracking-widest">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/50">
                                        @foreach($pendingRequests as $req)
                                            <tr class="hover:bg-slate-800/30 transition-colors group">
                                                <td class="px-6 py-5 whitespace-nowrap">
                                                    <div class="flex items-center gap-2">
                                                        <div class="text-sm font-black text-white uppercase">{{ $req->vehicle ? $req->vehicle->brand : 'N/A' }}</div>
                                                        <div class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] font-mono text-slate-500 font-black">{{ $req->vehicle ? $req->vehicle->plate : '---' }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 whitespace-nowrap">
                                                    @switch($req->type)
                                                        @case('oil') <span class="px-3 py-1 text-[9px] font-black uppercase rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 tracking-widest">Aceite</span> @break
                                                        @case('tires') <span class="px-3 py-1 text-[9px] font-black uppercase rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 tracking-widest">Neumáticos</span> @break
                                                        @case('mechanics') <span class="px-3 py-1 text-[9px] font-black uppercase rounded-xl bg-rose-500/10 text-rose-500 border border-rose-500/20 tracking-widest">Mecánica</span> @break
                                                        @default <span class="px-3 py-1 text-[9px] font-black uppercase rounded-xl bg-slate-500/10 text-slate-400 border border-slate-500/20 tracking-widest">General</span>
                                                    @endswitch
                                                </td>
                                                <td class="px-6 py-5">
                                                    <p class="text-[11px] text-slate-400 font-medium line-clamp-1 max-w-[200px]" title="{{ $req->description }}">{{ $req->description }}</p>
                                                </td>
                                                <td class="px-6 py-5 whitespace-nowrap text-right">
                                                    <form method="POST" action="/maintenance/requests/{{ $req->id }}/accept">
                                                        @csrf
                                                        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 hover:-translate-y-1 transition-all cursor-pointer">
                                                            Aceptar Servicio
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-950/50 backdrop-blur-md border-t border-slate-800 flex items-center justify-end">
                    <button @click="$dispatch('close')" class="px-8 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                        {{ __('Cerrar Panel') }}
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- Modal Rechazar Solicitud -->
        <x-modal name="reject-request-modal" :show="false" focusable>
            <div class="p-0 bg-slate-900 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-800 shadow-2xl relative">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/50 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center border border-rose-500/20 shadow-inner">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase">{{ __('Rechazar Solicitud') }}</h3>
                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">Indique el motivo administrativo</p>
                        </div>
                    </div>
                </div>

                <form :action="rejectionUrl" method="POST" class="p-8 space-y-8">
                    @csrf
                    <div class="group">
                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-rose-400 transition-colors">Motivo del Rechazo</label>
                        <div class="border border-slate-800 rounded-3xl bg-slate-950 p-6 focus-within:border-rose-500 transition-all shadow-inner">
                            <textarea name="rejection_reason" rows="4" required class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-medium resize-none leading-relaxed" placeholder="Ej: No existe disponibilidad del vehículo para la fecha solicitada..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" @click="$dispatch('close')" class="px-6 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                            {{ __('Cancelar') }}
                        </button>
                        
                        <button type="submit" class="px-8 py-3 bg-rose-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-rose-600/20 hover:bg-rose-500 hover:-translate-y-1 transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            {{ __('Confirmar Rechazo') }}
                        </button>
                    </div>
                </form>
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
            <div class="p-8 bg-[#0f172a] text-slate-100" x-data="{ activeTab: 'list' }">
                <div class="mb-8 border-b border-slate-800 pb-4">
                    <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-500/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        {{ __('Gestión Documental') }}
                    </h2>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest" x-text="viewingVehicle.plate"></span>
                        <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="viewingVehicle.brand + ' ' + viewingVehicle.model"></span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex gap-2 mb-8 bg-slate-900/50 p-1.5 rounded-2xl border border-slate-800 w-fit">
                    <button @click="activeTab = 'list'" :class="activeTab === 'list' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:text-slate-200'" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200 cursor-pointer">Documentos</button>
                    <button @click="activeTab = 'upload'" :class="activeTab === 'upload' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-slate-400 hover:text-slate-200'" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200 cursor-pointer">Subir Nuevo</button>
                </div>

                <!-- Lista -->
                <div x-show="activeTab === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <template x-if="viewingVehicle.documents && viewingVehicle.documents.length > 0">
                        <div class="space-y-3">
                            <template x-for="doc in viewingVehicle.documents" :key="doc.id">
                                <div class="flex items-center justify-between bg-slate-800/40 p-4 rounded-2xl border border-slate-700/50 hover:border-slate-500 transition-all group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-slate-400 group-hover:bg-blue-500/10 group-hover:text-blue-400 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-white uppercase tracking-tight" x-text="doc.type === 'insurance' ? 'Seguro (SOAP)' : (doc.type === 'technical_review' ? 'Revisión Técnica' : 'Permiso Circulación')"></div>
                                            <div class="text-[10px] font-bold" :class="getDaysRemaining(doc.expires_at) < 0 ? 'text-rose-500' : 'text-slate-500'">
                                                Vence: <span x-text="doc.expires_at ? doc.expires_at.split('T')[0].split('-').reverse().join('/') : ''"></span>
                                                <template x-if="getDaysRemaining(doc.expires_at) < 0">
                                                    <span class="ml-2 px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-500 text-[8px] font-black uppercase tracking-widest border border-rose-500/20">Vencido</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a :href="'/storage/' + doc.file_path" target="_blank" class="p-2 bg-blue-600/10 text-blue-400 rounded-xl hover:bg-blue-600 hover:text-white transition-all cursor-pointer" title="Ver Documento">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        
                                        <form method="POST" :action="'/vehicles/documents/' + doc.id" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-600/10 text-rose-500 rounded-xl hover:bg-rose-600 hover:text-white transition-all cursor-pointer" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!viewingVehicle.documents || viewingVehicle.documents.length === 0">
                        <div class="text-center py-10 border-2 border-dashed border-slate-800 rounded-3xl">
                            <p class="text-sm text-slate-500 font-bold italic">No hay documentos registrados</p>
                        </div>
                    </template>
                </div>

                <!-- Subir -->
                <div x-show="activeTab === 'upload'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <form method="POST" :action="'/vehicles/' + viewingVehicle.id + '/documents'" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Tipo de Documento</label>
                                <div class="flex items-center border border-slate-700 rounded-xl bg-slate-900/50 focus-within:border-blue-500 transition-all overflow-hidden">
                                    <select name="type" required class="w-full bg-transparent border-none text-slate-100 text-sm font-bold py-3 px-4 focus:ring-0 cursor-pointer">
                                        <option value="insurance" class="bg-slate-900">Seguro Obligatorio (SOAP)</option>
                                        <option value="technical_review" class="bg-slate-900">Revisión Técnica</option>
                                        <option value="permit" class="bg-slate-900">Permiso de Circulación</option>
                                        <option value="other" class="bg-slate-900">Otro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Fecha de Vencimiento</label>
                                <div class="flex items-center border border-slate-700 rounded-xl bg-slate-900/50 px-4 py-3 focus-within:border-blue-500 transition-all">
                                    <input type="date" name="expires_at" required class="w-full bg-transparent border-none outline-none text-slate-100 text-sm font-bold">
                                </div>
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Archivo (PDF o Imagen)</label>
                            <input type="file" name="file" required accept=".pdf,image/*" 
                                class="block w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-all cursor-pointer bg-slate-900/50 border border-slate-700 rounded-xl p-1.5" />
                        </div>

                        <div class="pt-6 flex justify-end">
                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                Subir Documento
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-10 pt-6 border-t border-slate-800 flex justify-end">
                    <button @click="$dispatch('close')" class="px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all cursor-pointer">
                        {{ __('Cerrar') }}
                    </button>
                </div>
            </div>
        </x-modal>
    </div>

    <!-- Modal Detalle de Reserva -->
    <!-- Modal Detalle Reserva -->
    <x-modal name="reservation-detail-modal" :show="false" focusable maxWidth="4xl">
        <div class="p-0 bg-slate-900 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-800 shadow-2xl relative" 
            x-data="{ 
                showEarlyTermination: false,
                getDates() {
                    if (!viewingVehicle || !viewingVehicle.reservation) return [];
                    const start = new Date(viewingVehicle.reservation.start_date?.replace(/-/g, '/') || new Date()); 
                    const end = new Date(viewingVehicle.reservation.end_date?.replace(/-/g, '/') || new Date());
                    const days = [];
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
            <!-- Header -->
            <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/50 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20 shadow-inner">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight uppercase">{{ __('Detalles de la Comisión') }}</h3>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5" x-text="'Reserva #' + (viewingVehicle.reservation?.id || '---')"></p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close')" class="text-slate-500 hover:text-white transition-colors cursor-pointer p-2 hover:bg-slate-800 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Columna Info -->
                    <div class="space-y-6">
                        <!-- Destino -->
                        <div class="bg-slate-950/40 p-6 rounded-[2rem] border border-slate-800/50 shadow-inner">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Destino de la Operación</span>
                            <div class="text-lg font-black text-white mb-1" x-text="viewingVehicle.reservation?.destination"></div>
                            <span class="px-3 py-1 text-[9px] font-black uppercase rounded-xl border tracking-widest" 
                                :class="viewingVehicle.reservation?.destination_type === 'outside' ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' : 'bg-blue-500/10 text-blue-500 border-blue-500/20'"
                                x-text="viewingVehicle.reservation?.destination_type === 'outside' ? 'Comisión fuera de la ciudad' : 'Operación Local'"></span>
                        </div>

                        <!-- Cronograma -->
                        <div class="bg-slate-950/40 p-6 rounded-[2rem] border border-slate-800/50 shadow-inner">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Cronograma del Servicio</span>
                                <div class="font-black text-xs font-mono" 
                                    :class="viewingVehicle.reservation?.days_remaining < 1 ? 'text-rose-500' : 'text-emerald-400'" 
                                    x-text="viewingVehicle.reservation?.days_remaining >= 0 ? viewingVehicle.reservation?.days_remaining + ' Días Restantes' : 'Expirado'"></div>
                            </div>
                            <div class="flex items-center justify-between gap-4 bg-slate-900/50 p-4 rounded-2xl border border-slate-800 mb-6">
                                <div class="flex-1 text-center">
                                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-1">Inicio</div>
                                    <div class="text-sm font-black text-white" x-text="viewingVehicle.reservation?.start_date?.split(' ')[0] || ''"></div>
                                    <div class="text-[10px] font-bold text-slate-400 mt-0.5" x-text="(viewingVehicle.reservation?.start_date?.split(' ')[1] || '') + ' hrs'"></div>
                                </div>
                                <div class="w-10 h-[1px] bg-slate-800"></div>
                                <div class="flex-1 text-center">
                                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-1">Retorno</div>
                                    <div class="text-sm font-black text-white" x-text="viewingVehicle.reservation?.end_date?.split(' ')[0] || ''"></div>
                                    <div class="text-[10px] font-bold text-slate-400 mt-0.5" x-text="(viewingVehicle.reservation?.end_date?.split(' ')[1] || '') + ' hrs'"></div>
                                </div>
                            </div>

                            <!-- Mini Calendar Timeline -->
                            <div class="space-y-3">
                                <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest pl-1">Progreso Diario</span>
                                <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar">
                                    <template x-for="date in getDates()" :key="date.getTime()">
                                        <div class="flex-shrink-0 w-11 h-14 rounded-2xl flex flex-col items-center justify-center border transition-all duration-300 shadow-lg"
                                            :class="{
                                                'bg-blue-600 border-blue-400 text-white shadow-blue-600/20 scale-110 z-10': date.toDateString() === new Date().toDateString(),
                                                'bg-slate-950/40 border-slate-800 text-slate-600 opacity-40': date < new Date().setHours(0,0,0,0),
                                                'bg-slate-900 border-slate-800 text-slate-400': date > new Date()
                                            }">
                                            <span class="text-[7px] uppercase font-black tracking-tighter" x-text="date.toLocaleDateString('es-ES', { weekday: 'short' }).slice(0,3)"></span>
                                            <span class="text-sm font-black leading-none mt-1" x-text="date.getDate()"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Personal -->
                    <div class="space-y-6">
                        <!-- Solicitante -->
                        <div class="bg-slate-950/40 p-6 rounded-[2rem] border border-slate-800/50 shadow-inner group/card">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Personal Solicitante</span>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center border border-indigo-500/20 shadow-inner font-black text-xl group-hover/card:scale-110 transition-transform">
                                    <span x-text="viewingVehicle.reservation?.user_name?.charAt(0)"></span>
                                </div>
                                <div>
                                    <div class="text-sm font-black text-white" x-text="viewingVehicle.reservation?.user_name"></div>
                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5" x-text="viewingVehicle.reservation?.user_cargo || 'Personal Autorizado'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Conductor -->
                        <div class="bg-slate-950/40 p-6 rounded-[2rem] border border-slate-800/50 shadow-inner group/card" 
                            :class="{'border-emerald-500/30 bg-emerald-500/5': viewingVehicle.reservation?.has_external_conductor}">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Conductor Designado</span>
                                <template x-if="viewingVehicle.reservation?.has_external_conductor">
                                    <span class="px-2 py-0.5 bg-emerald-500 text-slate-950 text-[8px] font-black uppercase rounded-lg tracking-widest">Externo</span>
                                </template>
                            </div>
                            
                            <template x-if="viewingVehicle.reservation?.has_external_conductor">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-600/20 text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-inner font-black text-xl">
                                        <span x-text="viewingVehicle.reservation?.conductor_name?.charAt(0)"></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-white" x-text="viewingVehicle.reservation?.conductor_name"></div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5" x-text="'RUT: ' + viewingVehicle.reservation?.conductor_rut"></div>
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="!viewingVehicle.reservation?.has_external_conductor">
                                <div class="flex items-center gap-3 text-slate-500 italic py-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-widest">Mismo personal solicitante</span>
                                </div>
                            </template>
                        </div>

                        <!-- Acompañantes -->
                        <div class="bg-slate-950/40 p-6 rounded-[2rem] border border-slate-800/50 shadow-inner" x-show="viewingVehicle.reservation?.companions?.length > 0">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Acompañantes</span>
                                <span class="px-2 py-0.5 bg-slate-900 border border-slate-800 rounded-lg text-[9px] font-black text-blue-400" x-text="viewingVehicle.reservation?.companions?.length"></span>
                            </div>
                            <div class="flex -space-x-3 overflow-hidden mb-4">
                                <template x-for="(companion, index) in (viewingVehicle.reservation?.companions || [])" :key="index">
                                    <div class="inline-block h-10 w-10 rounded-2xl ring-4 ring-slate-900 bg-slate-800 flex items-center justify-center text-[10px] font-black text-slate-300 border border-slate-700 hover:scale-110 hover:z-20 transition-all cursor-help" :title="companion.name">
                                        <span x-text="companion.name.charAt(0)"></span>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="viewingCompanions = viewingVehicle.reservation?.companions || []; $dispatch('open-modal', 'companions-list-modal')" class="text-[9px] font-black text-blue-500 hover:text-blue-400 uppercase tracking-widest transition-colors cursor-pointer flex items-center gap-2">
                                Ver nómina completa
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sección de Término -->
                <div class="pt-8 border-t border-slate-800">
                    <div x-show="!showEarlyTermination" class="flex justify-end gap-4">
                        <button @click="showEarlyTermination = true" class="px-8 py-3 bg-rose-600/10 text-rose-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl border border-rose-500/20 hover:bg-rose-600 hover:text-white transition-all cursor-pointer">
                            {{ __('Finalizar Asignación Ahora') }}
                        </button>
                    </div>

                    <div x-show="showEarlyTermination" x-transition class="bg-rose-500/5 border border-rose-500/20 rounded-[2.5rem] p-8 shadow-2xl">
                        <div class="flex items-center gap-5 mb-8">
                            <div class="w-14 h-14 rounded-2xl bg-rose-600/20 text-rose-500 flex items-center justify-center border border-rose-500/20 animate-pulse">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-white uppercase tracking-tight">Confirmación de Término</h4>
                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Esta acción liberará el vehículo para nuevas reservas</p>
                            </div>
                        </div>

                        <form method="POST" :action="'/requests/' + viewingVehicle.reservation?.id + '/finish-early'" class="space-y-6">
                            @csrf
                            <div class="group">
                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-rose-400 transition-colors pl-1">Motivo del Término (Obligatorio)</label>
                                <div class="border border-slate-800 rounded-[1.5rem] bg-slate-950 p-5 focus-within:border-rose-500 transition-all shadow-inner">
                                    <textarea name="early_termination_reason" rows="3" required class="w-full bg-transparent border-none outline-none text-white placeholder-slate-800 text-sm font-medium resize-none" placeholder="Indique la razón del término anticipado..."></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4">
                                <button type="button" @click="showEarlyTermination = false" class="px-8 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-widest transition-colors cursor-pointer">
                                    {{ __('Volver') }}
                                </button>
                                <button type="submit" class="px-10 py-3 bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-500 shadow-xl shadow-rose-600/20 transition-all cursor-pointer">
                                    {{ __('Confirmar y Liberar Vehículo') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-slate-950/50 backdrop-blur-md border-t border-slate-800 flex items-center justify-end">
                <button @click="$dispatch('close')" class="px-8 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                    {{ __('Cerrar Detalle') }}
                </button>
            </div>
        </div>
    </x-modal>

    <!-- Modal Lista de Acompañantes -->
    <x-modal name="companions-list-modal" :show="false" focusable maxWidth="2xl">
        <div class="p-0 bg-slate-900 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-800 shadow-2xl relative">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/50 backdrop-blur-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 shadow-inner">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight uppercase">{{ __('Nómina de Acompañantes') }}</h3>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">Lista de personal registrado para este viaje</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close')" class="text-slate-500 hover:text-white transition-colors cursor-pointer p-2 hover:bg-slate-800 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8 space-y-4 max-h-[60vh] overflow-y-auto custom-scrollbar">
                <template x-if="viewingCompanions && viewingCompanions.length > 0">
                    <div class="space-y-4">
                        <template x-for="(companion, index) in viewingCompanions" :key="index">
                            <div class="flex items-center justify-between bg-slate-950/40 p-5 rounded-2xl border border-slate-800/50 hover:border-slate-700 transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-900 group-hover:bg-indigo-600/10 group-hover:text-indigo-400 transition-colors flex items-center justify-center text-slate-500 font-black text-lg border border-slate-800 shadow-inner">
                                        <span x-text="companion.name.charAt(0)"></span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-white" x-text="companion.name"></div>
                                        <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5" x-text="companion.type + ' — ' + (companion.department || 'Externo')"></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] font-black text-slate-600 uppercase tracking-widest">RUT</div>
                                    <div class="text-[11px] font-mono font-bold text-slate-400 mt-0.5" x-text="companion.rut || '---'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!viewingCompanions || viewingCompanions.length === 0">
                    <div class="text-center py-12 border-2 border-dashed border-slate-800 rounded-[2.5rem] bg-slate-950/20">
                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-widest italic">No se encontraron registros de personal adjunto</p>
                    </div>
                </template>
            </div>

            <div class="px-8 py-6 bg-slate-950/50 backdrop-blur-md border-t border-slate-800 flex items-center justify-end">
                <button @click="$dispatch('close')" class="px-8 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                    {{ __('Regresar al Detalle') }}
                </button>
            </div>
        </div>
    </x-modal>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initFlatpickr = () => {
                flatpickr(".flatpickr-date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d/m/Y",
                    locale: "es",
                    theme: "dark",
                    disableMobile: "true"
                });
            };

            initFlatpickr();

            // Re-init on modal open if needed
            window.addEventListener('open-modal', () => {
                setTimeout(initFlatpickr, 100);
            });
        });
    </script>
</x-app-layout>
