<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-black text-2xl text-white leading-tight tracking-tight uppercase">
                {{ __('Reportes de Rendiciones y Viáticos') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-indigo-500/20">Finanzas / Auditoría</span>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Generación de Reportes Financieros</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#0f172a] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Filters Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                    Filtros de Búsqueda
                </h3>

                <form method="GET" action="{{ route('renditions.reports') }}" class="space-y-6" id="reports-filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        
                        <!-- Colaborador -->
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Colaborador</label>
                            <div class="flex items-center border border-slate-850 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-indigo-500 transition-all">
                                <select name="user_id" class="w-full bg-transparent border-none text-white text-xs font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900" onchange="document.getElementById('reports-filter-form').submit()">
                                    <option value="">Todos los colaboradores</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} {{ $u->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Mes -->
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Mes</label>
                            <div class="flex items-center border border-slate-850 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-indigo-500 transition-all">
                                <select name="month" class="w-full bg-transparent border-none text-white text-xs font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900" onchange="document.getElementById('reports-filter-form').submit()">
                                    <option value="">Todos los meses</option>
                                    @php
                                        $months = [
                                            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                        ];
                                    @endphp
                                    @foreach($months as $val => $label)
                                        <option value="{{ $val }}" {{ request('month') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Año -->
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Año</label>
                            <div class="flex items-center border border-slate-850 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-indigo-500 transition-all">
                                <select name="year" class="w-full bg-transparent border-none text-white text-xs font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900" onchange="document.getElementById('reports-filter-form').submit()">
                                    <option value="">Todos los años</option>
                                    @for($y = 2020; $y <= now()->year + 5; $y++)
                                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Estado</label>
                            <div class="flex items-center border border-slate-850 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-indigo-500 transition-all">
                                <select name="status" class="w-full bg-transparent border-none text-white text-xs font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900" onchange="document.getElementById('reports-filter-form').submit()">
                                    <option value="">Todos los estados</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Borrador</option>
                                    <option value="pending_jefatura" {{ request('status') === 'pending_jefatura' ? 'selected' : '' }}>Pendiente Jefatura</option>
                                    <option value="pending_controlling" {{ request('status') === 'pending_controlling' ? 'selected' : '' }}>Pendiente Controlling</option>
                                    <option value="pending_finances" {{ request('status') === 'pending_finances' ? 'selected' : '' }}>Pendiente Finanzas</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobado / Cerrado</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazado</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tipo Viaje -->
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Tipo Viaje</label>
                            <div class="flex items-center border border-slate-850 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-indigo-500 transition-all">
                                <select name="trip_type" class="w-full bg-transparent border-none text-white text-xs font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900" onchange="document.getElementById('reports-filter-form').submit()">
                                    <option value="">Todos los tipos</option>
                                    <option value="terreno" {{ request('trip_type') === 'terreno' ? 'selected' : '' }}>Terreno</option>
                                    <option value="reunion" {{ request('trip_type') === 'reunion' ? 'selected' : '' }}>Reunión</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-800/60">
                        <a href="{{ route('renditions.reports') }}" class="px-6 py-3 bg-slate-800/80 border border-slate-700/80 text-slate-300 hover:text-white hover:border-rose-500/60 hover:bg-rose-500/10 rounded-xl text-xs font-black uppercase tracking-widest transition-all cursor-pointer flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar Filtros
                        </a>
                        
                        <button type="submit" class="px-7 py-3 bg-slate-800 border border-slate-700 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-700 transition-all cursor-pointer">
                            Filtrar
                        </button>

                        <a href="{{ route('renditions.reports.export', request()->all()) }}" class="px-7 py-3 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-600/20 hover:bg-emerald-500 hover:-translate-y-0.5 transition-all cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Exportar a Excel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative">
                <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-900/50">
                    <h3 class="text-sm font-black text-white tracking-tight uppercase">Resultados de Búsqueda</h3>
                    <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[10px] font-black uppercase tracking-widest rounded-lg">
                        {{ $plannings->total() }} Solicitudes Encontradas
                    </span>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-16 text-center">
                        <div class="mx-auto w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-white">No se encontraron registros</h3>
                        <p class="mt-2 text-sm text-slate-500 font-medium max-w-xs mx-auto">Pruebe ajustando los filtros de búsqueda para encontrar registros históricos.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-800">
                            <thead class="bg-slate-950/30">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">ID</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Destinos</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Fecha Viaje</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Presupuesto</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Rendido</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado RP</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado RND</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                @php
                                    $statusTranslations = [
                                        'draft' => 'Borrador',
                                        'pending_jefatura' => 'Pendiente Jefatura',
                                        'pending_controlling' => 'Pendiente Controlling',
                                        'pending_finances' => 'Pendiente Finanzas',
                                        'approved' => 'Aprobado',
                                        'rejected' => 'Rechazado',
                                        'completed' => 'Completado',
                                        'payment_completed' => 'Pago Realizado',
                                        'closed' => 'Cerrado',
                                    ];
                                @endphp
                                @foreach($plannings as $plan)
                                    <tr class="hover:bg-slate-800/20 transition-all duration-200">
                                        <!-- ID -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <span class="font-mono text-xs font-bold text-blue-400 bg-blue-500/10 px-2.5 py-1 rounded-md border border-blue-500/20">
                                                REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>

                                        <!-- Colaborador -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <img class="h-8 w-8 rounded-lg ring-1 ring-slate-800 object-cover" src="{{ $plan->user->profile_photo_path ? asset('storage/' . $plan->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($plan->user->name . ' ' . $plan->user->last_name) . '&color=93C5FD&background=1e293b&bold=true&size=64' }}" alt="{{ $plan->user->name }}">
                                                <div>
                                                    <div class="text-sm font-semibold text-white">{{ $plan->user->name }} {{ $plan->user->last_name }}</div>
                                                    <div class="text-[10px] text-slate-500 font-bold uppercase">{{ $plan->user->departamento }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Destinos -->
                                        <td class="px-6 py-5">
                                            <div class="text-xs font-bold text-white max-w-[200px] truncate" title="{{ $plan->destination }}">{{ $plan->destination }}</div>
                                            @if(!empty($plan->destinations))
                                                <div class="text-[9px] text-indigo-400 font-bold mt-1 uppercase tracking-tighter">
                                                    + {{ count($plan->destinations) }} destinos adicionales
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Fecha Viaje -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @if($plan->start_date)
                                                <div class="text-xs font-black text-blue-300">
                                                    {{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }}
                                                </div>
                                                @if($plan->end_date && $plan->end_date !== $plan->start_date)
                                                    <div class="text-[9px] text-slate-400 font-bold mt-0.5">
                                                        al {{ \Carbon\Carbon::parse($plan->end_date)->format('d/m/Y') }}
                                                    </div>
                                                @endif
                                                <div class="text-[9px] text-indigo-400/70 font-bold mt-0.5 uppercase">
                                                    {{ \Carbon\Carbon::parse($plan->start_date)->translatedFormat('M Y') }}
                                                </div>
                                            @else
                                                <span class="text-[10px] text-slate-600 font-bold italic">Sin fecha</span>
                                            @endif
                                        </td>

                                        <!-- Presupuesto -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @php
                                                $assigned = ($plan->requested_funds ?? 0) + ($plan->amipass_amount ?? 0);
                                            @endphp
                                            <div class="text-xs font-black text-white">
                                                ${{ number_format($assigned, 0, ',', '.') }}
                                            </div>
                                            <div class="text-[9px] text-slate-500 font-bold mt-0.5">
                                                Fondos: ${{ number_format($plan->requested_funds ?? 0, 0, ',', '.') }} / Ami: ${{ number_format($plan->amipass_amount ?? 0, 0, ',', '.') }}
                                            </div>
                                        </td>

                                        <!-- Rendido -->
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @if($plan->rendition)
                                                <div class="text-xs font-black text-emerald-400">
                                                    ${{ number_format($plan->rendition->total_declared, 0, ',', '.') }}
                                                </div>
                                                @php
                                                    $diff = $plan->rendition->funds_received - $plan->rendition->total_declared;
                                                @endphp
                                                <div class="text-[9px] font-bold mt-0.5 {{ $diff >= 0 ? 'text-emerald-500' : 'text-amber-500' }}">
                                                    {{ $diff >= 0 ? 'Sobró: ' : 'Faltó: ' }}${{ number_format(abs($diff), 0, ',', '.') }}
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-600 font-bold italic">Sin rendición</span>
                                            @endif
                                        </td>

                                        <!-- Estado RP -->
                                        <td class="px-6 py-5 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-[10px] font-black rounded-lg
                                                @if($plan->status === 'approved')
                                                    bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                                @elseif($plan->status === 'rejected')
                                                    bg-red-500/10 text-red-400 border border-red-500/20
                                                @else
                                                    bg-amber-500/10 text-amber-400 border border-amber-500/20
                                                @endif
                                            ">
                                                {{ $statusTranslations[$plan->status] ?? ucfirst(str_replace('_', ' ', $plan->status)) }}
                                            </span>
                                        </td>

                                        <!-- Estado RND -->
                                        <td class="px-6 py-5 text-center whitespace-nowrap">
                                            @if($plan->rendition)
                                                <span class="px-2.5 py-1 text-[10px] font-black rounded-lg
                                                    @if($plan->rendition->status === 'approved')
                                                        bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                                    @elseif($plan->rendition->status === 'rejected')
                                                        bg-red-500/10 text-red-400 border border-red-500/20
                                                    @else
                                                        bg-blue-500/10 text-blue-400 border border-blue-500/20
                                                    @endif
                                                ">
                                                    {{ $statusTranslations[$plan->rendition->status] ?? ucfirst(str_replace('_', ' ', $plan->rendition->status)) }}
                                                </span>
                                            @else
                                                <span class="text-[10px] text-slate-600 font-bold uppercase tracking-tighter">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/20">
                        {{ $plannings->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
