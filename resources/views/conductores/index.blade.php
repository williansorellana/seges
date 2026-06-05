<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Conductores') }}
            </h2>
            <div class="flex flex-wrap gap-3 items-center">                <!-- Papelera -->
                <a href="{{ route('conductores.trash') }}"
                    class="inline-flex items-center px-4 py-2 bg-slate-800/50 border border-slate-700 rounded-lg font-bold text-[11px] text-slate-300 uppercase tracking-wider hover:bg-slate-700 hover:text-white transition-all duration-300 group h-10 shadow-sm cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('Papelera') }}
                </a>

                <!-- Nuevo -->
                <button @click="openCreateModal = true; $dispatch('open-modal', 'create-conductor-modal')"
                    class="inline-flex items-center px-5 py-2 bg-blue-600 border border-blue-500 rounded-lg font-bold text-[11px] text-white uppercase tracking-wider hover:bg-blue-500 hover:scale-105 active:scale-95 transition-all duration-300 group h-10 shadow-[0_0_20px_rgba(37,99,235,0.3)] cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Nuevo Conductor') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
            width: window.innerWidth, 
            openDeleteModal: false, 
            openViewModal: false, 
            openEditModal: false, 
            openCreateModal: false, 
            deleteAction: '', 
            viewingConductor: {}, 
            editingConductor: { remove_foto: false },
            formatRut(person) {
                if (!person.rut) return;
                let value = person.rut.replace(/[^0-9kK]/g, '').toUpperCase();
                if (value.length > 1) {
                    const dv = value.slice(-1);
                    let body = value.slice(0, -1);
                    body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    person.rut = body + '-' + dv;
                } else {
                    person.rut = value;
                }
            }
        }" @resize.window="width = window.innerWidth" @open-create-modal.window="openCreateModal = true; $dispatch('open-modal', 'create-conductor-modal')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Stats Card Briefing -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative group bg-slate-800/40 border border-slate-700/50 p-5 rounded-2xl shadow-inner">
                    <div class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Total Conductores</div>
                    <div class="text-3xl font-black text-white">{{ $conductores->count() }}</div>
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                </div>
                <div class="relative group bg-slate-800/40 border border-emerald-500/20 p-5 rounded-2xl shadow-inner">
                    <div class="text-emerald-400 text-[10px] font-bold uppercase tracking-widest mb-1">Licencias Vigentes</div>
                    <div class="text-3xl font-black text-white">{{ $conductores->filter(fn($c) => !$c->fecha_licencia->isPast())->count() }}</div>
                    <div class="absolute top-0 right-0 p-4 text-emerald-500/20">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="relative group bg-slate-800/40 border border-rose-500/20 p-5 rounded-2xl shadow-inner animate-pulse">
                    <div class="text-rose-400 text-[10px] font-bold uppercase tracking-widest mb-1">Licencias Vencidas</div>
                    <div class="text-3xl font-black text-white">{{ $conductores->filter(fn($c) => $c->fecha_licencia->isPast())->count() }}</div>
                    <div class="absolute top-0 right-0 p-4 text-rose-500/20">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                </div>
            </div>

            <!-- Desktop View -->
            <div x-show="width >= 768"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-900/80 text-slate-500 border-b border-slate-800">
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Foto</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Nombre / RUT</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Cargo / Depto</th>
                                    <th class="px-6 py-5 text-left text-[9px] font-black uppercase tracking-[0.2em]">Estado Licencia</th>
                                    <th class="px-6 py-5 text-right text-[9px] font-black uppercase tracking-[0.2em]">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 bg-slate-950/20">
                                @forelse($conductores as $conductor)
                                    <tr class="hover:bg-slate-900/40 transition-all duration-300 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($conductor->fotografia)
                                                <div class="h-12 w-12 flex-shrink-0 group-hover:scale-110 transition-all duration-500 relative">
                                                    <div class="absolute -inset-1 bg-blue-600/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                    <img class="relative h-12 w-12 rounded-2xl object-cover border border-slate-800 shadow-2xl"
                                                        src="{{ asset('storage/' . $conductor->fotografia) }}"
                                                        alt="{{ $conductor->nombre }}">
                                                </div>
                                            @else
                                                <div class="h-12 w-12 rounded-2xl bg-slate-900 flex items-center justify-center text-[9px] font-black text-slate-600 border border-slate-800 group-hover:border-slate-700 transition-colors uppercase">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-white group-hover:text-blue-400 transition-colors">{{ $conductor->nombre }}</div>
                                            @if($conductor->rut)
                                                <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $conductor->rut }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-white">{{ $conductor->cargo }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $conductor->departamento }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php $vencida = $conductor->fecha_licencia->isPast(); @endphp
                                            @if($vencida)
                                                <span class="px-3 py-1.5 inline-flex text-[9px] font-black rounded-xl text-rose-400 bg-rose-500/5 border border-rose-500/20 uppercase tracking-widest shadow-inner animate-pulse">
                                                    Vencida
                                                </span>
                                            @else
                                                 <span class="px-3 py-1.5 inline-flex text-[9px] font-black rounded-xl text-emerald-400 bg-emerald-500/5 border border-emerald-500/20 uppercase tracking-widest shadow-inner">
                                                    Vence: {{ $conductor->fecha_licencia->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- Ver Detalle -->
                                                <button @click="viewingConductor = { 
                                                                    nombre: '{{ $conductor->nombre }}',
                                                                    rut: '{{ $conductor->rut ?? '' }}',
                                                                    cargo: '{{ $conductor->cargo }}', 
                                                                    depto: '{{ $conductor->departamento }}', 
                                                                    vencimiento: '{{ $conductor->fecha_licencia->format('d/m/Y') }}', 
                                                                    foto: '{{ $conductor->fotografia ? asset('storage/' . $conductor->fotografia) : '' }}',
                                                                    is_expired: {{ $conductor->fecha_licencia->isPast() ? 'true' : 'false' }}
                                                                }; openViewModal = true; $dispatch('open-modal', 'view-conductor-modal')"
                                                    class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-emerald-500 hover:text-white hover:bg-emerald-600 hover:border-emerald-500 shadow-lg hover:shadow-emerald-600/20 transition-all duration-300 flex items-center justify-center cursor-pointer"
                                                    title="Ver Ficha">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>

                                                <!-- Editar -->
                                                <button @click="editingConductor = {
                                                                    id: {{ $conductor->id }},
                                                                    nombre: '{{ $conductor->nombre }}',
                                                                    rut: '{{ $conductor->rut ?? '' }}',
                                                                    cargo: '{{ $conductor->cargo }}',
                                                                    depto: '{{ $conductor->departamento }}',
                                                                    vencimiento: '{{ $conductor->fecha_licencia->format('Y-m-d') }}',
                                                                    foto: '{{ $conductor->fotografia ? asset('storage/' . $conductor->fotografia) : '' }}',
                                                                    has_foto: {{ $conductor->fotografia ? 'true' : 'false' }},
                                                                    remove_foto: false
                                                                }; openEditModal = true; $dispatch('open-modal', 'edit-conductor-modal')"
                                                    class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-blue-500 hover:text-white hover:bg-blue-600 hover:border-blue-500 shadow-lg hover:shadow-blue-600/20 transition-all duration-300 flex items-center justify-center cursor-pointer"
                                                    title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <!-- Eliminar -->
                                                <button @click="deleteAction = '{{ route('conductores.destroy', $conductor) }}'; openDeleteModal = true; $dispatch('open-modal', 'confirm-delete-conductor-modal')"
                                                    class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-rose-500 hover:text-white hover:bg-rose-600 hover:border-rose-500 shadow-lg hover:shadow-rose-600/20 transition-all duration-300 flex items-center justify-center cursor-pointer"
                                                    title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-500 font-bold uppercase tracking-widest text-[11px] italic">No hay conductores registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mobile View -->
            <div x-show="width < 768" class="space-y-4">
                @forelse($conductores as $conductor)
                    <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-5 shadow-xl flex flex-col gap-4">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl overflow-hidden border border-slate-800 relative flex-shrink-0">
                                @if($conductor->fotografia)
                                    <img src="{{ asset('storage/' . $conductor->fotografia) }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full bg-slate-900 flex items-center justify-center text-[9px] font-black text-slate-600 uppercase">
                                        N/A
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-black text-white truncate">{{ $conductor->nombre }}</h3>
                                @if($conductor->rut)
                                    <p class="text-[10px] text-slate-500 font-mono font-bold mt-0.5">{{ $conductor->rut }}</p>
                                @endif
                                <p class="text-xs text-slate-400 font-medium mt-1 truncate">{{ $conductor->cargo }} — {{ $conductor->departamento }}</p>
                            </div>
                        </div>

                        @php $vencida = $conductor->fecha_licencia->isPast(); @endphp
                        <div class="flex justify-between items-center bg-slate-950/40 px-4 py-3 rounded-xl border border-slate-800/50 text-[10px] font-black uppercase tracking-wider">
                            <span class="text-slate-500">Licencia:</span>
                            @if($vencida)
                                <span class="text-rose-400 animate-pulse">Vencida ({{ $conductor->fecha_licencia->format('d/m/Y') }})</span>
                            @else
                                <span class="text-emerald-400">Vence: {{ $conductor->fecha_licencia->format('d/m/Y') }}</span>
                            @endif
                        </div>

                        <div class="flex justify-end gap-2 border-t border-slate-800/60 pt-3">
                            <button @click="viewingConductor = { 
                                                    nombre: '{{ $conductor->nombre }}',
                                                    rut: '{{ $conductor->rut ?? '' }}',
                                                    cargo: '{{ $conductor->cargo }}', 
                                                    depto: '{{ $conductor->departamento }}', 
                                                    vencimiento: '{{ $conductor->fecha_licencia->format('d/m/Y') }}', 
                                                    foto: '{{ $conductor->fotografia ? asset('storage/' . $conductor->fotografia) : '' }}',
                                                    is_expired: {{ $conductor->fecha_licencia->isPast() ? 'true' : 'false' }} 
                                                }; openViewModal = true"
                                class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-emerald-500 hover:text-white hover:bg-emerald-600 transition-all flex items-center justify-center cursor-pointer shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button @click="editingConductor = {
                                                    id: {{ $conductor->id }},
                                                    nombre: '{{ $conductor->nombre }}',
                                                    rut: '{{ $conductor->rut ?? '' }}',
                                                    cargo: '{{ $conductor->cargo }}',
                                                    depto: '{{ $conductor->departamento }}',
                                                    vencimiento: '{{ $conductor->fecha_licencia->format('Y-m-d') }}',
                                                    foto: '{{ $conductor->fotografia ? asset('storage/' . $conductor->fotografia) : '' }}',
                                                    has_foto: {{ $conductor->fotografia ? 'true' : 'false' }},
                                                    remove_foto: false
                                                }; openEditModal = true"
                                class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-blue-500 hover:text-white hover:bg-blue-600 transition-all flex items-center justify-center cursor-pointer shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button @click="deleteAction = '{{ route('conductores.destroy', $conductor) }}'; openDeleteModal = true; $dispatch('open-modal', 'confirm-delete-conductor-modal')"
                                class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 text-rose-500 hover:text-white hover:bg-rose-600 transition-all flex items-center justify-center cursor-pointer shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-800/40 border border-slate-700/50 rounded-2xl p-8 text-center text-slate-500 text-xs font-bold uppercase tracking-wider">
                        No hay conductores registrados
                    </div>
                @endforelse
            </div>
        </div>

        <!-- View Modal -->
        <x-modal name="view-conductor-modal" :show="false" @close="openViewModal = false" maxWidth="2xl">
            <div class="p-0 bg-slate-800 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-700 shadow-2xl relative">
                <!-- Header with Cover Image/Background -->
                <div class="relative h-40 bg-slate-800/50">
                    <template x-if="viewingConductor.foto">
                        <img :src="viewingConductor.foto" alt="Foto Conductor" class="w-full h-full object-cover opacity-40 transition-opacity duration-700">
                    </template>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-800 via-slate-800/40 to-transparent"></div>
                    
                    <div class="absolute bottom-6 left-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center border border-blue-500/30 shadow-inner backdrop-blur-sm">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-black text-white tracking-tighter uppercase" x-text="viewingConductor.nombre"></h2>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em] mt-1" x-text="viewingConductor.cargo + ' — ' + viewingConductor.depto"></p>
                            </div>
                        </div>
                    </div>

                    <button @click="openViewModal = false; $dispatch('close-modal', 'view-conductor-modal')" class="absolute top-6 right-6 w-9 h-9 bg-slate-800/50 hover:bg-slate-700 rounded-xl text-slate-400 hover:text-white transition-all flex items-center justify-center cursor-pointer backdrop-blur-md border border-slate-700/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-8 space-y-8 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left Side: Profile Image / Placeholder -->
                        <div class="flex flex-col items-center justify-center bg-slate-950/40 rounded-3xl p-4 border border-slate-700 shadow-inner min-h-[200px] relative overflow-hidden group">
                            <template x-if="viewingConductor.foto">
                                <img :src="viewingConductor.foto" class="w-full h-48 object-cover rounded-2xl shadow-md border border-slate-700 hover:scale-105 transition-transform duration-500">
                            </template>
                            <template x-if="!viewingConductor.foto">
                                <div class="w-full h-48 flex flex-col items-center justify-center text-slate-600 rounded-2xl border border-slate-700 border-dashed">
                                    <svg class="h-12 w-12 mb-2 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-[10px] font-black uppercase tracking-wider">Sin fotografía</span>
                                </div>
                            </template>
                        </div>

                        <!-- Right Side: Details -->
                        <div class="space-y-6">
                            <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-700/50 shadow-inner">
                                <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">RUT Conductor</span>
                                <span class="text-base font-black text-white font-mono" x-text="viewingConductor.rut || '---'"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-700/50 shadow-inner">
                                    <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Cargo</span>
                                    <span class="text-sm font-black text-white" x-text="viewingConductor.cargo"></span>
                                </div>
                                <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-700/50 shadow-inner">
                                    <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Departamento</span>
                                    <span class="text-sm font-black text-white" x-text="viewingConductor.depto"></span>
                                </div>
                            </div>

                            <div class="bg-slate-950/40 p-5 rounded-3xl border border-slate-700/50 shadow-inner">
                                <span class="block text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">Vencimiento Licencia</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-base font-black font-mono" :class="viewingConductor.is_expired ? 'text-rose-500' : 'text-emerald-500'" x-text="viewingConductor.vencimiento"></span>
                                    <template x-if="viewingConductor.is_expired">
                                        <span class="px-2.5 py-1 text-[9px] font-black bg-rose-500/5 text-rose-400 rounded-lg border border-rose-500/20 uppercase tracking-widest animate-pulse">
                                            Vencida
                                        </span>
                                    </template>
                                    <template x-if="!viewingConductor.is_expired">
                                        <span class="px-2.5 py-1 text-[9px] font-black bg-emerald-500/5 text-emerald-400 rounded-lg border border-emerald-500/20 uppercase tracking-widest">
                                            Vigente
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-950/50 backdrop-blur-md border-t border-slate-700 flex items-center justify-end">
                    <button @click="openViewModal = false; $dispatch('close-modal', 'view-conductor-modal')" class="px-8 py-3 text-[10px] font-black text-slate-500 hover:text-white uppercase tracking-[0.2em] transition-colors cursor-pointer">
                        {{ __('Cerrar Ficha') }}
                    </button>
                </div>
            </div>
        </x-modal>

        <!-- Create Modal -->
        <x-modal name="create-conductor-modal" :show="false" @close="openCreateModal = false" maxWidth="3xl">
            <form action="{{ route('conductores.store') }}" method="POST" enctype="multipart/form-data" 
                class="p-0 bg-slate-800 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-700 shadow-2xl relative"
                x-data="{
                    photoPreview: null,
                    isCompressing: false,
                    rut: '',
                    formatRut() {
                        let value = this.rut.replace(/[^0-9kK]/g, '').toUpperCase();
                        if (value.length > 1) {
                            const dv = value.slice(-1);
                            let body = value.slice(0, -1);
                            body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            this.rut = body + '-' + dv;
                        } else {
                            this.rut = value;
                        }
                    },
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

                <!-- Header -->
                <div class="px-6 py-5 border-b border-slate-700 flex items-center justify-between bg-slate-800/50 backdrop-blur-md sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 shadow-inner">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-white tracking-tight">{{ __('Registrar Nuevo Conductor') }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Ingreso de ficha para personal de conducción</p>
                        </div>
                    </div>
                    <button type="button" @click="openCreateModal = false; $dispatch('close-modal', 'create-conductor-modal')" class="text-slate-500 hover:text-white transition-colors cursor-pointer p-2 hover:bg-slate-800 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Nombre Completo</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="nombre" required placeholder="Ej: Juan Pérez" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- RUT -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">RUT (Opcional)</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="rut" x-model="rut" @input="formatRut()" placeholder="Ej: 12.345.678-9" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- Cargo -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Cargo</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="cargo" required placeholder="Ej: Chofer de Reparto" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- Departamento -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Departamento</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="departamento" required placeholder="Ej: Logística" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- Fecha Licencia -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Vencimiento Licencia</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="date" name="fecha_licencia" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal cursor-pointer [&::-webkit-calendar-picker-indicator]:invert">
                            </div>
                        </div>

                        <!-- Fotografía -->
                        <div class="md:col-span-2 space-y-4 pt-4 border-t border-slate-700/60">
                            <label class="block text-sm font-medium text-gray-300">Fotografía (Opcional)</label>

                            <!-- Preview Box -->
                            <div class="mb-3" x-show="photoPreview" style="display: none;">
                                <span
                                    class="block rounded-3xl w-full h-40 bg-cover bg-no-repeat bg-center mx-auto border border-slate-700 shadow-inner"
                                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" x-on:click.prevent="$refs.photo.click()"
                                    :disabled="isCompressing"
                                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg border border-slate-700 transition-colors shadow-sm text-xs font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!isCompressing">Seleccionar Imagen</span>
                                    <span x-show="isCompressing">Procesando...</span>
                                </button>
                                <span x-show="!photoPreview" class="text-xs text-slate-500">Ningún archivo seleccionado</span>
                            </div>

                            <input id="create-fotografia" name="fotografia" type="file" accept="image/*"
                                class="hidden" x-ref="photo" x-on:change="
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
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-5 bg-slate-800/80 backdrop-blur-md border-t border-slate-700 flex items-center justify-end gap-3 sticky bottom-0 z-10">
                    <button type="button" @click="openCreateModal = false; $dispatch('close-modal', 'create-conductor-modal')" class="px-5 py-2.5 border border-slate-600 text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-800 hover:border-slate-500 transition-all cursor-pointer flex items-center justify-center h-10">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center h-10">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        {{ __('Registrar Conductor') }}
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Edit Modal -->
        <x-modal name="edit-conductor-modal" :show="false" @close="openEditModal = false" maxWidth="3xl">
            <form :action="'/conductores/' + editingConductor.id" method="POST" enctype="multipart/form-data"
                class="p-0 bg-slate-800 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-700 shadow-2xl relative"
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

                <input type="hidden" name="remove_fotografia" :value="editingConductor.remove_foto ? '1' : '0'">

                <!-- Header -->
                <div class="px-6 py-5 border-b border-slate-700 flex items-center justify-between bg-slate-800/50 backdrop-blur-md sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 shadow-inner">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-white tracking-tight">{{ __('Editar Conductor') }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Modificación de perfil and estado de licencia</p>
                        </div>
                    </div>
                    <button type="button" @click="openEditModal = false; $dispatch('close-modal', 'edit-conductor-modal')" class="text-slate-500 hover:text-white transition-colors cursor-pointer p-2 hover:bg-slate-800 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Nombre Completo</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="nombre" :value="editingConductor.nombre" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- RUT -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">RUT (Opcional)</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="rut" x-model="editingConductor.rut" @input="formatRut(editingConductor)" placeholder="Ej: 12.345.678-9" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- Cargo -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Cargo</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="cargo" :value="editingConductor.cargo" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- Departamento -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Departamento</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="text" name="departamento" :value="editingConductor.depto" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal">
                            </div>
                        </div>

                        <!-- Fecha Licencia -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-300 mb-1 group-focus-within:text-blue-400 transition-colors">Vencimiento Licencia</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                                <input type="date" name="fecha_licencia" :value="editingConductor.vencimiento" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm font-normal cursor-pointer [&::-webkit-calendar-picker-indicator]:invert">
                            </div>
                        </div>

                        <!-- Fotografía -->
                        <div class="md:col-span-2 space-y-4 pt-4 border-t border-slate-700/60">
                            <label class="block text-sm font-medium text-gray-300">Fotografía Perfil</label>

                            <!-- Preview Box -->
                            <div class="mb-3" x-show="(photoPreview || editingConductor.has_foto) && !editingConductor.remove_foto" style="display: none;">
                                <span
                                    class="block rounded-3xl w-full h-40 bg-cover bg-no-repeat bg-center mx-auto border border-slate-700 shadow-inner"
                                    x-bind:style="'background-image: url(\'' + (photoPreview || editingConductor.foto) + '\');'">
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" x-on:click.prevent="$refs.photoEdit.click()"
                                    :disabled="isCompressing"
                                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg border border-slate-700 transition-colors shadow-sm text-xs font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!isCompressing">Seleccionar Imagen</span>
                                    <span x-show="isCompressing">Procesando...</span>
                                </button>

                                <template x-if="editingConductor.has_foto && !editingConductor.remove_foto && !photoPreview">
                                    <button type="button" @click="editingConductor.remove_foto = true"
                                        class="px-4 py-2 bg-rose-950/20 hover:bg-rose-900/40 text-rose-400 rounded-lg border border-rose-500/20 transition-colors text-xs font-bold shadow-[0_0_15px_rgba(239,68,68,0.05)] cursor-pointer">
                                        Eliminar Foto
                                    </button>
                                </template>

                                <template x-if="editingConductor.remove_foto">
                                    <div class="flex items-center gap-3 bg-rose-500/5 px-4 py-2 rounded-lg border border-rose-500/10">
                                        <span class="text-[10px] text-rose-400 font-black uppercase tracking-wider">Foto marcada para eliminar</span>
                                        <button type="button" @click="editingConductor.remove_foto = false"
                                            class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-700 text-[10px] font-semibold">
                                            Deshacer
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <input id="edit-fotografia" name="fotografia" type="file" accept="image/*"
                                class="hidden" x-ref="photoEdit" x-on:change="
                                    const file = $refs.photoEdit.files[0];
                                    if (file) {
                                        editingConductor.remove_foto = false;
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
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-5 bg-slate-800/80 backdrop-blur-md border-t border-slate-700 flex items-center justify-end gap-3 sticky bottom-0 z-10">
                    <button type="button" @click="openEditModal = false; $dispatch('close-modal', 'edit-conductor-modal')" class="px-5 py-2.5 border border-slate-600 text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-800 hover:border-slate-500 transition-all cursor-pointer flex items-center justify-center h-10">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5 cursor-pointer flex items-center gap-2 justify-center h-10">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        {{ __('Guardar Cambios') }}
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Delete Confirmation Modal -->
        <x-modal name="confirm-delete-conductor-modal" :show="false" @close="openDeleteModal = false">
            <div class="p-0 bg-slate-800 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-700 shadow-2xl relative">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-rose-600/5 rounded-full blur-[80px] pointer-events-none"></div>

                <div class="p-10 text-center">
                    <div class="w-20 h-20 rounded-[2rem] bg-rose-500/10 text-rose-500 flex items-center justify-center border border-rose-500/20 shadow-inner mx-auto mb-8 animate-bounce-subtle">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-xl font-medium text-white mb-2">{{ __('¿Confirmar Eliminación?') }}</h3>
                    <p class="text-slate-400 text-sm font-normal max-w-xs mx-auto leading-relaxed">
                        {{ __('El conductor se moverá a la papelera. Podrás restaurarlo después si lo necesitas.') }}
                    </p>
                </div>

                <div class="px-8 py-6 bg-slate-900/50 backdrop-blur-md border-t border-slate-700 flex items-center justify-center gap-4">
                    <button type="button" @click="openDeleteModal = false; $dispatch('close-modal', 'confirm-delete-conductor-modal')" class="px-5 py-2.5 border border-slate-600 text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-800 hover:border-slate-500 transition-all cursor-pointer flex items-center justify-center h-10">
                        {{ __('Cancelar') }}
                    </button>
                    
                    <form method="POST" :action="deleteAction">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2.5 bg-transparent border border-red-500/30 dark:border-[#FF4433]/30 rounded-lg font-semibold text-xs text-red-600 dark:text-[#FF4433] uppercase tracking-wider hover:bg-red-50 dark:hover:bg-[#1D0002]/30 hover:border-red-500/50 transition-all duration-300 cursor-pointer flex items-center gap-2 justify-center h-10 shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" /></svg>
                            {{ __('Eliminar Conductor') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>
</x-app-layout>