<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                {{ __('Panel de Controlling') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-blue-500/20">Controlling</span>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Auditoría y Validación</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Toast Notification --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
                    class="fixed bottom-6 right-6 z-50 max-w-sm w-full bg-[#1e293b] border border-emerald-500/30 rounded-xl shadow-2xl shadow-emerald-500/10 p-4 flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/15 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white">¡Operación exitosa!</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 text-slate-500 hover:text-slate-300 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-8 bg-rose-500/5 border border-rose-500/20 rounded-3xl p-6 shadow-2xl">
                    <h3 class="text-sm font-black text-rose-400 flex items-center gap-2 mb-4 uppercase tracking-widest">
                        No se pudo completar la acción
                    </h3>

                    <ul class="space-y-2">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-rose-300 font-medium">
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $activeTab = request('tab', 'requerimientos');
            @endphp

            <!-- Tabs Navigation -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-px mb-8">
                <div class="flex gap-8">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'requerimientos', 'plannings_page' => 1]) }}"
                       class="pb-4 text-xs font-black uppercase tracking-widest relative transition-all duration-300 cursor-pointer flex items-center gap-2 group {{ $activeTab === 'requerimientos' ? 'text-blue-400' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-4 h-4 {{ $activeTab === 'requerimientos' ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                        </svg>
                        <span>Auditoría de Requerimientos</span>
                        @if($activeTab === 'requerimientos')
                            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-blue-500 rounded-full shadow-[0_0_8px_rgba(59,130,246,0.5)]"></div>
                        @endif
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'rendiciones', 'renditions_page' => 1]) }}"
                       class="pb-4 text-xs font-black uppercase tracking-widest relative transition-all duration-300 cursor-pointer flex items-center gap-2 group {{ $activeTab === 'rendiciones' ? 'text-purple-400' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-4 h-4 {{ $activeTab === 'rendiciones' ? 'text-purple-400' : 'text-slate-500 group-hover:text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span>Auditoría de Rendiciones</span>
                        @if($activeTab === 'rendiciones')
                            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-purple-500 rounded-full shadow-[0_0_8px_rgba(168,85,247,0.5)]"></div>
                        @endif
                    </a>
                </div>
            </div>

            @if($activeTab === 'requerimientos')
            {{-- ════════════════════════════════════════════ --}}
            {{-- SECCIÓN 1: AUDITORÍA DE REQUERIMIENTOS      --}}
            {{-- ════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                
                {{-- Header --}}
                <div class="p-6 border-b border-slate-700/40 flex items-center gap-4">
                    <div class="p-2.5 bg-blue-500/10 text-blue-400 rounded-xl ring-1 ring-blue-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Auditoría de Requerimientos</h3>
                        <p class="text-sm text-slate-400">Revisa y valida las planificaciones de viaje antes de pasarlas a Finanzas.</p>
                    </div>
                    @if(!$plannings->isEmpty())
                        <div class="ml-auto">
                            <span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20">{{ $plannings->total() }} pendiente{{ $plannings->total() !== 1 ? 's' : '' }}</span>
                        </div>
                    @endif
                </div>

                @if($plannings->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Bandeja Vacía</h3>
                        <p class="mt-1 text-sm text-slate-400 max-w-sm mx-auto">No hay solicitudes pendientes de validación en Controlling.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">ID / Solicitante</th>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Destino y Motivo</th>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Fondos Requeridos</th>
                                    <th scope="col" class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Validación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach ($plannings as $plan)
                                    <tr class="hover:bg-slate-800/60 transition-colors duration-200 group" x-data="{ showReject: false }">
                                        
                                        {{-- ID y Usuario --}}
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-2">
                                                <span class="font-mono text-[12px] text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded-md ring-1 ring-blue-500/20 self-start">REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                <div class="flex items-center gap-3">
                                                    <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $plan->user->profile_photo_path ? asset('storage/' . $plan->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($plan->user->name . ' ' . $plan->user->last_name) . '&color=93C5FD&background=1e293b&bold=true&size=64' }}" alt="{{ $plan->user->name }} {{ $plan->user->last_name }}">
                                                    <div>
                                                        <div class="text-sm font-semibold text-white">{{ $plan->user->name }} {{ $plan->user->last_name }}</div>
                                                        <div class="text-[11px] text-slate-500">{{ $plan->user->departamento ?? 'Sin departamento' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Detalles Viaje --}}
                                        <td class="px-6 py-5">
                                            <div class="text-sm font-semibold text-white">{{ $plan->motive }}</div>
                                            <div class="text-xs text-slate-400 mt-0.5 line-clamp-1" title="{{ $plan->destination }}">{{ $plan->destination }}</div>
                                            @if($plan->region)
                                                <div class="flex items-center gap-1 text-[11px] text-slate-500 mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    {{ $plan->region }}
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-1.5 text-[11px] text-blue-400 mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ \Carbon\Carbon::parse($plan->start_date)->format('d M') }} al {{ \Carbon\Carbon::parse($plan->end_date)->format('d M') }}
                                            </div>
                                        </td>

                                        {{-- Fondos Requeridos --}}
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="flex flex-col gap-1.5">
                                                @if($plan->requires_funds)
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-md bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20 self-start">
                                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        ${{ number_format($plan->requested_funds, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-md bg-slate-700/50 text-slate-500 ring-1 ring-slate-600/30 self-start">No aplica</span>
                                                @endif
                                                @if($plan->requires_amipass)
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-md bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 self-start">
                                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                        Amipass: {{ $plan->amipass_days }} días
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        @php
                                            $isOwnPlanning = $plan->user_id === auth()->id() && auth()->user()->email !== 'test@example.com';
                                        @endphp

                                        {{-- Acciones --}}
                                        <td class="px-6 py-5 whitespace-nowrap text-center">
                                            @if($isOwnPlanning)
                                                <div class="inline-flex flex-col items-center gap-2 px-5 py-4 rounded-2xl bg-slate-800/60 border border-slate-700/70">
                                                    <span class="text-[10px] text-amber-400 font-black uppercase tracking-widest">
                                                        Gestión bloqueada
                                                    </span>
                                                    <span class="text-[11px] text-slate-500 font-bold max-w-[190px] leading-relaxed">
                                                        No puedes validar o rechazar tu propia planificación.
                                                    </span>
                                                </div>
                                            @else
                                                <div class="flex items-center justify-center gap-2" x-show="!showReject">
                                                    <form action="{{ route('route-plannings.approve-controlling', $plan->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-500 transition-all hover:-translate-y-0.5" title="Validar y Escalar a Finanzas">
                                                            <span class="flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                                Validar
                                                            </span>
                                                        </button>
                                                    </form>

                                                    <button @click="showReject = true" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer" title="Rechazar Documento">
                                                        <span class="flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            Rechazar
                                                        </span>
                                                    </button>
                                                </div>

                                                {{-- Reject Form --}}
                                                <div x-show="showReject" x-cloak x-transition class="text-left bg-slate-800 p-4 rounded-xl border border-slate-700 mt-2" style="min-width: 280px;">
                                                    <form action="{{ route('route-plannings.reject-controlling', $plan->id) }}" method="POST">
                                                        @csrf
                                                        <label class="block text-xs font-semibold text-rose-400 mb-1.5">Motivo del rechazo:</label>
                                                        <textarea name="observation" rows="2" class="w-full text-sm bg-slate-900 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-500 focus:border-rose-500 focus:ring-0 px-3 py-2" required placeholder="Ej: Faltan especificaciones..."></textarea>
                                                        <div class="mt-3 flex justify-end gap-2">
                                                            <button type="button" @click="showReject = false" class="px-3 py-1.5 text-xs text-slate-400 hover:text-slate-200 transition-colors">Cancelar</button>
                                                            <button type="submit" class="px-4 py-1.5 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5">Devolver</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-slate-700/40">
                        {{ $plannings->appends(['tab' => 'requerimientos'])->links() }}
                    </div>
                @endif

            </div>
            @endif

            @if($activeTab === 'rendiciones')
            {{-- ════════════════════════════════════════════ --}}
            {{-- SECCIÓN 2: AUDITORÍA DE RENDICIONES          --}}
            {{-- ════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                
                {{-- Header --}}
                <div class="p-6 border-b border-slate-700/40 flex items-center gap-4">
                    <div class="p-2.5 bg-purple-500/10 text-purple-400 rounded-xl ring-1 ring-purple-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Auditoría de Rendiciones</h3>
                        <p class="text-sm text-slate-400">Verifica que las boletas coincidan con los fondos entregados.</p>
                    </div>
                </div>

                @if(isset($renditions) && $renditions->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Sin rendiciones</h3>
                        <p class="mt-1 text-sm text-slate-400">No hay rendiciones pendientes de auditoría.</p>
                    </div>
                @elseif(isset($renditions))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Colaborador / Viaje</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Entregado vs Rendido</th>
                                    <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Auditoría</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach($renditions as $ren)
                                <tr x-data="{ showReject: false }" class="hover:bg-slate-800/60 transition-colors duration-200">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $ren->user->profile_photo_path ? asset('storage/' . $ren->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($ren->user->name . ' ' . $ren->user->last_name) . '&color=C4B5FD&background=1e293b&bold=true&size=64' }}" alt="{{ $ren->user->name }} {{ $ren->user->last_name }}">
                                            <div>
                                                <div class="text-sm font-semibold text-white">{{ $ren->user->name }} {{ $ren->user->last_name }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $ren->routePlanning->destination }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-xs text-slate-500">Entregado: <span class="text-slate-300 font-medium">${{ number_format($ren->funds_received, 0, ',', '.') }}</span></div>
                                            <div class="text-sm font-bold text-purple-400">Rendido: ${{ number_format($ren->total_declared, 0, ',', '.') }}</div>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <span class="px-2 py-1 rounded-lg bg-slate-700/40 text-slate-300 text-[9px] font-black uppercase tracking-widest border border-slate-600/40">
                                                    {{ $ren->total_expenses_count }} docs
                                                </span>

                                                @if($ren->observed_expenses_count > 0)
                                                    <span class="px-2 py-1 rounded-lg bg-rose-500/10 text-rose-400 text-[9px] font-black uppercase tracking-widest border border-rose-500/20">
                                                        {{ $ren->observed_expenses_count }} observado(s)
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase tracking-widest border border-emerald-500/20">
                                                        Sin observados
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    @php
                                        $isOwnRendition = $ren->user_id === auth()->id() && auth()->user()->email !== 'test@example.com';
                                    @endphp
                                    <td class="px-6 py-5 text-center">
                                        <div x-show="!showReject" class="flex justify-center gap-2">
                                            <a href="{{ route('renditions.show', $ren->id) }}" target="_blank" class="px-4 py-2 border border-slate-600 text-slate-300 text-xs font-semibold rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                                                <span class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    Auditar
                                                </span>
                                            </a>

                                            @if($isOwnRendition)
                                                <div class="px-4 py-2.5 rounded-xl bg-slate-800/70 border border-slate-700 text-left max-w-[190px]">
                                                    <div class="text-[9px] text-amber-400 font-black uppercase tracking-widest">
                                                        Gestión bloqueada
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 font-bold leading-relaxed mt-1">
                                                        No puedes validar, rechazar u observar documentos de tu propia rendición.
                                                    </div>
                                                </div>
                                            @else
                                                <form action="{{ route('renditions.approve-controlling-rendition', $ren->id) }}" method="POST">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        @disabled($ren->observed_expenses_count > 0)
                                                        title="{{ $ren->observed_expenses_count > 0 ? 'No se puede validar mientras existan documentos observados.' : 'Validar rendición y enviar a Finanzas.' }}"
                                                        class="px-4 py-2 text-xs font-semibold rounded-lg transition-all hover:-translate-y-0.5 cursor-pointer
                                                            {{ $ren->observed_expenses_count > 0
                                                                ? 'bg-slate-700 text-slate-400 cursor-not-allowed opacity-60'
                                                                : 'bg-blue-600 text-white hover:bg-blue-500'
                                                            }}"
                                                    >
                                                        Validar
                                                    </button>
                                                </form>

                                                <button @click="showReject = true" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">Rechazar</button>
                                            @endif
                                        </div>

                                        @if(!$isOwnRendition)
                                            <div x-show="showReject" x-cloak x-transition class="text-left bg-slate-800 p-4 rounded-xl border border-slate-700 mt-2">
                                                <form action="{{ route('renditions.reject-controlling-rendition', $ren->id) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-xs font-semibold text-rose-400 mb-1.5">Motivo del rechazo:</label>
                                                    <textarea name="observation" class="w-full text-sm bg-slate-900 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-500 focus:border-rose-500 focus:ring-0 px-3 py-2" required placeholder="Motivo..."></textarea>
                                                    <div class="mt-3 flex justify-end gap-2">
                                                        <button type="button" @click="showReject = false" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white transition-colors cursor-pointer">Cancelar</button>
                                                        <button type="submit" class="px-4 py-1.5 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">Devolver</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($renditions->hasPages())
                        <div class="px-6 py-4 border-t border-slate-700/40">
                            {{ $renditions->appends(['tab' => 'rendiciones'])->links() }}
                        </div>
                    @endif
                @endif
            </div>
            @endif

        </div>
    </div>

</x-app-layout>
