<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                {{ __('Mis Solicitudes de Planificación') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-blue-500/20">Solicitudes</span>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Planificación de Viajes y Alimentación</span>
            </div>
        </div>

        <!-- Import Flatpickr -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

        <style>
            /* Custom Flatpickr Overrides for Seges */
            .flatpickr-calendar.dark {
                background: #2b2b2b;
                box-shadow: 0 10px 25px rgba(0,0,0,0.5);
                border: 1px solid #444;
                border-radius: 12px;
            }
            .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
                background: #f97316;
                border-color: #f97316;
            }
        </style>
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

            @if (session('error'))
                <div class="mb-6 bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl relative text-sm font-bold">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Action Bar -->
            <div class="flex justify-end mb-6">
                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-planning-modal')"
                    class="inline-flex items-center px-5 py-2 bg-blue-600 border border-blue-500 rounded-lg font-bold text-[11px] text-white uppercase tracking-wider hover:bg-blue-500 hover:scale-105 active:scale-95 transition-all duration-300 group h-10 shadow-[0_0_20px_rgba(37,99,235,0.3)] cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Crear Planificación') }}
                </button>
            </div>
            <!-- 1. MIS SOLICITUDES -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                
                @if($plannings->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-24 h-24 bg-slate-800 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-500 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white">No tienes solicitudes</h3>
                        <p class="mt-2 text-sm text-slate-400 max-w-sm mx-auto">Comienza creando tu primera planificación de ruta para gestionar tus viajes y viáticos.</p>
                        <div class="mt-8">
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-planning-modal')" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                Crear Planificación
                            </button>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        ID / Creado
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        Detalles del Viaje
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        Fechas
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        Requerimientos
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        Historial
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40" x-data="{ activePlanning: null }">
                                @foreach ($plannings as $plan)
                                    <tr class="hover:bg-slate-800/60 transition-colors duration-200 group cursor-pointer" @click="if (!$event.target.closest('a')) activePlanning = (activePlanning === {{ $plan->id }} ? null : {{ $plan->id }})">
                                        <!-- ID y Fecha -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="flex flex-col items-start gap-2">
                                                <span class="font-mono text-[13px] text-blue-400 font-bold bg-blue-500/10 px-2.5 py-1 rounded-md border border-blue-500/20 shadow-sm group-hover:scale-105 transition-transform origin-left">#REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                <div class="text-[11px] text-slate-500 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $plan->created_at->format('d M, Y H:i') }}
                                                </div>
                                            </div>
                                        </td>
 
                                        <!-- Detalles del Viaje -->
                                        <td class="px-6 py-5">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 h-9 w-9 rounded-lg flex items-center justify-center mt-0.5 {{ $plan->trip_type === 'terreno' ? 'bg-amber-500/10 text-amber-500 ring-1 ring-amber-500/20' : 'bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20' }}">
                                                    @if($plan->trip_type === 'terreno')
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-white">{{ $plan->destination }}</div>
                                                    @if($plan->region)
                                                        <div class="flex items-center gap-1 text-[11px] text-slate-400 mt-0.5">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                            {{ $plan->region }}
                                                        </div>
                                                    @endif
                                                    <div class="text-xs text-slate-400 mt-0.5 line-clamp-1 max-w-[220px]" title="{{ $plan->motive }}">{{ $plan->motive }}</div>
                                                    @if($plan->companions)
                                                        <div class="flex items-center gap-1 mt-0.5 text-[11px] text-blue-400">
                                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            <span class="line-clamp-1 max-w-[180px]" title="{{ $plan->companions }}">{{ $plan->companions }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
 
                                        <!-- Fechas -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center gap-2 text-sm text-slate-300">
                                                    <span class="text-[10px] font-bold text-slate-500 uppercase w-7">Del</span>
                                                    <span class="font-medium">{{ \Carbon\Carbon::parse($plan->start_date)->format('d M, Y') }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 text-sm text-slate-300">
                                                    <span class="text-[10px] font-bold text-slate-500 uppercase w-7">Al</span>
                                                    <span class="font-medium">{{ \Carbon\Carbon::parse($plan->end_date)->format('d M, Y') }}</span>
                                                </div>
                                                @php $days = \Carbon\Carbon::parse($plan->start_date)->diffInDays(\Carbon\Carbon::parse($plan->end_date)) + 1; @endphp
                                                <div class="text-[10px] text-slate-500 font-medium">{{ $days }} {{ $days === 1 ? 'día' : 'días' }}</div>
                                            </div>
                                        </td>
 
                                        <!-- Requerimientos -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @php
                                                $requestedFunds = $plan->requested_funds ?? 0;
                                                $amipassAmount = $plan->amipass_amount ?? 0;
                                                $totalRequested = $requestedFunds + $amipassAmount;
                                            @endphp
 
                                            <div class="flex flex-col gap-1.5">
                                                <span class="px-2.5 py-1 inline-flex items-center text-xs font-black rounded-md bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 w-fit">
                                                    Total: ${{ number_format($totalRequested, 0, ',', '.') }}
                                                </span>
 
                                                @if($plan->requires_funds)
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-md bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20 w-fit">
                                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Fondos: ${{ number_format($requestedFunds, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-md bg-slate-700/50 text-slate-500 ring-1 ring-slate-600/30 w-fit">
                                                        Sin fondos
                                                    </span>
                                                @endif
 
                                                @if($plan->requires_amipass)
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-md bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20 w-fit">
                                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                        </svg>
                                                        Amipass: ${{ number_format($amipassAmount, 0, ',', '.') }}
                                                    </span>
 
                                                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                                                        {{ $plan->amipass_business_days ?? $plan->amipass_days }} día(s)
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-medium rounded-md bg-slate-700/50 text-slate-500 ring-1 ring-slate-600/30 w-fit">
                                                        Sin Amipass
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
 
                                        <!-- Estado -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @switch($plan->status)
                                                @case('draft')
                                                    <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-slate-500/10 text-slate-400 ring-1 ring-slate-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-2"></span>Borrador
                                                    </span>
                                                    @break
                                                @case('pending_jefatura')
                                                    <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-2 animate-pulse"></span>Esp. Jefatura
                                                    </span>
                                                    @break
                                                @case('pending_controlling')
                                                    <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 mr-2 animate-pulse"></span>En Controlling
                                                    </span>
                                                    @break
                                                @case('pending_finances')
                                                    <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-purple-500/10 text-purple-400 ring-1 ring-purple-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 mr-2 animate-pulse"></span>En Finanzas
                                                    </span>
                                                    @break
                                                @case('approved')
                                                    <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20">
                                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Aprobada
                                                    </span>

                                                    @if($plan->rendition)
                                                        <div class="mt-2 text-[10px] text-blue-400 font-bold uppercase tracking-widest">
                                                            Rendición generada
                                                        </div>
                                                    @else
                                                        <div class="mt-2 text-[10px] text-amber-400 font-bold uppercase tracking-widest">
                                                            Rendición no generada
                                                        </div>
                                                    @endif
                                                    @break
                                                @case('rejected')
                                                    <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-red-500/10 text-red-400 ring-1 ring-red-500/20">
                                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>Rechazada
                                                    </span>
                                                    @break
                                            @endswitch
                                            <div class="mt-2 flex flex-col gap-1.5">
                                                <a href="{{ route('route-plannings.pdf', $plan->id) }}"
                                                target="_blank"
                                                class="px-2.5 py-1 inline-flex items-center text-[10px] font-semibold rounded-md bg-rose-600 text-white hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer w-fit">
                                                    Descargar PDF
                                                </a>

                                                @if($plan->status === 'approved' && $plan->rendition)
                                                    <a href="{{ route('renditions.show', $plan->rendition->id) }}"
                                                    wire:navigate
                                                    class="px-2.5 py-1 inline-flex items-center text-[10px] font-semibold rounded-md border border-blue-500/30 text-blue-400 hover:bg-blue-500 hover:text-white hover:-translate-y-0.5 transition-all cursor-pointer w-fit">
                                                        Ir a rendición
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 align-top">
                                            @if($plan->workflowHistories->isEmpty())
                                                <span class="text-xs text-slate-500">Sin movimientos</span>
                                            @else
                                                <div class="space-y-2 max-w-xs">
                                                    @foreach($plan->workflowHistories->sortByDesc('created_at')->take(3) as $history)
                                                        <div class="text-xs border-l-2 pl-2
                                                            @if(str_contains($history->action, 'rejected'))
                                                                border-red-500
                                                            @elseif(str_contains($history->action, 'approved'))
                                                                border-green-500
                                                            @else
                                                                border-indigo-500
                                                            @endif
                                                        ">
                                                            <div class="font-semibold text-white">
                                                                {{ $history->user->name ?? 'Sistema' }}
                                                            </div>
 
                                                            <div class="text-slate-400">
                                                                @php
                                                                    $actionLabels = [
                                                                        'approved_by_jefatura' => 'Aprobado por Jefatura',
                                                                        'rejected_by_jefatura' => 'Rechazado por Jefatura',
                                                                        'approved_by_controlling' => 'Aprobado por Controlling',
                                                                        'rejected_by_controlling' => 'Rechazado por Controlling',
                                                                        'approved_by_finances' => 'Aprobado por Finanzas',
                                                                        'rejected_by_finances' => 'Rechazado por Finanzas',
                                                                    ];
 
                                                                    $actionLabel = $actionLabels[$history->action] ?? ucfirst(str_replace('_', ' ', $history->action));
                                                                @endphp
 
                                                                {{ $actionLabel }}
                                                            </div>
 
                                                            <div class="text-slate-500">
                                                                {{ $history->created_at->format('d/m/Y H:i') }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Collapsible Detail Row -->
                                    <tr x-show="activePlanning === {{ $plan->id }}" x-cloak class="bg-slate-900/40" x-transition>
                                        <td colspan="6" class="px-8 py-6 border-b border-slate-700/40">
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                                
                                                <!-- Columna Izquierda: Detalles Adicionales (7 cols) -->
                                                <div class="md:col-span-7 space-y-6">
                                                    @if($plan->status === 'rejected' && $plan->observations->count() > 0)
                                                        <div class="bg-rose-500/10 border border-rose-500/30 rounded-3xl p-5 shadow-2xl relative overflow-hidden flex items-start gap-4">
                                                            <div class="absolute -top-24 -right-24 w-48 h-48 bg-rose-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                                                            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20 shadow-inner flex-shrink-0">
                                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                </svg>
                                                            </div>
                                                            <div class="space-y-3 w-full">
                                                                <div>
                                                                    <h3 class="text-sm font-black text-rose-400 uppercase tracking-widest">Motivo de Rechazo</h3>
                                                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Observaciones registradas por el revisor</p>
                                                                </div>
                                                                <div class="space-y-3">
                                                                    @foreach($plan->observations as $obs)
                                                                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800 flex flex-col gap-2">
                                                                            <div class="flex items-center gap-2">
                                                                                <span class="font-black text-[11px] text-white uppercase tracking-tight">{{ $obs->user->name ?? 'Revisor' }}</span>
                                                                                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                                                                <span class="text-[10px] text-slate-500 font-bold uppercase">{{ $obs->created_at->format('d/m, H:i') }}</span>
                                                                            </div>
                                                                            <p class="text-xs text-slate-300 font-medium leading-relaxed">{{ $obs->observation }}</p>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Destinos Adicionales -->
                                                    <div>
                                                        <h5 class="text-xs font-black text-white uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                                            Destinos del Viaje
                                                        </h5>
                                                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                                            <div class="text-xs font-bold text-white mb-2">Destino Principal: {{ $plan->destination }} @if($plan->region) ({{ $plan->region }}) @endif</div>
                                                            @if(!empty($plan->destinations))
                                                                <div class="mt-2.5 pt-2.5 border-t border-slate-800/80">
                                                                    <div class="text-[10px] text-slate-500 font-black uppercase tracking-wider mb-2">Destinos Adicionales:</div>
                                                                    <ul class="space-y-1.5">
                                                                        @foreach($plan->destinations as $dest)
                                                                            @if(!empty($dest['destination']))
                                                                                <li class="flex items-center gap-2 text-xs font-semibold text-slate-300">
                                                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                                                    {{ $dest['destination'] }} @if(!empty($dest['region'])) <span class="text-slate-500 font-medium">({{ $dest['region'] }})</span> @endif
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @else
                                                                <p class="text-xs text-slate-500 font-medium italic mt-1">Sin destinos adicionales registrados.</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Desglose de Fondos -->
                                                    @if($plan->requires_funds)
                                                        <div>
                                                            <h5 class="text-xs font-black text-white uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                Desglose de Presupuesto Solicitado
                                                            </h5>
                                                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800 space-y-4">
                                                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                                                    <div class="bg-slate-950/40 p-2.5 rounded-xl border border-slate-800/80 text-center">
                                                                        <div class="text-[9px] text-slate-500 font-black uppercase tracking-wider">Bencina</div>
                                                                        <div class="text-xs font-bold text-white mt-1">${{ number_format($plan->funds_bencina ?? 0, 0, ',', '.') }}</div>
                                                                    </div>
                                                                    <div class="bg-slate-950/40 p-2.5 rounded-xl border border-slate-800/80 text-center">
                                                                        <div class="text-[9px] text-slate-500 font-black uppercase tracking-wider">Peajes</div>
                                                                        <div class="text-xs font-bold text-white mt-1">${{ number_format($plan->funds_peaje ?? 0, 0, ',', '.') }}</div>
                                                                    </div>
                                                                    <div class="bg-slate-950/40 p-2.5 rounded-xl border border-slate-800/80 text-center">
                                                                        <div class="text-[9px] text-slate-500 font-black uppercase tracking-wider">Alojamiento</div>
                                                                        <div class="text-xs font-bold text-white mt-1">${{ number_format($plan->funds_alojamiento ?? 0, 0, ',', '.') }}</div>
                                                                    </div>
                                                                    <div class="bg-slate-950/40 p-2.5 rounded-xl border border-slate-800/80 text-center">
                                                                        <div class="text-[9px] text-slate-500 font-black uppercase tracking-wider">Alimentación</div>
                                                                        <div class="text-xs font-bold text-white mt-1">${{ number_format($plan->funds_alimentacion ?? 0, 0, ',', '.') }}</div>
                                                                    </div>
                                                                    <div class="bg-slate-950/40 p-2.5 rounded-xl border border-slate-800/80 text-center col-span-2 sm:col-span-1">
                                                                        <div class="text-[9px] text-slate-500 font-black uppercase tracking-wider">Otros</div>
                                                                        <div class="text-xs font-bold text-white mt-1">${{ number_format($plan->funds_otros ?? 0, 0, ',', '.') }}</div>
                                                                    </div>
                                                                </div>
                                                                @if($plan->funds_description)
                                                                    <div class="pt-3 border-t border-slate-800/80">
                                                                        <div class="text-[10px] text-slate-500 font-black uppercase tracking-wider mb-1">Justificación del presupuesto:</div>
                                                                        <p class="text-xs text-slate-300 font-medium leading-relaxed">{{ $plan->funds_description }}</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Columna Derecha: Notificaciones de viaje (5 cols) -->
                                                <div class="md:col-span-5">
                                                    <div class="bg-slate-900/60 p-5 rounded-2xl border border-slate-800 h-full flex flex-col justify-between">
                                                        <div>
                                                            <h5 class="text-xs font-black text-white uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                                Notificación de Viaje (Anexo Contrato)
                                                            </h5>
                                                            <p class="text-[11px] text-slate-400 leading-relaxed mb-4">
                                                                Envía un correo con los detalles del viaje a destinatarios específicos para notificar la actividad (anexo de contrato u otros fines).
                                                            </p>
                                                            
                                                            <form action="{{ route('route-plannings.send-notification', $plan->id) }}" method="POST" class="space-y-3">
                                                                @csrf
                                                                <div>
                                                                    <label class="block text-[9px] text-slate-500 font-black uppercase tracking-wider mb-1.5">Correos Destinatarios (separados por coma)</label>
                                                                    <input type="text" name="emails" value="{{ $plan->notification_emails }}" placeholder="ej: contratos@empresa.com, jefatura@empresa.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold text-white placeholder-slate-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                                                                </div>
                                                                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-indigo-600/10">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                                    Enviar Notificación
                                                                </button>
                                                            </form>
                                                        </div>
                                                        @if($plan->notification_emails)
                                                            <div class="mt-4 pt-3 border-t border-slate-800/80 text-[10px] text-slate-500">
                                                                <span class="font-bold">Últimos notificados:</span> <span class="font-mono text-slate-400">{{ $plan->notification_emails }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-slate-700/40 bg-slate-950/20">
                        {{ $plannings->links() }}
                    </div>
                @endif
                
            </div>

            <!-- 2. CREAR PLANIFICACIÓN MODAL -->
            <x-modal name="create-planning-modal" :show="$errors->any() || request('tab') === 'crear' || request('open_create')" focusable maxWidth="4xl">
                <div class="p-0 bg-slate-800 text-slate-100 overflow-hidden rounded-[2.5rem] border border-slate-700 shadow-2xl relative">
                    <!-- Close button in top right -->
                    <button type="button" @click="$dispatch('close-modal', 'create-planning-modal'); window.history.replaceState({}, '', '{{ route('route-plannings.index') }}')" class="absolute top-6 right-6 w-9 h-9 bg-slate-800/50 hover:bg-slate-700 rounded-xl text-slate-400 hover:text-white transition-all flex items-center justify-center cursor-pointer backdrop-blur-md border border-slate-700/30 z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="p-8 border-b border-slate-700/40 bg-slate-900/50">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-orange-500/10 text-orange-400 p-2.5 rounded-xl ring-1 ring-orange-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white tracking-tight uppercase">Formulario de Solicitud</h3>
                        </div>
                        <p class="text-slate-400 text-sm">Completa los detalles de tu viaje para solicitar fondos o alimentación antes de rendir.</p>
                    </div>

                    <div class="max-h-[70vh] overflow-y-auto custom-scrollbar">
                        <form action="{{ route('route-plannings.store') }}" method="POST" class="p-8" x-data="{ requiresFunds: false, requiresAmipass: false, destinations: [], funds_bencina: 0, funds_peaje: 0, funds_alojamiento: 0, funds_alimentacion: 0, funds_otros: 0, get requested_funds() { return (parseInt(this.funds_bencina) || 0) + (parseInt(this.funds_peaje) || 0) + (parseInt(this.funds_alojamiento) || 0) + (parseInt(this.funds_alimentacion) || 0) + (parseInt(this.funds_otros) || 0); }, addDestination() { this.destinations.push({ region: '', destination: '' }); }, removeDestination(index) { this.destinations.splice(index, 1); } }">
                        @csrf
                        
                        @php
                            $now = now();
                            $thisWeekWednesdayLimit = now()->startOfWeek()->addDays(2)->setTime(13, 0);
                            if ($now->lessThanOrEqualTo($thisWeekWednesdayLimit)) {
                                $fundsDate = now()->startOfWeek()->addDays(4);
                            } else {
                                $fundsDate = now()->startOfWeek()->addDays(11);
                            }
                        @endphp

                        <div class="mb-6 bg-amber-500/10 border border-amber-500/20 text-amber-300 px-4 py-3 rounded-lg text-sm">
                            <strong class="text-amber-400">Importante:</strong>
                            Las solicitudes ingresadas antes del miércoles a las 13:00 hrs tendrán fondos disponibles el viernes de la misma semana.
                            Las solicitudes ingresadas después de ese horario quedarán para el viernes de la semana siguiente.
                            <br>
                            <span class="font-semibold text-amber-400">
                                Según la fecha actual, la disponibilidad estimada sería: {{ $fundsDate->format('d/m/Y') }}.
                            </span>
                        </div>

                        <!-- 1. Detalles Generales -->
                        <h4 class="text-lg font-semibold text-white mb-4 border-b border-slate-700 pb-2">1. Detalles del Viaje</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            
                            <!-- Tipo de Viaje -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Tipo de Actividad <span class="text-red-500">*</span></label>
                                <div class="flex space-x-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="trip_type" value="terreno" class="form-radio text-orange-600 focus:ring-orange-500 border-slate-700 bg-slate-900" required>
                                        <span class="ml-2 text-slate-300">Trabajo en Terreno</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="trip_type" value="reunion" class="form-radio text-orange-600 focus:ring-orange-500 border-slate-700 bg-slate-900" required>
                                        <span class="ml-2 text-slate-300">Reunión de Negocios</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Fechas con Flatpickr -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-1">Fechas del Viaje <span class="text-red-500">*</span></label>
                                
                                <input type="hidden" name="start_date" id="start_date" required>
                                <input type="hidden" name="end_date" id="end_date" required>

                                <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full md:w-1/2">
                                    <input type="text" id="dateRange" required placeholder="Seleccionar rango de fechas..." class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm">
                                </div>
                                <p class="text-xs text-slate-500 mt-2">Haz clic para abrir el calendario interactivo. Selecciona inicio y fin.</p>
                            </div>

                            <!-- Destino: Región y Ciudad -->
                            <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" x-data="locationAutocomplete()">
                                <div>
                                    <label for="region" class="block text-sm font-medium text-slate-300 mb-1">Región <span class="text-red-500">*</span></label>
                                    <select name="region" id="region" x-model="searchRegion" @change="searchCity = ''" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 [&>option]:bg-slate-900" required>
                                        <option value="">Seleccione una región</option>
                                        <template x-for="regionObj in dataset" :key="regionObj.region">
                                            <option :value="regionObj.region" x-text="regionObj.region"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label for="destination" class="block text-sm font-medium text-slate-300 mb-1">Destino (Ciudad/Comuna) <span class="text-red-500">*</span></label>
                                    <select name="destination" id="destination" x-model="searchCity" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-100 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 [&>option]:bg-slate-900" required>
                                        <option value="">Seleccione una comuna</option>
                                        <template x-for="city in availableCities" :key="city">
                                            <option :value="city" x-text="city"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Destinations Section -->
                            <div class="col-span-1 md:col-span-2 pt-4 border-t border-slate-700/60">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="block text-sm font-semibold text-slate-300">Destinos Adicionales (Múltiples Destinos)</label>
                                    <button type="button" @click="addDestination()" class="px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white border border-blue-500/30 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Agregar Destino
                                    </button>
                                </div>
                                
                                <input type="hidden" name="destinations" :value="JSON.stringify(destinations)">

                                <div class="space-y-4">
                                    <template x-for="(dest, index) in destinations" :key="index">
                                        <div class="flex items-center gap-4 bg-slate-900/40 p-4 rounded-2xl border border-slate-800" 
                                             x-data="locationAutocomplete()" 
                                             x-init="searchRegion = dest.region; searchCity = dest.destination; $watch('searchRegion', val => { destinations[index].region = val; searchCity = ''; destinations[index].destination = ''; }); $watch('searchCity', val => destinations[index].destination = val)">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                                                <div>
                                                    <label class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Región</label>
                                                    <select x-model="searchRegion" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 [&>option]:bg-slate-900" required>
                                                        <option value="">Seleccione una región</option>
                                                        <template x-for="regionObj in dataset" :key="regionObj.region">
                                                            <option :value="regionObj.region" x-text="regionObj.region"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-1">Ciudad / Comuna</label>
                                                    <select x-model="searchCity" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-semibold text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 [&>option]:bg-slate-900" required>
                                                        <option value="">Seleccione una comuna</option>
                                                        <template x-for="city in availableCities" :key="city">
                                                            <option :value="city" x-text="city"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeDestination(index)" class="p-2 bg-rose-600/10 text-rose-500 hover:bg-rose-600 hover:text-white rounded-xl transition-all cursor-pointer border border-rose-500/20" title="Eliminar destino">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Motivo -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="motive" class="block text-sm font-medium text-slate-300 mb-1">Motivo del viaje <span class="text-red-500">*</span></label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                    <input type="text" name="motive" id="motive" required placeholder="Ej: Visita a cliente X" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm">
                                </div>
                            </div>

                            <!-- Acompañantes -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="companions" class="block text-sm font-medium text-slate-300 mb-1">
                                    Acompañantes
                                    <span class="text-xs text-slate-500 font-normal ml-1">(Opcional)</span>
                                </label>
                                <div class="flex items-start border border-slate-700 rounded-lg bg-slate-900 px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full min-h-[70px]">
                                    <textarea name="companions" id="companions" rows="2" placeholder="Ej: Juan Pérez, María González, Carlos López..." class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm resize-y"></textarea>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">Escribe los nombres de las personas que te acompañarán, separados por coma.</p>
                            </div>

                            <!-- Correos Dinámicos -->
                            <div class="col-span-1 md:col-span-2 mt-4" x-data="{ emails: [''] }">
                                <label class="block text-sm font-medium text-slate-300 mb-1">Correos para Notificación de Anexo</label>
                                <template x-for="(email, index) in emails" :key="index">
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="email" name="notification_emails[]" x-model="emails[index]" placeholder="ejemplo@dimak.cl" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                                        <button type="button" @click="emails.splice(index, 1)" class="px-3 py-2 bg-rose-600/10 text-rose-500 hover:bg-rose-600 hover:text-white rounded-lg transition-all border border-rose-500/20" title="Eliminar correo">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="emails.push('')" class="text-xs text-blue-400 font-bold hover:text-blue-300 transition-colors inline-flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg> Agregar otro correo
                                </button>
                            </div>

                            <!-- RUT de Integrantes para Amipass -->
                            <div class="col-span-1 md:col-span-2 mt-4" x-show="requiresAmipass" x-data="{ ruts: [''] }" x-transition x-cloak>
                                <label class="block text-sm font-medium text-slate-300 mb-1">
                                    RUT de Integrantes / Acompañantes para Amipass <span class="text-red-500">*</span>
                                </label>
                                <template x-for="(rut, index) in ruts" :key="index">
                                    <div class="flex items-center gap-2 mb-2">
                                        <input type="text" name="amipass_ruts[]" x-model="ruts[index]" placeholder="12.345.678-9" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors" x-bind:required="requiresAmipass">
                                        <button type="button" @click="ruts.splice(index, 1)" class="px-3 py-2 bg-rose-600/10 text-rose-500 hover:bg-rose-600 hover:text-white rounded-lg transition-all border border-rose-500/20" title="Eliminar RUT">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </template>
                                <button type="button" @click="ruts.push('')" class="text-xs text-blue-400 font-bold hover:text-blue-300 transition-colors inline-flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg> Agregar otro integrante
                                </button>
                            </div>

                        </div>

                        <!-- 2. Solicitudes Financieras -->
                        <h4 class="text-lg font-semibold text-white mb-4 border-b border-slate-700 pb-2">2. Fondos y Viáticos</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            
                            <!-- Fondos por Rendir -->
                            <div class="bg-slate-800/40 p-5 rounded-xl border border-slate-700 transition-all">
                                <label class="flex items-center cursor-pointer mb-3">
                                    <div class="relative">
                                        <input type="checkbox" name="requires_funds" value="1" class="sr-only" x-model="requiresFunds">
                                        <div class="block bg-slate-600 w-10 h-6 rounded-full transition" :class="{'bg-orange-500': requiresFunds}"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform" :class="{'translate-x-4': requiresFunds}"></div>
                                    </div>
                                    <div class="ml-3 font-medium text-slate-200">
                                        Solicitar Fondos por Rendir
                                    </div>
                                </label>
                                <p class="text-xs text-slate-400 mb-4 ml-12">Adelanto de dinero para peajes, combustible, alojamiento, etc.</p>
                                
                                <div x-show="requiresFunds" x-transition.opacity class="ml-12 space-y-4" style="display: none;">
                                    <p class="text-xs text-slate-400 font-semibold mb-3">Ingrese el desglose de fondos estimado. El total se calculará de forma automática.</p>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-300 mb-1">Bencina / Combustible ($)</label>
                                            <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2 focus-within:border-blue-500">
                                                <span class="text-slate-500 text-xs mr-1.5">$</span>
                                                <input type="number" name="funds_bencina" x-model.number="funds_bencina" min="0" class="w-full bg-transparent border-none outline-none text-white text-sm" placeholder="0">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-300 mb-1">Peajes ($)</label>
                                            <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2 focus-within:border-blue-500">
                                                <span class="text-slate-500 text-xs mr-1.5">$</span>
                                                <input type="number" name="funds_peaje" x-model.number="funds_peaje" min="0" class="w-full bg-transparent border-none outline-none text-white text-sm" placeholder="0">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-300 mb-1">Alojamiento ($)</label>
                                            <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2 focus-within:border-blue-500">
                                                <span class="text-slate-500 text-xs mr-1.5">$</span>
                                                <input type="number" name="funds_alojamiento" x-model.number="funds_alojamiento" min="0" class="w-full bg-transparent border-none outline-none text-white text-sm" placeholder="0">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-300 mb-1">Alimentación ($)</label>
                                            <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2 focus-within:border-blue-500">
                                                <span class="text-slate-500 text-xs mr-1.5">$</span>
                                                <input type="number" name="funds_alimentacion" x-model.number="funds_alimentacion" min="0" class="w-full bg-transparent border-none outline-none text-white text-sm" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-medium text-slate-300 mb-1">Otros Gastos ($)</label>
                                            <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2 focus-within:border-blue-500">
                                                <span class="text-slate-500 text-xs mr-1.5">$</span>
                                                <input type="number" name="funds_otros" x-model.number="funds_otros" min="0" class="w-full bg-transparent border-none outline-none text-white text-sm" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-t border-slate-700/60">
                                        <label for="requested_funds" class="block text-sm font-bold text-white mb-1">Monto Total Solicitado ($)</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-950 px-3 py-2.5">
                                            <span class="text-slate-500 text-sm mr-2">$</span>
                                            <input type="number" name="requested_funds" id="requested_funds" :value="requested_funds" readonly class="w-full bg-transparent border-none outline-none text-slate-300 text-sm font-black focus:ring-0 focus:outline-none" placeholder="0">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-300 mb-1">Justificación detallada del monto solicitado <span class="text-red-500">*</span></label>
                                        <textarea name="funds_description" rows="3" placeholder="Por favor, explique la necesidad de los fondos y los montos estimados..." class="w-full bg-slate-900 border border-slate-700 rounded-lg text-sm text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:ring-blue-500" x-bind:required="requiresFunds"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Amipass -->
                            <div class="bg-slate-800/40 p-5 rounded-xl border border-slate-700 transition-all">
                                <label class="flex items-center cursor-pointer mb-3">
                                    <div class="relative">
                                        <input type="checkbox" name="requires_amipass" value="1" class="sr-only" x-model="requiresAmipass">
                                        <div class="block bg-slate-600 w-10 h-6 rounded-full transition" :class="{'bg-green-500': requiresAmipass}"></div>
                                        <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform" :class="{'translate-x-4': requiresAmipass}"></div>
                                    </div>
                                    <div class="ml-3 font-medium text-slate-200 flex items-center gap-2">
                                        Solicitar Tarjeta Amipass
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">Alimentación</span>
                                    </div>
                                </label>
                                <p class="text-xs text-slate-400 mb-4 ml-12">Recarga diaria para almuerzo/comidas durante el viaje.</p>
                                
                                <div x-show="requiresAmipass" x-transition.opacity class="ml-12 space-y-4" style="display: none;">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="amipass_start_time" class="block text-sm font-medium text-slate-300 mb-1">
                                                Hora de salida
                                            </label>
                                            <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                                <input
                                                    type="time"
                                                    name="amipass_start_time"
                                                    id="amipass_start_time"
                                                    class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none"
                                                    x-bind:required="requiresAmipass"
                                                >
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1">
                                                Hora en que comienza el desplazamiento el primer día.
                                            </p>
                                        </div>

                                        <div>
                                            <label for="amipass_end_time" class="block text-sm font-medium text-slate-300 mb-1">
                                                Hora de regreso
                                            </label>
                                            <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                                <input
                                                    type="time"
                                                    name="amipass_end_time"
                                                    id="amipass_end_time"
                                                    class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none"
                                                    x-bind:required="requiresAmipass"
                                                >
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1">
                                                Hora estimada en que termina el viaje el último día.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-3">
                                        <p class="text-xs text-green-400 font-semibold">
                                            El monto Amipass se calculará automáticamente según las fechas del viaje, la hora de salida y la hora de regreso registradas.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end mt-8 border-t border-slate-700 pt-6">
                            <a href="{{ route('route-plannings.index') }}" @click.prevent="$dispatch('close-modal', 'create-planning-modal'); window.history.replaceState({}, '', '{{ route('route-plannings.index') }}')" class="inline-flex items-center justify-center px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer mr-4">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-md shadow-blue-500/20 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-[#1e293b] transition-all hover:-translate-y-0.5 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                                Enviar Solicitud
                            </button>
                        </div>

                    </form>
                </div>
            </div>
            </x-modal>
            
            <script>
                // Initialize Flatpickr
                document.addEventListener('DOMContentLoaded', function() {
                    let fpInstance = null;
                    const initFlatpickr = () => {
                        if (fpInstance) {
                            fpInstance.destroy();
                        }
                        const el = document.getElementById('dateRange');
                        if (el) {
                            fpInstance = flatpickr(el, {
                                mode: "range",
                                dateFormat: "Y-m-d",
                                altInput: true,
                                altFormat: "d/m/Y",
                                locale: "es",
                                monthSelectorType: "static",
                                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                                onChange: function(selectedDates, dateStr, instance) {
                                    if (selectedDates.length === 2) {
                                        const start = selectedDates[0].toLocaleDateString('en-CA');
                                        const end = selectedDates[1].toLocaleDateString('en-CA');
                                        document.getElementById('start_date').value = start;
                                        document.getElementById('end_date').value = end;
                                    } else if (selectedDates.length === 1) {
                                        const start = selectedDates[0].toLocaleDateString('en-CA');
                                        document.getElementById('start_date').value = start;
                                        document.getElementById('end_date').value = start;
                                    } else {
                                        document.getElementById('start_date').value = '';
                                        document.getElementById('end_date').value = '';
                                    }
                                }
                            });
                        }
                    };

                    initFlatpickr();

                    window.addEventListener('open-modal', (event) => {
                        if (event.detail === 'create-planning-modal') {
                            setTimeout(initFlatpickr, 100);
                        }
                    });
                });

                function locationAutocomplete() {
                    return {
                        searchRegion: '',
                        searchCity: '',
                        openRegion: false,
                        openCity: false,
                        dataset: [
                            { "region": "Arica y Parinacota", "comunas": ["Arica", "Camarones", "Putre", "General Lagos"] },
                            { "region": "Tarapacá", "comunas": ["Iquique", "Alto Hospicio", "Pozo Almonte", "Camiña", "Colchane", "Huara", "Pica"] },
                            { "region": "Antofagasta", "comunas": ["Antofagasta", "Mejillones", "Sierra Gorda", "Taltal", "Calama", "Ollagüe", "San Pedro de Atacama", "Tocopilla", "María Elena"] },
                            { "region": "Atacama", "comunas": ["Copiapó", "Caldera", "Tierra Amarilla", "Chañaral", "Diego de Almagro", "Vallenar", "Alto del Carmen", "Freirina", "Huasco"] },
                            { "region": "Coquimbo", "comunas": ["La Serena", "Coquimbo", "Andacollo", "La Higuera", "Paiguano", "Vicuña", "Illapel", "Canela", "Los Vilos", "Salamanca", "Ovalle", "Combarbalá", "Monte Patria", "Punitaqui", "Río Hurtado"] },
                            { "region": "Valparaíso", "comunas": ["Valparaíso", "Casablanca", "Concón", "Juan Fernández", "Puchuncaví", "Quintero", "Viña del Mar", "Isla de Pascua", "Los Andes", "Calle Larga", "Rinconada", "San Esteban", "La Ligua", "Cabildo", "Papudo", "Petorca", "Zapallar", "Quillota", "Calera", "Hijuelas", "La Cruz", "Nogales", "San Antonio", "Algarrobo", "Cartagena", "El Quisco", "El Tabo", "Santo Domingo", "San Felipe", "Catemu", "Llaillay", "Panquehue", "Putaendo", "Santa María", "Quilpué", "Limache", "Olmué", "Villa Alemana"] },
                            { "region": "Región del Libertador Gral. Bernardo O’Higgins", "comunas": ["Rancagua", "Codegua", "Coinco", "Coltauco", "Doñihue", "Graneros", "Las Cabras", "Machalí", "Malloa", "Mostazal", "Olivar", "Peumo", "Pichidegua", "Quinta de Tilcoco", "Rengo", "Requínoa", "San Vicente", "Pichilemu", "La Estrella", "Litueche", "Marchihue", "Navidad", "Paredones", "San Fernando", "Chépica", "Chimbarongo", "Lolol", "Nancagua", "Palmilla", "Peralillo", "Placilla", "Pumanque", "Santa Cruz"] },
                            { "region": "Región del Maule", "comunas": ["Talca", "Constitución", "Curepto", "Empedrado", "Maule", "Pelarco", "Pencahue", "Río Claro", "San Clemente", "San Rafael", "Cauquenes", "Chanco", "Pelluhue", "Curicó", "Hualañé", "Licantén", "Molina", "Rauco", "Romeral", "Sagrada Familia", "Teno", "Vichuquén", "Linares", "Colbún", "Longaví", "Parral", "Retiro", "San Javier", "Villa Alegre", "Yerbas Buenas"] },
                            { "region": "Región de Ñuble", "comunas": ["Cobquecura", "Coelemu", "Ninhue", "Portezuelo", "Quirihue", "Ránquil", "Treguaco", "Bulnes", "Chillán Viejo", "Chillán", "El Carmen", "Pemuco", "Pinto", "Quillón", "San Ignacio", "Yungay", "Coihueco", "Ñiquén", "San Carlos", "San Fabián", "San Nicolás"] },
                            { "region": "Región del Biobío", "comunas": ["Concepción", "Coronel", "Chiguayante", "Florida", "Hualqui", "Lota", "Penco", "San Pedro de la Paz", "Santa Juana", "Talcahuano", "Tomé", "Hualpén", "Lebu", "Arauco", "Cañete", "Contulmo", "Curanilahue", "Los Álamos", "Tirúa", "Los Ángeles", "Antuco", "Cabrero", "Laja", "Mulchén", "Nacimiento", "Negrete", "Quilaco", "Quilleco", "San Rosendo", "Santa Bárbara", "Tucapel", "Yumbel", "Alto Biobío"] },
                            { "region": "Región de la Araucanía", "comunas": ["Temuco", "Carahue", "Cunco", "Curarrehue", "Freire", "Galvarino", "Gorbea", "Lautaro", "Loncoche", "Melipeuco", "Nueva Imperial", "Padre las Casas", "Perquenco", "Pitrufquén", "Pucón", "Saavedra", "Teodoro Schmidt", "Toltén", "Vilcún", "Villarrica", "Cholchol", "Angol", "Collipulli", "Curacautín", "Ercilla", "Lonquimay", "Los Sauces", "Lumaco", "Traiguén", "Victoria"] },
                            { "region": "Región de Los Ríos", "comunas": ["Valdivia", "Corral", "Lanco", "Los Lagos", "Máfil", "Mariquina", "Paillaco", "Panguipulli", "La Unión", "Futrono", "Lago Ranco", "Río Bueno"] },
                            { "region": "Región de Los Lagos", "comunas": ["Puerto Montt", "Calbuco", "Cochamó", "Fresia", "Frutillar", "Los Muermos", "Llanquihue", "Maullín", "Puerto Varas", "Castro", "Ancud", "Chonchi", "Curaco de Vélez", "Dalcahue", "Puqueldón", "Queilén", "Quellón", "Quemchi", "Quinchao", "Osorno", "Puerto Octay", "Purranque", "Puyehue", "Entre Lagos", "Río Negro", "San Juan de la Costa", "San Pablo", "Chaitén", "Futaleufú", "Hualaihué", "Palena"] },
                            { "region": "Región Aisén del Gral. Carlos Ibáñez del Campo", "comunas": ["Coihaique", "Lago Verde", "Aisén", "Cisnes", "Guaitecas", "Cochrane", "O’Higgins", "Tortel", "Chile Chico", "Río Ibáñez"] },
                            { "region": "Región de Magallanes y de la Antártica Chilena", "comunas": ["Punta Arenas", "Laguna Blanca", "Río Verde", "San Gregorio", "Cabo de Hornos (Ex Navarino)", "Antártica", "Porvenir", "Primavera", "Timaukel", "Natales", "Torres del Paine"] },
                            { "region": "Región Metropolitana de Santiago", "comunas": ["Cerrillos", "Cerro Navia", "Conchalí", "El Bosque", "Estación Central", "Huechuraba", "Independencia", "La Cisterna", "La Florida", "La Granja", "La Pintana", "La Reina", "Las Condes", "Lo Barnechea", "Lo Espejo", "Lo Prado", "Macul", "Maipú", "Ñuñoa", "Pedro Aguirre Cerda", "Peñalolén", "Providencia", "Pudahuel", "Quilicura", "Quinta Normal", "Recoleta", "Renca", "Santiago", "San Joaquín", "San Miguel", "San Ramón", "Vitacura", "Puente Alto", "Pirque", "San José de Maipo", "Colina", "Lampa", "Tiltil", "San Bernardo", "Buin", "Calera de Tango", "Paine", "Melipilla", "Alhué", "Curacaví", "María Pinto", "San Pedro", "Talagante", "El Monte", "Isla de Maipo", "Padre Hurtado", "Peñaflor"] }
                        ],
                        get availableCities() {
                            let regionData = this.dataset.find(r => r.region === this.searchRegion);
                            return regionData ? regionData.comunas : [];
                        },

                        get filteredRegions() {
                            if (this.searchRegion === '') {
                                return this.dataset.map(item => item.region);
                            }
                            return this.dataset
                                .map(item => item.region)
                                .filter(region => region.toLowerCase().includes(this.searchRegion.toLowerCase()));
                        },
                        get filteredCities() {
                            let validRegion = this.dataset.find(item => item.region === this.searchRegion);
                            let possibleCities = validRegion ? validRegion.comunas : this.dataset.flatMap(item => item.comunas);
                            
                            if (this.searchCity === '') {
                                return validRegion ? possibleCities : [];
                            }
                            
                            return possibleCities
                                .filter(city => city.toLowerCase().includes(this.searchCity.toLowerCase()));
                        },
                        selectRegion(region) {
                            this.searchRegion = region;
                            this.openRegion = false;
                            let regionData = this.dataset.find(r => r.region === region);
                            if (regionData && !regionData.comunas.includes(this.searchCity)) {
                                this.searchCity = '';
                            }
                        },
                        selectCity(city) {
                            this.searchCity = city;
                            this.openCity = false;
                            
                            if (!this.dataset.find(r => r.region === this.searchRegion)) {
                                let foundRegion = this.dataset.find(r => r.comunas.includes(city));
                                if (foundRegion) {
                                    this.searchRegion = foundRegion.region;
                                }
                            }
                        }
                    }
                }
            </script>
        </div>
    </div>
</x-app-layout>
