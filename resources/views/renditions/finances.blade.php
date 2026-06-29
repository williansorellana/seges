<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                {{ __('Panel de Gestión Financiera') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-emerald-500/20">Tesorería</span>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Control de Fondos y Cierres</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
            
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
                $activeTab = request('tab', 'tesoreria');
            @endphp

            <!-- Tabs Navigation -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-px mb-8">
                <div class="flex gap-8">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'tesoreria', 'plannings_page' => 1]) }}"
                       class="pb-4 text-xs font-black uppercase tracking-widest relative transition-all duration-300 cursor-pointer flex items-center gap-2 group {{ $activeTab === 'tesoreria' ? 'text-emerald-400' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-4 h-4 {{ $activeTab === 'tesoreria' ? 'text-emerald-400' : 'text-slate-500 group-hover:text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Tesorería y Fondos</span>
                        @if($activeTab === 'tesoreria')
                            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        @endif
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'cierre', 'renditions_page' => 1]) }}"
                       class="pb-4 text-xs font-black uppercase tracking-widest relative transition-all duration-300 cursor-pointer flex items-center gap-2 group {{ $activeTab === 'cierre' ? 'text-indigo-400' : 'text-slate-400 hover:text-slate-200' }}">
                        <svg class="w-4 h-4 {{ $activeTab === 'cierre' ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span>Cierre Contable</span>
                        @if($activeTab === 'cierre')
                            <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-indigo-500 rounded-full shadow-[0_0_8px_rgba(99,102,241,0.5)]"></div>
                        @endif
                    </a>
                </div>
            </div>

            @if($activeTab === 'tesoreria')
            <!-- 1. TESORERÍA Y FONDOS -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                
                <div class="p-6 border-b border-slate-700/40 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-2.5 bg-emerald-500/10 text-emerald-400 rounded-xl ring-1 ring-emerald-500/20">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-tight">Tesorería y Fondos</h3>
                            <p class="text-sm text-slate-400">Autorización y liberación de viáticos.</p>
                        </div>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg class="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Sin salidas pendientes</h3>
                        <p class="mt-1 text-sm text-slate-400">No hay requerimientos financieros por autorizar.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">ID y Solicitante</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Motivo / Destino</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Total a Liberar</th>
                                    <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Gestión</th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-slate-700/40"
                                x-data="{ activePlanning: null }"
                            >
                                @foreach ($plannings as $plan)
                                    <tr
                                        class="hover:bg-slate-800/60 transition-colors duration-200 group cursor-pointer"
                                        x-data="{ showReject: false }"
                                        @click="if(!$event.target.closest('form') && !$event.target.closest('button') && !$event.target.closest('textarea')) activePlanning = activePlanning === {{ $plan->id }} ? null : {{ $plan->id }}"
                                    >    
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col gap-1.5">
                                                <span class="font-mono text-[12px] text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded-md ring-1 ring-blue-500/20 self-start mt-1">REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                <div class="flex items-center gap-3 mt-1.5">
                                                    <div class="relative">
                                                        <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $plan->user->profile_photo_path ? asset('storage/' . $plan->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($plan->user->name . ' ' . $plan->user->last_name) . '&color=93C5FD&background=1e293b&bold=true&size=64' }}" alt="{{ $plan->user->name }}">
                                                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border border-slate-800 flex items-center justify-center">
                                                            <svg class="w-2 h-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-semibold text-white">{{ $plan->user->name }} {{ $plan->user->last_name }}</div>
                                                        @php
                                                            if ($plan->status === 'pending_finances') {
                                                                $approvalLabel = $plan->trip_type === 'reunion' ? 'Visado por Jefatura' : 'Visado por Controlling';
                                                            } elseif ($plan->status === 'pending_controlling') {
                                                                $approvalLabel = 'Visado por Jefatura';
                                                            } elseif ($plan->status === 'approved') {
                                                                $approvalLabel = 'Aprobado';
                                                            } elseif ($plan->status === 'rejected') {
                                                                $approvalLabel = 'Rechazado';
                                                            } else {
                                                                $approvalLabel = 'En revisión';
                                                            }
                                                        @endphp
                                                        <div class="text-[9px] text-emerald-400 font-bold uppercase tracking-widest mt-0.5">{{ $approvalLabel }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <div class="text-sm font-semibold text-white mb-1 tracking-tight truncate max-w-xs">{{ $plan->motive }}</div>
                                            <div class="text-xs text-slate-400 mt-0.5 line-clamp-1" title="{{ $plan->destination }}">{{ $plan->destination }}</div>
                                            <div class="flex items-center gap-1.5 text-[11px] text-blue-400 mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($plan->end_date)->format('d/m/Y') }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @php
                                                $requestedFunds = $plan->requested_funds ?? 0;
                                                $amipassAmount = $plan->amipass_amount ?? 0;
                                                $totalToRelease = $requestedFunds + $amipassAmount;
                                            @endphp

                                            <div class="flex flex-col gap-1.5">
                                                <div class="text-sm font-black text-emerald-400">
                                                    Total: ${{ number_format($totalToRelease, 0, ',', '.') }}
                                                </div>

                                                @if($plan->requires_funds)
                                                    <div class="text-[11px] text-slate-500">
                                                        Fondos:
                                                        <span class="text-slate-300 font-bold">
                                                            ${{ number_format($requestedFunds, 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="text-[11px] text-slate-600 font-bold">
                                                        Sin Fondos
                                                    </div>
                                                @endif

                                                @if($plan->requires_amipass)
                                                    <div class="text-[11px] text-slate-500">
                                                        Amipass:
                                                        <span class="text-emerald-400 font-bold">
                                                            ${{ number_format($amipassAmount, 0, ',', '.') }}
                                                        </span>
                                                    </div>

                                                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                                        {{ $plan->amipass_business_days ?? $plan->amipass_days }} día(s)
                                                    </div>
                                                @else
                                                    <div class="text-[11px] text-slate-600 font-bold">
                                                        Sin Amipass
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        @php
                                            $isOwnPlanning = $plan->user_id === auth()->id() && auth()->user()->email !== 'test@example.com';
                                        @endphp

                                        <td class="px-6 py-5 text-center">
                                            @if($isOwnPlanning)
                                                <div class="inline-flex flex-col items-center gap-2 px-5 py-4 rounded-2xl bg-slate-800/60 border border-slate-700/70">
                                                    <span class="text-[10px] text-amber-400 font-black uppercase tracking-widest">
                                                        Gestión bloqueada
                                                    </span>
                                                    <span class="text-[11px] text-slate-500 font-bold max-w-[190px] leading-relaxed">
                                                        No puedes liberar o rechazar tu propia planificación.
                                                    </span>
                                                </div>
                                            @else
                                                <div class="flex items-center justify-center gap-3 relative">
                                                    <div x-show="!showReject" class="flex gap-3 transition-all">
                                                        <form action="{{ route('route-plannings.approve-finances', $plan->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg shadow-md shadow-emerald-500/20 hover:bg-emerald-500 hover:-translate-y-0.5 transition-all cursor-pointer">
                                                                Liberar Fondos
                                                            </button>
                                                        </form>
                                                        
                                                        <button @click="showReject = true" class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg shadow-md shadow-rose-500/20 hover:bg-rose-500 hover:-translate-y-0.5 transition-all cursor-pointer">
                                                            Rechazar
                                                        </button>
                                                    </div>

                                                    <div x-show="showReject" x-cloak class="absolute right-0 top-0 w-72 bg-slate-900 p-5 rounded-[1.5rem] border border-rose-500/30 shadow-2xl z-20" x-transition>
                                                        <form action="{{ route('route-plannings.reject-finances', $plan->id) }}" method="POST">
                                                            @csrf
                                                            <label class="block text-[10px] font-black text-rose-500 mb-3 uppercase tracking-[0.2em] text-left">Motivo de Rechazo</label>
                                                            <textarea name="observation" rows="3" class="w-full text-xs bg-slate-950 border-slate-800 text-white rounded-xl focus:ring-rose-500 focus:border-rose-500 placeholder-slate-700 font-bold" required placeholder="Especifique el motivo..."></textarea>
                                                            <div class="mt-4 flex justify-end gap-3">
                                                                <button type="button" @click="showReject = false" class="px-4 py-2 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-white transition-colors cursor-pointer">Cerrar</button>
                                                                <button type="submit" class="px-5 py-2 bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-rose-600/20 hover:bg-rose-500 hover:-translate-y-0.5 transition-all cursor-pointer">Confirmar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr
                                        x-show="activePlanning === {{ $plan->id }}"
                                        x-cloak
                                        x-transition
                                        class="bg-slate-900/40"
                                    >
                                        <td colspan="4" class="px-8 pt-6 pb-10 border-b border-slate-700/40">

                                            <!-- Aplicamos el grid de 2 columnas -->
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                                <!-- Columna Izquierda: Destinos y Amipass -->
                                                <div class="flex flex-col gap-6">
                                                    
                                                    <!-- Tarjeta Destinos -->
                                                    <div class="flex flex-col h-full">
                                                        <h5 class="text-xs font-black text-white uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                            Destinos del Viaje
                                                        </h5>

                                                        <div class="bg-slate-900/60 p-5 rounded-2xl border border-slate-800 flex-grow">

                                                            <div class="text-sm font-bold text-white mb-2">
                                                                Destino Principal:
                                                                {{ $plan->destination }}
                                                                @if($plan->region)
                                                                    <span class="text-slate-400 font-normal">({{ $plan->region }})</span>
                                                                @endif
                                                            </div>

                                                            @if(!empty($plan->destinations))
                                                                <div class="mt-4 pt-3 border-t border-slate-800/80">
                                                                    <div class="text-[10px] text-slate-500 font-black uppercase tracking-wider mb-2">
                                                                        Destinos Adicionales
                                                                    </div>
                                                                    <ul class="space-y-1.5">
                                                                        @foreach($plan->destinations as $dest)
                                                                            @if(!empty($dest['destination']))
                                                                                <li class="text-xs text-slate-300">
                                                                                    • {{ $dest['destination'] }}
                                                                                    @if(!empty($dest['region']))
                                                                                        <span class="text-slate-500">({{ $dest['region'] }})</span>
                                                                                    @endif
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @else
                                                                <p class="text-xs text-slate-500 italic mt-2">
                                                                    Sin destinos adicionales.
                                                                </p>
                                                            @endif

                                                        </div>
                                                    </div>

                                                    <!-- Tarjeta Amipass -->
                                                    @if($plan->requires_amipass)
                                                    <div>
                                                        <h5 class="text-xs font-black text-white uppercase tracking-wider mb-2">
                                                            Amipass
                                                        </h5>

                                                        <div class="bg-slate-900/60 p-5 rounded-2xl border border-slate-800">
                                                            <div class="space-y-2 text-sm">
                                                                <div class="flex justify-between">
                                                                    <span class="text-slate-500">Monto</span>
                                                                    <span class="font-bold text-emerald-400">
                                                                        ${{ number_format($plan->amipass_amount,0,',','.') }}
                                                                    </span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-slate-500">Días</span>
                                                                    <span class="font-bold text-white">
                                                                        {{ $plan->amipass_business_days ?? $plan->amipass_days }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                </div>

                                                <!-- Columna Derecha: Desglose -->
                                                <div class="flex flex-col h-full">
                                                    @if($plan->requires_funds)
                                                    <div class="flex flex-col h-full">
                                                        <h5 class="text-xs font-black text-white uppercase tracking-wider mb-2">
                                                            Desglose de Presupuesto
                                                        </h5>

                                                        <div class="bg-slate-900/60 p-5 rounded-2xl border border-slate-800 flex-grow">

                                                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">

                                                                <div class="bg-slate-950/40 p-3 rounded-xl border border-slate-800 text-center flex flex-col justify-center">
                                                                    <div class="text-[9px] text-slate-500 uppercase font-black mb-1">Bencina</div>
                                                                    <div class="text-sm font-bold text-white">
                                                                        ${{ number_format($plan->funds_bencina,0,',','.') }}
                                                                    </div>
                                                                </div>

                                                                <div class="bg-slate-950/40 p-3 rounded-xl border border-slate-800 text-center flex flex-col justify-center">
                                                                    <div class="text-[9px] text-slate-500 uppercase font-black mb-1">Peajes</div>
                                                                    <div class="text-sm font-bold text-white">
                                                                        ${{ number_format($plan->funds_peaje,0,',','.') }}
                                                                    </div>
                                                                </div>

                                                                <div class="bg-slate-950/40 p-3 rounded-xl border border-slate-800 text-center flex flex-col justify-center">
                                                                    <div class="text-[9px] text-slate-500 uppercase font-black mb-1">Alojamiento</div>
                                                                    <div class="text-sm font-bold text-white">
                                                                        ${{ number_format($plan->funds_alojamiento,0,',','.') }}
                                                                    </div>
                                                                </div>

                                                                <div class="bg-slate-950/40 p-3 rounded-xl border border-slate-800 text-center flex flex-col justify-center">
                                                                    <div class="text-[9px] text-slate-500 uppercase font-black mb-1">Alimentación</div>
                                                                    <div class="text-sm font-bold text-white">
                                                                        ${{ number_format($plan->funds_alimentacion,0,',','.') }}
                                                                    </div>
                                                                </div>

                                                                <div class="bg-slate-950/40 p-3 rounded-xl border border-slate-800 text-center flex flex-col justify-center col-span-2 sm:col-span-1">
                                                                    <div class="text-[9px] text-slate-500 uppercase font-black mb-1">Otros</div>
                                                                    <div class="text-sm font-bold text-white">
                                                                        ${{ number_format($plan->funds_otros,0,',','.') }}
                                                                    </div>
                                                                </div>

                                                            </div>

                                                            @if($plan->funds_description)
                                                                <div class="pt-3 mt-4 border-t border-slate-800">
                                                                    <div class="text-[10px] text-slate-500 uppercase font-black mb-1">
                                                                        Justificación
                                                                    </div>
                                                                    <p class="text-xs text-slate-300">
                                                                        {{ $plan->funds_description }}
                                                                    </p>
                                                                </div>
                                                            @endif

                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>

                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($plannings->hasPages())
                        <div class="px-8 py-6 border-t border-slate-800 bg-slate-950/20">
                            {{ $plannings->appends(['tab' => 'tesoreria'])->links() }}
                        </div>
                    @endif
                @endif
            </div>
            @endif

            @if($activeTab === 'cierre')
            <!-- 2. CIERRE CONTABLE RENDICIONES -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                
                <div class="p-6 border-b border-slate-700/40 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-2.5 bg-indigo-500/10 text-indigo-400 rounded-xl ring-1 ring-indigo-500/20">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-tight">Cierre Contable de Rendiciones</h3>
                            <p class="text-sm text-slate-400">Conciliación final de gastos rendidos.</p>
                        </div>
                    </div>
                </div>

                @if(isset($renditions) && $renditions->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg class="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Sin cierres pendientes</h3>
                        <p class="mt-1 text-sm text-slate-400">No hay rendiciones pendientes de conciliación final.</p>
                    </div>
                @elseif(isset($renditions))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">ID y Colaborador</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Destino / Motivo</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Conciliación Bancaria</th>
                                    <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Acción Final</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach($renditions as $ren)
                                <tr x-data="{ showReject: false }" class="hover:bg-slate-800/60 transition-colors duration-200">
                                    
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="font-mono text-[12px] text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded-md ring-1 ring-indigo-500/20 self-start mt-1">RND-{{ str_pad($ren->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            <div class="flex items-center gap-3 mt-1.5">
                                                <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $ren->user->profile_photo_path ? asset('storage/' . $ren->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($ren->user->name . ' ' . $ren->user->last_name) . '&color=818CF8&background=1e293b&bold=true&size=64' }}" alt="{{ $ren->user->name }}">
                                                <div>
                                                    <div class="text-sm font-semibold text-white">{{ $ren->user->name }} {{ $ren->user->last_name }}</div>
                                                    <div class="text-[11px] text-slate-500">{{ $ren->user->departamento ?? 'Corporativo' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="text-sm font-semibold text-white mb-1 tracking-tight">{{ $ren->routePlanning->motive }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5 line-clamp-1" title="{{ $ren->routePlanning->destination }}">{{ $ren->routePlanning->destination }}</div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="text-[11px] text-slate-500">
                                                Asignado: <span class="text-slate-300 font-bold">${{ number_format($ren->funds_received, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="text-[11px] text-slate-500">
                                                Rendido: <span class="text-blue-400 font-bold">${{ number_format($ren->total_declared, 0, ',', '.') }}</span>
                                            </div>
                                            @php
                                                $difference = $ren->funds_received - $ren->total_declared;

                                                if ($difference > 0) {
                                                    $balanceLabel = 'A devolver a empresa';
                                                    $balanceAmount = $difference;
                                                    $balanceClass = 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400';
                                                } elseif ($difference < 0) {
                                                    $balanceLabel = 'Reembolso al trabajador';
                                                    $balanceAmount = abs($difference);
                                                    $balanceClass = 'bg-amber-500/10 border-amber-500/20 text-amber-400';
                                                } else {
                                                    $balanceLabel = 'Sin saldo pendiente';
                                                    $balanceAmount = 0;
                                                    $balanceClass = 'bg-blue-500/10 border-blue-500/20 text-blue-400';
                                                }
                                            @endphp

                                            <div class="mt-2 px-2.5 py-1 rounded-md border border-transparent {{ $balanceClass }} w-fit flex items-center gap-2">
                                                <span class="text-[10px] font-bold uppercase tracking-wider">
                                                    {{ $balanceLabel }}:
                                                </span>

                                                <span class="text-xs font-black">
                                                    ${{ number_format($balanceAmount, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    @php
                                        $isOwnRendition = $ren->user_id === auth()->id() && auth()->user()->email !== 'test@example.com';
                                        $difference = $ren->funds_received - $ren->total_declared;

                                        if ($ren->status === 'pending_finances') {
                                            $financeStatusLabel = 'Pendiente de aprobación';
                                            $financeStatusClass = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                                        } elseif ($ren->status === 'approved' && !$ren->payment_completed) {
                                            if ($ren->refund_to_worker) {
                                                $financeStatusLabel = 'Reembolso pendiente';
                                                $financeStatusClass = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                                            } elseif ($ren->refund_to_company) {
                                                $financeStatusLabel = 'Devolución pendiente';
                                                $financeStatusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                            } else {
                                                $financeStatusLabel = 'Cierre pendiente';
                                                $financeStatusClass = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
                                            }
                                        } elseif ($ren->status === 'approved' && $ren->payment_completed) {
                                            $financeStatusLabel = 'Cerrada';
                                            $financeStatusClass = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                                        } else {
                                            $financeStatusLabel = ucfirst($ren->status);
                                            $financeStatusClass = 'bg-slate-800 text-slate-400 border-slate-700';
                                        }
                                    @endphp

                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-3 relative" x-data="{ showReject: false, showPayment: false }">
                                            <span class="px-3 py-1 rounded-xl border text-[9px] font-black uppercase tracking-widest {{ $financeStatusClass }}">
                                                {{ $financeStatusLabel }}
                                            </span>

                                            <div x-show="!showReject && !showPayment" class="flex flex-wrap justify-center gap-2 transition-all">
                                                <a href="{{ route('renditions.show', $ren->id) }}" target="_blank"
                                                    class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-500 transition-all hover:-translate-y-0.5 inline-flex items-center gap-1.5 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver detalle
                                                </a>

                                                @if($ren->transfer_proof_path)
                                                    <a href="{{ route('renditions.download-transfer-proof', $ren->id) }}" target="_blank"
                                                        class="px-4 py-2 bg-slate-800 text-emerald-400 border border-emerald-500/30 text-xs font-semibold rounded-lg hover:bg-slate-700 transition-all hover:-translate-y-0.5 inline-flex items-center gap-1.5 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Ver Transferencia
                                                    </a>
                                                @endif

                                                @if($isOwnRendition)
                                                    <div class="px-4 py-2.5 rounded-xl bg-slate-800/70 border border-slate-700 text-left max-w-[210px]">
                                                        <div class="text-[9px] text-amber-400 font-black uppercase tracking-widest">
                                                            Gestión bloqueada
                                                        </div>
                                                        <div class="text-[10px] text-slate-500 font-bold leading-relaxed mt-1">
                                                            No puedes aprobar, rechazar o cerrar tu propia rendición.
                                                        </div>
                                                    </div>
                                                @else
                                                    @if($ren->status === 'pending_finances')
                                                        <form action="{{ route('renditions.approve-finances-rendition', $ren->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                                Aprobar rendición
                                                            </button>
                                                        </form>

                                                        <button @click="showReject = true"
                                                            class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                            Devolver
                                                        </button>
                                                    @endif

                                                    @if($ren->status === 'approved' && !$ren->payment_completed)
                                                        <button @click="showPayment = true"
                                                            class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                            @if($ren->refund_to_worker)
                                                                Confirmar reembolso
                                                            @elseif($ren->refund_to_company)
                                                                Confirmar devolución
                                                            @else
                                                                Confirmar cierre
                                                            @endif
                                                        </button>
                                                    @endif

                                                    @if($ren->status === 'approved' && $ren->payment_completed)
                                                        <div class="px-4 py-2.5 rounded-xl bg-emerald-500/5 border border-emerald-500/20 text-left max-w-[220px]">
                                                            <div class="text-[9px] text-emerald-400 font-black uppercase tracking-widest">
                                                                Cierre realizado
                                                            </div>
                                                            <div class="text-[10px] text-slate-500 font-bold leading-relaxed mt-1">
                                                                Esta rendición no requiere acciones pendientes.
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>

                                            <div x-show="showReject" x-cloak
                                                class="absolute right-0 top-0 w-72 bg-slate-800 p-4 rounded-xl border border-slate-700 shadow-2xl z-20"
                                                x-transition>
                                                <form action="{{ route('renditions.reject-finances-rendition', $ren->id) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-xs font-bold text-rose-400 mb-2 uppercase tracking-wider text-left">
                                                        Motivo de Devolución
                                                    </label>

                                                    <textarea name="observation" rows="3"
                                                        class="w-full text-xs bg-slate-900 border-slate-700 text-slate-100 rounded-lg focus:ring-rose-500 focus:border-rose-500 placeholder-slate-500"
                                                        required
                                                        placeholder="Error detectado..."></textarea>

                                                    <div class="mt-4 flex justify-end gap-2">
                                                        <button type="button" @click="showReject = false"
                                                            class="px-3 py-1.5 text-xs text-slate-400 hover:text-white transition-colors cursor-pointer">
                                                            Cancelar
                                                        </button>

                                                        <button type="submit"
                                                            class="px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                            Confirmar devolución
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div x-show="showPayment" x-cloak
                                                class="absolute right-0 top-0 w-80 bg-slate-800 p-4 rounded-xl border border-slate-700 shadow-2xl z-20"
                                                x-transition>
                                                <form action="{{ route('renditions.payment-completed', $ren->id) }}" method="POST">
                                                    @csrf

                                                    <label class="block text-xs font-bold text-emerald-400 mb-2 uppercase tracking-wider text-left">
                                                        Confirmar cierre financiero
                                                    </label>

                                                    <p class="text-[11px] text-slate-400 font-bold text-left mb-3 leading-relaxed">
                                                        @if($ren->refund_to_worker)
                                                            Confirma que el reembolso al trabajador fue realizado.
                                                        @elseif($ren->refund_to_company)
                                                            Confirma que la devolución a la empresa fue recibida.
                                                        @else
                                                            Confirma que la rendición quedó sin saldos pendientes.
                                                        @endif
                                                    </p>

                                                    <textarea name="payment_observation" rows="3"
                                                        class="w-full text-xs bg-slate-900 border-slate-700 text-slate-100 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 placeholder-slate-500"
                                                        placeholder="Observación opcional..."></textarea>

                                                    <div class="mt-4 flex justify-end gap-2">
                                                        <button type="button" @click="showPayment = false"
                                                            class="px-3 py-1.5 text-xs text-slate-400 hover:text-white transition-colors cursor-pointer">
                                                            Cancelar
                                                        </button>

                                                        <button type="submit"
                                                            class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                                            Confirmar
                                                        </button>
                                                    </div>
                                                </form>

                                                <form action="{{ route('renditions.reject-transfer', $ren->id) }}" method="POST" class="mt-2">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-red-500 hover:underline">Rechazar comprobante y solicitar nuevo</button>
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
                        <div class="px-8 py-6 border-t border-slate-800 bg-slate-950/20">
                            {{ $renditions->appends(['tab' => 'cierre'])->links() }}
                        </div>
                    @endif
                @endif
            </div>
            @endif

        </div>
    </div>
</x-app-layout>

