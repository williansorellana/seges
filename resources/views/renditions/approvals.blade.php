<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                {{ __('Aprobaciones de Jefatura') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-yellow-500/10 text-yellow-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-yellow-500/20">Jefatura</span>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Aprobación de Solicitudes y Gastos</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Toast Notification --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="fixed bottom-10 right-10 z-50 max-w-sm w-full bg-slate-900 border border-emerald-500/30 rounded-3xl shadow-2xl shadow-emerald-500/10 p-5 flex items-start gap-4 backdrop-blur-xl">
                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-white uppercase tracking-wider">¡Operación exitosa!</p>
                        <p class="text-[11px] text-slate-400 mt-1 font-medium leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-600 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @php
                $activeTab = request('tab', 'solicitudes');
            @endphp

            <!-- Tabs Navigation -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-px mb-8">
                <div class="flex gap-8">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'solicitudes', 'plannings_page' => 1]) }}"
                       class="pb-4 text-xs font-black uppercase tracking-widest relative transition-all duration-300 cursor-pointer flex items-center gap-2 group {{ $activeTab === 'solicitudes' ? 'text-yellow-400' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-4 h-4 {{ $activeTab === 'solicitudes' ? 'text-yellow-400' : 'text-slate-500 group-hover:text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.53a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                        <span>Panel de Aprobación</span>
                        @if($activeTab === 'solicitudes')
                            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-yellow-500 rounded-full shadow-[0_0_8px_rgba(234,179,8,0.5)]"></div>
                        @endif
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'rendiciones', 'renditions_page' => 1]) }}"
                       class="pb-4 text-xs font-black uppercase tracking-widest relative transition-all duration-300 cursor-pointer flex items-center gap-2 group {{ $activeTab === 'rendiciones' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-4 h-4 {{ $activeTab === 'rendiciones' ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span>Rendiciones de Gastos</span>
                        @if($activeTab === 'rendiciones')
                            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-indigo-500 rounded-full shadow-[0_0_8px_rgba(99,102,241,0.5)]"></div>
                        @endif
                    </a>
                </div>
            </div>

            @if($activeTab === 'solicitudes')
            <!-- 1. PANEL DE APROBACIÓN (SOLICITUDES) -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                
                <div class="p-6 border-b border-slate-700/40 flex items-center gap-4">
                    <div class="p-2.5 bg-yellow-500/10 text-yellow-400 rounded-xl ring-1 ring-yellow-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.53a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Panel de Aprobación</h3>
                        <p class="text-sm text-slate-400">Revisa las solicitudes de fondos y amipass de tu equipo.</p>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V8.25H8.25m0 0h0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Todo al día</h3>
                        <p class="mt-1 text-sm text-slate-400">No tienes solicitudes pendientes de aprobación en este momento.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Colaborador</th>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Viaje</th>
                                    <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Solicitado</th>
                                    <th scope="col" class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach ($plannings as $plan)
                                    <tr class="hover:bg-slate-800/60 transition-colors duration-200" x-data="{ showReject: false }">
                                        
                                        <!-- Usuario -->
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $plan->user->profile_photo_path ? asset('storage/' . $plan->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($plan->user->name . ' ' . $plan->user->last_name) . '&color=FBBF24&background=1e293b&bold=true&size=64' }}" alt="{{ $plan->user->name }} {{ $plan->user->last_name }}">
                                                <div>
                                                    <div class="text-sm font-semibold text-white">{{ $plan->user->name }} {{ $plan->user->last_name }}</div>
                                                    <div class="text-[11px] text-slate-500">{{ $plan->user->departamento ?? 'Sin departamento' }}</div>
                                                </div>
                                            </div>
                                        </td>
 
                                        <!-- Detalles Viaje -->
                                        <td class="px-6 py-5">
                                            <div class="text-sm font-semibold text-white mb-1 tracking-tight truncate max-w-xs">
                                                {{ $plan->destination }} 
                                                <span class="text-xs text-slate-500 font-normal">({{ ucfirst($plan->trip_type) }})</span>
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5 line-clamp-1" title="{{ $plan->motive }}">{{ $plan->motive }}</div>
                                            <div class="flex items-center gap-1.5 text-[11px] text-yellow-400 mt-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($plan->end_date)->format('d/m/Y') }}
                                            </div>
                                                                               <td class="px-6 py-5 whitespace-nowrap">
                                            @php
                                                $requestedFunds = $plan->requested_funds ?? 0;
                                                $amipassAmount = $plan->amipass_amount ?? 0;
                                                $totalRequested = $requestedFunds + $amipassAmount;
                                            @endphp

                                            <div class="flex flex-col gap-1.5">
                                                <span class="px-2.5 py-1 inline-flex items-center text-xs font-black rounded-md bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 self-start">
                                                    Total: ${{ number_format($totalRequested, 0, ',', '.') }}
                                                </span>

                                                @if($plan->requires_funds)
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-md bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20 self-start">
                                                        Fondos: ${{ number_format($requestedFunds, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-md bg-slate-700/50 text-slate-500 ring-1 ring-slate-600/30 self-start">Sin Fondos</span>
                                                @endif

                                                @if($plan->requires_amipass)
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-md bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20 self-start">
                                                        Amipass: ${{ number_format($amipassAmount, 0, ',', '.') }} / {{ $plan->amipass_business_days ?? $plan->amipass_days }} día(s)
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex items-center justify-center gap-3 relative">
                                                <div x-show="!showReject" class="flex gap-2 transition-all">
                                                    <a href="{{ route('route-plannings.pdf', $plan->id) }}"
                                                    target="_blank"
                                                    class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 inline-flex items-center gap-1.5 cursor-pointer"
                                                    title="Descargar PDF">
                                                        PDF
                                                    </a>

                                                    <form action="{{ route('route-plannings.approve-jefatura', $plan->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                            Aprobar
                                                        </button>
                                                    </form>
                                                    
                                                    <button @click="showReject = true" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                        Rechazar
                                                    </button>
                                                </div>

                                                <div x-show="showReject" x-cloak class="absolute right-0 top-0 w-72 bg-slate-800 p-4 rounded-xl border border-slate-700 shadow-2xl z-20" x-transition>
                                                    <form action="{{ route('route-plannings.reject-jefatura', $plan->id) }}" method="POST">
                                                        @csrf
                                                        <label class="block text-xs font-bold text-rose-400 mb-2 uppercase tracking-wider text-left">Motivo del Rechazo</label>
                                                        <textarea name="observation" rows="3" class="w-full text-xs bg-slate-900 border-slate-700 text-slate-100 rounded-lg focus:ring-rose-500 focus:border-rose-500 placeholder-slate-500" required placeholder="Especifique el motivo..."></textarea>
                                                        <div class="mt-4 flex justify-end gap-2">
                                                            <button type="button" @click="showReject = false" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white transition-colors cursor-pointer">Cancelar</button>
                                                            <button type="submit" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">Confirmar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>      </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-slate-700/40 bg-slate-950/20">
                        {{ $plannings->appends(['tab' => 'solicitudes'])->links() }}
                    </div>
                @endif
            </div>
            @endif

            @if($activeTab === 'rendiciones')
            <!-- 2. RENDICIONES DE GASTOS (BOLETAS) -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                <div class="p-6 border-b border-slate-700/40 flex items-center gap-4">
                    <div class="p-2.5 bg-indigo-500/10 text-indigo-400 rounded-xl ring-1 ring-indigo-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Rendiciones de Gastos (Boletas)</h3>
                        <p class="text-sm text-slate-400">Revisa las justificaciones de gastos de tu equipo.</p>
                    </div>
                </div>

                @if(isset($renditions) && $renditions->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V8.25H8.25m0 0h0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Sin rendiciones pendientes</h3>
                        <p class="mt-1 text-sm text-slate-400">No hay rendiciones de boletas pendientes.</p>
                    </div>
                @elseif(isset($renditions))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Monto Rendido</th>
                                    <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach($renditions as $ren)
                                <tr class="hover:bg-slate-800/60 transition-colors duration-200" x-data="{ showReject: false }">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $ren->user->profile_photo_path ? asset('storage/' . $ren->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($ren->user->name . ' ' . $ren->user->last_name) . '&color=818CF8&background=1e293b&bold=true&size=64' }}" alt="{{ $ren->user->name }}">
                                            <div>
                                                <div class="text-sm font-semibold text-white">{{ $ren->user->name }} {{ $ren->user->last_name }}</div>
                                                <div class="text-[11px] text-slate-500">Destino: {{ $ren->routePlanning->destination }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="px-2 py-0.5 inline-flex items-center text-[11px] font-semibold rounded-md bg-slate-800 text-slate-400 ring-1 ring-slate-700/50 self-start">
                                                Asignado: ${{ number_format($ren->funds_received, 0, ',', '.') }}
                                            </span>
                                            <span class="text-sm font-black text-indigo-400">
                                                Rendido: ${{ number_format($ren->total_declared, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-3 relative">
                                            <div x-show="!showReject" class="flex gap-2 transition-all">
                                                <a href="{{ route('renditions.show', $ren->id) }}" target="_blank"
                                                    class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-500 transition-all hover:-translate-y-0.5 inline-flex items-center gap-1.5 cursor-pointer">
                                                    Ver Boletas
                                                </a>
                                                <a href="{{ route('renditions.pdf', $ren->id) }}" target="_blank"
                                                    class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 inline-flex items-center gap-1.5 cursor-pointer">
                                                    PDF
                                                </a>
                                                <form action="{{ route('renditions.approve-jefatura-rendition', $ren->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                        Aprobar
                                                    </button>
                                                </form>
                                                <button @click="showReject = true" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                    Rechazar
                                                </button>
                                            </div>

                                            <div x-show="showReject" x-cloak class="absolute right-0 top-0 w-72 bg-slate-800 p-4 rounded-xl border border-slate-700 shadow-2xl z-20" x-transition>
                                                <form action="{{ route('renditions.reject-jefatura-rendition', $ren->id) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-xs font-bold text-rose-400 mb-2 uppercase tracking-wider text-left">Motivo del Rechazo</label>
                                                    <textarea name="observation" rows="3" class="w-full text-xs bg-slate-900 border-slate-700 text-slate-100 rounded-lg focus:ring-rose-500 focus:border-rose-500 placeholder-slate-500" required placeholder="Especifique el motivo..."></textarea>
                                                    <div class="mt-4 flex justify-end gap-2">
                                                        <button type="button" @click="showReject = false" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white transition-colors cursor-pointer">Cancelar</button>
                                                        <button type="submit" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">Confirmar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($renditions->hasPages())
                        <div class="px-6 py-4 border-t border-slate-700/40 bg-slate-950/20">
                            {{ $renditions->appends(['tab' => 'rendiciones'])->links() }}
                        </div>
                    @endif
                @endif
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
