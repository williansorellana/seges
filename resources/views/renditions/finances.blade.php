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

    <div class="py-12 bg-[#0f172a] min-h-screen">
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

            <!-- 1. TESORERÍA Y FONDOS -->
            <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-600/5 rounded-full blur-[80px] pointer-events-none"></div>
                
                <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/50 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 shadow-inner">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white tracking-tight">Tesorería y Fondos</h3>
                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">Autorización y liberación de viáticos</p>
                        </div>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-20 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mb-6 border border-slate-700 shadow-inner opacity-50">
                            <svg class="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                        </div>
                        <h3 class="text-lg font-black text-white uppercase tracking-tight">Sin salidas pendientes</h3>
                        <p class="mt-2 text-xs text-slate-500 font-bold uppercase tracking-wide">No hay requerimientos financieros por autorizar.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-950/50">
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">ID y Solicitante</th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Motivo / Destino</th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Total a Liberar</th>
                                    <th class="px-8 py-6 text-center text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Gestión</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                @foreach ($plannings as $plan)
                                    <tr class="group hover:bg-slate-800/30 transition-all duration-300" x-data="{ showReject: false }">
                                        
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col gap-3">
                                                <span class="font-black text-[11px] text-blue-400 bg-blue-500/10 px-3 py-1 rounded-xl border border-blue-500/20 shadow-inner self-start tracking-tight">REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                                <div class="flex items-center gap-3">
                                                    <div class="relative">
                                                        <img class="h-10 w-10 rounded-xl ring-2 ring-slate-800 object-cover" src="{{ $plan->user->profile_photo_path ? asset('storage/' . $plan->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($plan->user->name . ' ' . $plan->user->last_name) . '&color=10B981&background=0f172a&bold=true&size=64' }}" alt="{{ $plan->user->name }}">
                                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-slate-900 flex items-center justify-center">
                                                            <svg class="w-2 h-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-black text-white tracking-tight">{{ $plan->user->name }} {{ $plan->user->last_name }}</div>
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
                                                        <div class="text-[9px] text-emerald-500 font-black uppercase tracking-widest flex items-center mt-0.5">{{ $approvalLabel }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-8 py-6">
                                            <div class="text-sm font-black text-white mb-1 tracking-tight truncate max-w-xs">{{ $plan->motive }}</div>
                                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest flex items-center gap-2">
                                                <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                {{ $plan->destination }}
                                            </div>
                                            <div class="flex items-center gap-2 text-[10px] text-blue-500/80 font-black uppercase tracking-tighter mt-2">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($plan->end_date)->format('d/m/Y') }}
                                            </div>
                                        </td>

                                        <td class="px-8 py-6 whitespace-nowrap">
                                            <div class="flex flex-col gap-2">
                                                @if($plan->requires_funds)
                                                    <div class="text-[16px] font-black text-emerald-400 bg-emerald-500/5 px-4 py-2 rounded-2xl border border-emerald-500/10 shadow-inner inline-flex items-center tracking-tighter">
                                                        ${{ number_format($plan->requested_funds, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                                
                                                @if($plan->requires_amipass)
                                                    <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">
                                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                        Amipass: {{ $plan->amipass_days }} d
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-8 py-6 text-center">
                                            <div class="flex items-center justify-center gap-3 relative">
                                                <div x-show="!showReject" class="flex gap-3 transition-all">
                                                    <form action="{{ route('route-plannings.approve-finances', $plan->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-6 py-3 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-500 hover:-translate-y-1 transition-all cursor-pointer">
                                                            Liberar Fondos
                                                        </button>
                                                    </form>
                                                    
                                                    <button @click="showReject = true" class="px-6 py-3 bg-slate-800 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-slate-700 hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all cursor-pointer">
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
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($plannings->hasPages())
                        <div class="px-8 py-6 border-t border-slate-800 bg-slate-950/20">
                            {{ $plannings->links() }}
                        </div>
                    @endif
                @endif
            </div>

            <!-- 2. CIERRE CONTABLE RENDICIONES -->
            <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative">
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-600/5 rounded-full blur-[80px] pointer-events-none"></div>
                
                <div class="px-8 py-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/50 backdrop-blur-md">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 shadow-inner">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white tracking-tight">Cierre Contable de Rendiciones</h3>
                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">Conciliación final de gastos rendidos</p>
                        </div>
                    </div>
                </div>

                @if(isset($renditions) && $renditions->isEmpty())
                    <div class="p-20 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-3xl flex items-center justify-center mb-6 border border-slate-700 shadow-inner opacity-50">
                            <svg class="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-lg font-black text-white uppercase tracking-tight">Sin cierres pendientes</h3>
                        <p class="mt-2 text-xs text-slate-500 font-bold uppercase tracking-wide">No hay rendiciones pendientes de conciliación final.</p>
                    </div>
                @elseif(isset($renditions))
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-950/50">
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">ID y Colaborador</th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Destino / Motivo</th>
                                    <th class="px-8 py-6 text-left text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Conciliación Bancaria</th>
                                    <th class="px-8 py-6 text-center text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Acción Final</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                @foreach($renditions as $ren)
                                <tr x-data="{ showReject: false }" class="group hover:bg-slate-800/30 transition-all duration-300">
                                    
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-3">
                                            <span class="font-black text-[11px] text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-xl border border-indigo-500/20 shadow-inner self-start tracking-tight">RND-{{ str_pad($ren->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            <div class="flex items-center gap-3">
                                                <img class="h-10 w-10 rounded-xl ring-2 ring-slate-800 object-cover" src="{{ $ren->user->profile_photo_path ? asset('storage/' . $ren->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($ren->user->name . ' ' . $ren->user->last_name) . '&color=818CF8&background=0f172a&bold=true&size=64' }}" alt="{{ $ren->user->name }}">
                                                <div>
                                                    <div class="text-sm font-black text-white tracking-tight">{{ $ren->user->name }} {{ $ren->user->last_name }}</div>
                                                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mt-0.5">{{ $ren->user->departamento ?? 'Corporativo' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-white mb-1 tracking-tight">{{ $ren->routePlanning->motive }}</div>
                                        <div class="text-[10px] text-slate-500 font-black uppercase tracking-widest flex items-center gap-2 truncate max-w-[200px]">
                                            <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            {{ $ren->routePlanning->destination }}
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex flex-col gap-2">
                                            <div class="flex justify-between max-w-[180px]">
                                                <span class="text-[9px] text-slate-600 font-black uppercase tracking-tighter">Asignado:</span>
                                                <span class="text-[11px] text-slate-400 font-black tracking-tight">${{ number_format($ren->funds_received, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between max-w-[180px]">
                                                <span class="text-[9px] text-slate-600 font-black uppercase tracking-tighter">Rendido:</span>
                                                <span class="text-[11px] text-blue-400 font-black tracking-tight">${{ number_format($ren->total_declared, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="mt-2 px-3 py-1.5 rounded-xl border {{ $ren->funds_received - $ren->total_declared < 0 ? 'bg-amber-500/5 border-amber-500/20 text-amber-500' : 'bg-emerald-500/5 border-emerald-500/20 text-emerald-400' }} w-fit flex items-center gap-2">
                                                <span class="text-[9px] font-black uppercase tracking-widest">Diferencia:</span>
                                                <span class="text-[12px] font-black tracking-tighter">${{ number_format(abs($ren->funds_received - $ren->total_declared), 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <div class="flex items-center justify-center gap-3 relative">
                                            <div x-show="!showReject" class="flex gap-2 transition-all">
                                                <a href="{{ route('renditions.show', $ren->id) }}" target="_blank" class="px-5 py-2.5 bg-slate-800 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl border border-slate-700 hover:bg-slate-700 hover:text-white transition-all flex items-center gap-2 cursor-pointer shadow-lg shadow-black/20">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    Audit
                                                </a>
                                                <form action="{{ route('renditions.approve-finances-rendition', $ren->id) }}" method="POST">
                                                    @csrf 
                                                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl shadow-indigo-600/20 hover:bg-indigo-500 hover:-translate-y-1 transition-all cursor-pointer">
                                                        Cerrar
                                                    </button>
                                                </form>
                                                <button @click="showReject = true" class="p-2.5 bg-rose-600/10 text-rose-500 rounded-xl border border-rose-500/20 hover:bg-rose-500 hover:text-white transition-all cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>
                                            
                                            <!-- Formulario Rechazar -->
                                            <div x-show="showReject" x-cloak class="absolute right-0 top-0 w-72 bg-slate-900 p-5 rounded-[1.5rem] border border-rose-500/30 shadow-2xl z-20" x-transition>
                                                <form action="{{ route('renditions.reject-finances-rendition', $ren->id) }}" method="POST">
                                                    @csrf 
                                                    <label class="block text-[10px] font-black text-rose-500 mb-3 uppercase tracking-[0.2em] text-left">Motivo de Devolución</label>
                                                    <textarea name="observation" rows="3" class="w-full text-xs bg-slate-950 border-slate-800 text-white rounded-xl focus:ring-rose-500 focus:border-rose-500 placeholder-slate-700 font-bold" required placeholder="Error detectado..."></textarea>
                                                    <div class="mt-4 flex justify-end gap-3">
                                                        <button type="button" @click="showReject = false" class="px-4 py-2 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-white transition-colors cursor-pointer">Cerrar</button>
                                                        <button type="submit" class="px-5 py-2 bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-rose-600/20 hover:bg-rose-500 hover:-translate-y-0.5 transition-all cursor-pointer">Devolver</button>
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
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

