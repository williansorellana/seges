<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                {{ __('Mis Rendiciones') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-blue-500/20">Panel Principal</span>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Gestión de Gastos y Viáticos</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
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

            <!-- Tarjeta Informativa Superior - Premium Edition -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="p-2.5 bg-blue-500/10 text-blue-400 rounded-xl ring-1 ring-blue-500/20 flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="text-center md:text-left">
                            <h3 class="text-xl font-bold text-white tracking-tight uppercase">Centro de Rendiciones</h3>
                            <p class="text-slate-400 max-w-4xl text-sm leading-relaxed mt-1 font-medium">
                                Gestione sus justificaciones de gastos de viaje de forma eficiente. Recuerde que la carga oportuna de sus documentos asegura una revisión rápida y un cierre exitoso de su solicitud.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Listado de Rendiciones -->
            <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5">
                @if($renditions->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-500 opacity-50">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">{{ __('Sin rendiciones activas') }}</h3>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Aún no tienes fondos entregados o viajes finalizados pendientes de justificar.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700/40">
                            <thead class="bg-slate-800/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Identificador') }}</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Destino / Fechas') }}</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Fondos Asignados') }}</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Total Rendido') }}</th>
                                    <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Estado') }}</th>
                                    <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/40">
                                @foreach ($renditions as $ren)
                                    <tr class="hover:bg-slate-800/60 transition-colors duration-200">
                                        
                                        <!-- ID -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="flex flex-col gap-1.5">
                                                <span class="font-mono text-[12px] text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded-md ring-1 ring-blue-500/20 self-start mt-1">RND-{{ str_pad($ren->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                <div class="text-[10px] text-slate-500 font-mono mt-1">REF-{{ str_pad($ren->route_planning_id, 4, '0', STR_PAD_LEFT) }}</div>
                                            </div>
                                        </td>
 
                                        <!-- Viaje -->
                                        <td class="px-6 py-5">
                                            <div class="text-sm font-semibold text-white mb-1 tracking-tight">
                                                {{ $ren->routePlanning->destination }}
                                            </div>
                                            <div class="flex items-center gap-1.5 text-[11px] text-blue-400 mt-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ \Carbon\Carbon::parse($ren->routePlanning->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($ren->routePlanning->end_date)->format('d/m/Y') }}
                                            </div>
                                        </td>
 
                                        <!-- Fondos Recibidos -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="text-sm font-bold text-emerald-400">
                                                ${{ number_format($ren->funds_received, 0, ',', '.') }}
                                            </div>
                                        </td>
 
                                        <!-- Total Rendido -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @if($ren->total_declared > 0)
                                                <span class="text-sm font-bold text-blue-400 tracking-tight">
                                                    ${{ number_format($ren->total_declared, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-500 italic">
                                                    {{ __('Pendiente') }}
                                                </span>
                                            @endif
                                        </td>
 
                                        <!-- Estado -->
                                        <td class="px-8 py-6 whitespace-nowrap">
                                            @php
                                                if ($ren->status === 'draft') {
                                                    $statusLabel = 'Borrador';
                                                    $statusClass = 'bg-slate-800 text-slate-400 border-slate-700';
                                                    $statusDot = 'bg-slate-500';
                                                    $statusIcon = null;
                                                } elseif (in_array($ren->status, ['pending_jefatura', 'pending_controlling', 'pending_finances'])) {
                                                    $statusLabel = 'En revisión';
                                                    $statusClass = 'bg-amber-500/10 text-amber-500 border-amber-500/20 shadow-amber-900/10';
                                                    $statusDot = 'bg-amber-500 animate-pulse';
                                                    $statusIcon = null;
                                                } elseif ($ren->status === 'approved' && $ren->payment_completed) {
                                                    $statusLabel = 'Cerrada';
                                                    $statusClass = 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20 shadow-emerald-900/10';
                                                    $statusDot = null;
                                                    $statusIcon = 'check';
                                                } elseif ($ren->status === 'approved' && !$ren->payment_completed) {
                                                    $statusLabel = 'Aprobada / cierre pendiente';
                                                    $statusClass = 'bg-amber-500/10 text-amber-500 border-amber-500/20 shadow-amber-900/10';
                                                    $statusDot = 'bg-amber-500 animate-pulse';
                                                    $statusIcon = null;
                                                } elseif ($ren->status === 'closed') {
                                                    $statusLabel = 'Cerrada';
                                                    $statusClass = 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20 shadow-emerald-900/10';
                                                    $statusDot = null;
                                                    $statusIcon = 'check';
                                                } elseif ($ren->status === 'rejected') {
                                                    $statusLabel = 'Observada';
                                                    $statusClass = 'bg-rose-500/10 text-rose-500 border-rose-500/20 shadow-rose-900/10';
                                                    $statusDot = null;
                                                    $statusIcon = 'warning';
                                                } else {
                                                    $statusLabel = ucfirst(str_replace('_', ' ', $ren->status));
                                                    $statusClass = 'bg-slate-800 text-slate-400 border-slate-700';
                                                    $statusDot = 'bg-slate-500';
                                                    $statusIcon = null;
                                                }
                                            @endphp

                                            <span class="px-4 py-2 inline-flex items-center text-[10px] font-black uppercase tracking-widest rounded-xl border shadow-sm {{ $statusClass }}">
                                                @if($statusIcon === 'check')
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @elseif($statusIcon === 'warning')
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                @elseif($statusDot)
                                                    <span class="w-2 h-2 rounded-full {{ $statusDot }} mr-2"></span>
                                                @endif

                                                {{ __($statusLabel) }}
                                            </span>

                                            @if($ren->status === 'rejected' && $ren->observations->isNotEmpty())
                                                <div class="mt-2 text-xs text-red-600 dark:text-red-400 max-w-xs whitespace-normal">
                                                    {{ $ren->observations->last()->observation }}
                                                </div>
                                            @endif

                                            @if($ren->status === 'approved' && !$ren->payment_completed)
                                                <div class="mt-2 text-[10px] text-amber-400 font-bold uppercase tracking-widest max-w-xs whitespace-normal">
                                                    Pendiente de confirmación financiera.
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-8 py-6 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-4">
                                                @if($ren->status === 'draft')
                                                    <a href="{{ route('renditions.show', $ren->id) }}" wire:navigate
                                                    class="group/btn px-6 py-2.5 bg-blue-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-600/20 hover:bg-blue-500 hover:-translate-y-1 hover:shadow-blue-600/40 transition-all flex items-center gap-2 cursor-pointer">
                                                        <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        {{ __('Rendir Gastos') }}
                                                    </a>
                                                @elseif($ren->status === 'rejected')
                                                    <a href="{{ route('renditions.show', $ren->id) }}" wire:navigate
                                                    class="group/btn px-6 py-2.5 bg-rose-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-rose-600/20 hover:bg-rose-500 hover:-translate-y-1 hover:shadow-rose-600/40 transition-all flex items-center gap-2 cursor-pointer">
                                                        <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        {{ __('Corregir') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('renditions.show', $ren->id) }}" wire:navigate
                                                    class="group/btn px-5 py-2.5 bg-slate-800 text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-slate-700 hover:bg-slate-700 hover:text-white transition-all cursor-pointer">
                                                        {{ __('Ver Detalles') }}
                                                    </a>

                                                    @if($ren->status === 'approved' || $ren->status === 'closed')
                                                        <a href="{{ route('renditions.pdf', $ren->id) }}"
                                                        class="group/btn px-5 py-2.5 bg-rose-600/10 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-rose-500/20 hover:bg-rose-600 hover:text-white transition-all flex items-center gap-2 cursor-pointer shadow-lg shadow-rose-900/10">
                                                            <svg class="w-4 h-4 group-hover/btn:translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            PDF
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($renditions->hasPages())
                        <div class="px-6 py-4 border-t border-slate-700/40">
                            {{ $renditions->links() }}
                        </div>
                    @endif
                @endif
 
            </div>
        </div>
    </div>
</x-app-layout>

