<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mis Solicitudes de Planificación') }}
            </h2>
            <a href="{{ route('route-plannings.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-[#1e293b] transition-all duration-200 hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva Solicitud
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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

            @if (session('error'))
                <div class="mb-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 px-4 py-3 rounded-xl relative text-sm font-bold">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                
                @if($plannings->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-500 dark:text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">No tienes solicitudes</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-slate-400 max-w-sm mx-auto">Comienza creando tu primera planificación de ruta para gestionar tus viajes y viáticos.</p>
                        <div class="mt-8">
                            <a href="{{ route('route-plannings.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                Crear Planificación
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50/50 dark:bg-slate-800/50">
                                <tr>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        ID / Creado
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Detalles del Viaje
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Fechas
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Requerimientos
                                    </th>
                                    <th scope="col" class="px-6 py-5 text-left text-[11px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-widest">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Historial
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-[#1e293b] divide-y divide-gray-100 dark:divide-slate-700/40">
                                @foreach ($plannings as $plan)
                                    <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-800/80 transition-colors duration-200 group cursor-pointer">
                                        <!-- ID y Fecha -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="flex flex-col items-start gap-2">
                                                <span class="font-mono text-[13px] text-blue-600 dark:text-blue-400 font-bold bg-blue-50 dark:bg-blue-500/10 px-2.5 py-1 rounded-md border border-blue-100 dark:border-blue-500/20 shadow-sm group-hover:scale-105 transition-transform origin-left">#REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                <div class="text-[11px] text-gray-400 dark:text-slate-500 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $plan->created_at->format('d M, Y H:i') }}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Detalles del Viaje -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0 h-9 w-9 rounded-lg flex items-center justify-center mt-0.5 {{ $plan->trip_type === 'terreno' ? 'bg-amber-500/10 text-amber-500 ring-1 ring-amber-500/20' : 'bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20' }}">
                                                    @if($plan->trip_type === 'terreno')
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $plan->destination }}</div>
                                                    @if($plan->region)
                                                        <div class="flex items-center gap-1 text-[11px] text-slate-400 mt-0.5">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                            {{ $plan->region }}
                                                        </div>
                                                    @endif
                                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1 max-w-[220px]" title="{{ $plan->motive }}">{{ $plan->motive }}</div>
                                                    @if($plan->companions)
                                                        <div class="flex items-center gap-1 mt-0.5 text-[11px] text-blue-400">
                                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            <span class="line-clamp-1 max-w-[180px]" title="{{ $plan->companions }}">{{ $plan->companions }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Fechas -->
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                            <div class="mt-2 flex flex-col items-start gap-2">
                                                <a href="{{ route('route-plannings.pdf', $plan->id) }}"
                                                target="_blank"
                                                class="px-2.5 py-1 inline-flex items-center text-[10px] font-semibold rounded-md border border-slate-600 text-slate-400 hover:bg-slate-700 hover:text-white transition-all">
                                                    PDF planificación
                                                </a>

                                                @if($plan->status === 'approved' && $plan->rendition)
                                                    <a href="{{ route('renditions.show', $plan->rendition->id) }}"
                                                    wire:navigate
                                                    class="px-2.5 py-1 inline-flex items-center text-[10px] font-semibold rounded-md border border-blue-500/30 text-blue-400 hover:bg-blue-500 hover:text-white transition-all">
                                                        Ir a rendición
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            @if($plan->workflowHistories->isEmpty())
                                                <span class="text-xs text-gray-400">Sin movimientos</span>
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
                                                            <div class="font-semibold text-gray-800 dark:text-gray-100">
                                                                {{ $history->user->name ?? 'Sistema' }}
                                                            </div>

                                                            <div class="text-gray-500 dark:text-gray-400">
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

                                                            <div class="text-gray-400">
                                                                {{ $history->created_at->format('d/m/Y H:i') }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $plannings->links() }}
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</x-app-layout>
