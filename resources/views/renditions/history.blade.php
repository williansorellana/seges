<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Historial de Rendiciones y Planificaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- 1. HISTORIAL DE PLANIFICACIONES -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                <div class="p-6 border-b border-slate-700/40 flex items-center gap-4">
                    <div class="p-2.5 bg-blue-500/10 text-blue-400 rounded-xl ring-1 ring-blue-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Historial de Solicitudes de Viaje</h3>
                        <p class="text-sm text-slate-400">Solicitudes que han sido aprobadas o rechazadas definitivamente.</p>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Sin registros de solicitudes</h3>
                        <p class="mt-1 text-sm text-slate-400">No hay historial disponible para mostrar.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Fecha / Folio</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Destino y Fechas</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Montos</th>
                                    <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Estado Final</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach($plannings as $plan)
                                <tr class="hover:bg-slate-800/60 transition-colors duration-200">
                                    
                                    <!-- Fecha / Folio -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="text-sm font-semibold text-white">{{ $plan->updated_at->format('d/m/Y') }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $plan->updated_at->format('H:i') }} hrs</div>
                                            <span class="font-mono text-[12px] text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded-md ring-1 ring-blue-500/20 self-start mt-1">REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </td>

                                    <!-- Colaborador -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $plan->user->profile_photo_path ? asset('storage/' . $plan->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($plan->user->name . ' ' . $plan->user->last_name) . '&color=93C5FD&background=1e293b&bold=true&size=64' }}" alt="{{ $plan->user->name }} {{ $plan->user->last_name }}">
                                            <div>
                                                <div class="text-sm font-semibold text-white">{{ $plan->user->name }} {{ $plan->user->last_name }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $plan->user->departamento ?? 'Sin departamento' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Destino -->
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-semibold text-white">{{ $plan->motive }}</div>
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
                                            $totalRequested = $requestedFunds + $amipassAmount;
                                        @endphp

                                        <div class="flex flex-col gap-1.5">
                                            <div class="text-sm font-black text-emerald-400">
                                                Total: ${{ number_format($totalRequested, 0, ',', '.') }}
                                            </div>

                                            <div class="text-[11px] text-slate-500">
                                                Fondos:
                                                <span class="text-slate-300 font-bold">
                                                    ${{ number_format($requestedFunds, 0, ',', '.') }}
                                                </span>
                                            </div>

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

                                    <!-- Estado -->
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            @if($plan->status === 'approved')
                                                <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Aprobado
                                                </span>
                                            @else
                                                <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg bg-red-500/10 text-red-400 ring-1 ring-red-500/20">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Rechazado
                                                </span>
                                            @endif

                                            <a href="{{ route('route-plannings.pdf', $plan->id) }}"
                                            target="_blank"
                                            class="px-3 py-1.5 border border-slate-600 text-slate-300 text-[11px] font-semibold rounded-lg hover:bg-slate-700 hover:text-white transition-all inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-700/40">
                        {{ $plannings->links() }}
                    </div>
                @endif
            </div>

            <!-- 2. HISTORIAL DE RENDICIONES -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                <div class="p-6 border-b border-slate-700/40 flex items-center gap-4">
                    <div class="p-2.5 bg-indigo-500/10 text-indigo-400 rounded-xl ring-1 ring-indigo-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Historial de Rendiciones de Gastos</h3>
                        <p class="text-sm text-slate-400">Rendiciones que completaron su ciclo contable.</p>
                    </div>
                </div>

                @if($renditions->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Sin registros de rendiciones</h3>
                        <p class="mt-1 text-sm text-slate-400">No hay historial disponible para mostrar.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Fecha Cierre / Folio</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">Conciliación / Cierre</th>
                                    <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">Estado y Documento</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach($renditions as $ren)
                                <tr class="hover:bg-slate-800/60 transition-colors duration-200">
                                    
                                    <!-- Fecha / Folio -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="text-sm font-semibold text-white">{{ $ren->updated_at->format('d/m/Y') }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $ren->updated_at->format('H:i') }} hrs</div>
                                            <span class="font-mono text-[12px] text-indigo-400 font-bold bg-indigo-500/10 px-2 py-0.5 rounded-md ring-1 ring-indigo-500/20 self-start mt-1">RND-{{ str_pad($ren->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </td>

                                    <!-- Colaborador -->
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <img class="h-8 w-8 rounded-lg ring-1 ring-slate-600 object-cover" src="{{ $ren->user->profile_photo_path ? asset('storage/' . $ren->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($ren->user->name . ' ' . $ren->user->last_name) . '&color=818CF8&background=1e293b&bold=true&size=64' }}" alt="{{ $ren->user->name }} {{ $ren->user->last_name }}">
                                            <div>
                                                <div class="text-sm font-semibold text-white">{{ $ren->user->name }} {{ $ren->user->last_name }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $ren->routePlanning->destination }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Montos / Conciliación -->
                                    <td class="px-6 py-5">
                                        @php
                                            $planning = $ren->routePlanning;
                                            $normalFunds = $planning->requested_funds ?? 0;
                                            $amipassAmount = $planning->amipass_amount ?? 0;
                                            $realDifference = $ren->difference ?? ($ren->funds_received - $ren->total_declared);
                                        @endphp

                                        <div class="flex flex-col gap-1">
                                            <div class="text-xs text-slate-500">
                                                Asignado:
                                                <span class="text-slate-300 font-medium">
                                                    ${{ number_format($ren->funds_received, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            <div class="text-[11px] text-slate-600">
                                                Fondos:
                                                <span class="text-slate-400 font-bold">
                                                    ${{ number_format($normalFunds, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            @if($planning->requires_amipass)
                                                <div class="text-[11px] text-slate-600">
                                                    Amipass:
                                                    <span class="text-emerald-400 font-bold">
                                                        ${{ number_format($amipassAmount, 0, ',', '.') }}
                                                    </span>
                                                    <span class="text-slate-500">
                                                        / {{ $planning->amipass_business_days ?? $planning->amipass_days }} día(s)
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="text-xs text-slate-500">
                                                Rendido:
                                                <span class="text-slate-300 font-medium">
                                                    ${{ number_format($ren->total_declared, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            @if($ren->status === 'approved')
                                                <div class="mt-2 text-xs">
                                                    @if($ren->refund_to_company)
                                                        <span class="text-emerald-400 font-bold">
                                                            Devuelve a empresa: ${{ number_format(abs($realDifference), 0, ',', '.') }}
                                                        </span>
                                                    @elseif($ren->refund_to_worker)
                                                        <span class="text-amber-400 font-bold">
                                                            Reembolso trabajador: ${{ number_format(abs($realDifference), 0, ',', '.') }}
                                                        </span>
                                                    @else
                                                        <span class="text-blue-400 font-bold">
                                                            Rendición exacta
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="mt-1 text-sm font-bold px-2.5 py-0.5 rounded-md w-fit ring-1 {{ $realDifference < 0 ? 'bg-amber-500/10 text-amber-400 ring-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20' }}">
                                                Saldo: ${{ number_format(abs($realDifference), 0, ',', '.') }}
                                            </div>

                                            @if($ren->payment_completed)
                                                <div class="mt-1 text-[10px] text-emerald-400 font-black uppercase tracking-widest">
                                                    Cierre financiero confirmado
                                                </div>
                                            @elseif($ren->status === 'approved')
                                                <div class="mt-1 text-[10px] text-amber-400 font-black uppercase tracking-widest">
                                                    Cierre financiero pendiente
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Estado y Doc -->
                                    <td class="px-6 py-5 text-center">
                                        @php
                                            if ($ren->status === 'approved' && $ren->payment_completed) {
                                                $renditionStatusLabel = 'Cerrada';
                                                $renditionStatusClass = 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/20';
                                                $renditionStatusIcon = 'approved';
                                            } elseif ($ren->status === 'approved' && !$ren->payment_completed) {
                                                $renditionStatusLabel = 'Aprobada / cierre pendiente';
                                                $renditionStatusClass = 'bg-amber-500/10 text-amber-400 ring-amber-500/20';
                                                $renditionStatusIcon = 'pending';
                                            } elseif ($ren->status === 'rejected') {
                                                $renditionStatusLabel = 'Rechazada';
                                                $renditionStatusClass = 'bg-red-500/10 text-red-400 ring-red-500/20';
                                                $renditionStatusIcon = 'rejected';
                                            } else {
                                                $renditionStatusLabel = ucfirst(str_replace('_', ' ', $ren->status));
                                                $renditionStatusClass = 'bg-slate-500/10 text-slate-400 ring-slate-500/20';
                                                $renditionStatusIcon = 'pending';
                                            }
                                        @endphp

                                        <div class="flex flex-col items-center gap-3">
                                            <span class="px-3 py-1.5 inline-flex items-center text-xs font-bold rounded-lg ring-1 {{ $renditionStatusClass }}">
                                                @if($renditionStatusIcon === 'approved')
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @elseif($renditionStatusIcon === 'rejected')
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6l4 2"></path>
                                                    </svg>
                                                @endif

                                                {{ $renditionStatusLabel }}
                                            </span>

                                            <a href="{{ route('renditions.show', $ren->id) }}"
                                            target="_blank"
                                            class="px-3 py-1.5 border border-slate-600 text-slate-300 text-[11px] font-semibold rounded-lg hover:bg-slate-700 hover:text-white transition-all inline-flex items-center gap-1.5">
                                                Ver detalle
                                            </a>

                                            <a href="{{ route('renditions.pdf', $ren->id) }}"
                                            target="_blank"
                                            class="px-3 py-1.5 border border-slate-600 text-slate-300 text-[11px] font-semibold rounded-lg hover:bg-slate-700 hover:text-white transition-all inline-flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-700/40">
                        {{ $renditions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
