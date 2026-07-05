<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex flex-col gap-1">
                <h2 class="font-black text-2xl text-white leading-tight tracking-tight">
                    {{ __('Detalle de Rendición') }}
                </h2>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-indigo-500/20">ID: RND-{{ str_pad($rendition->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">{{ $rendition->created_at->format('d M, Y') }}</span>
                </div>
            </div>
            @php
                $currentUser = auth()->user();

                if ($currentUser->departamento === \App\Helpers\WorkflowHelper::DEPARTMENT_FINANCES) {
                    $backRoute = route('renditions.finances');
                    $backLabel = 'Volver a Finanzas';
                } elseif ($currentUser->departamento === \App\Helpers\WorkflowHelper::DEPARTMENT_CONTROLLING) {
                    $backRoute = route('renditions.controlling');
                    $backLabel = 'Volver a Controlling';
                } elseif ($currentUser->role === \App\Helpers\WorkflowHelper::ROLE_JEFATURA) {
                    $backRoute = route('renditions.approvals');
                    $backLabel = 'Volver a Jefatura';
                } else {
                    $backRoute = route('renditions.index');
                    $backLabel = 'Volver a mis rendiciones';
                }
            @endphp

            <div class="flex items-center gap-3">
                <a href="{{ route('renditions.pdf', $rendition->id) }}"
                target="_blank"
                class="px-5 py-2.5 bg-rose-600 text-white text-[11px] font-black uppercase tracking-[0.1em] rounded-xl border border-rose-500 hover:bg-rose-500 transition-all hover:-translate-y-0.5 flex items-center gap-2 shadow-lg shadow-rose-600/30">
                    PDF Rendición
                </a>

                <a href="{{ route('route-plannings.pdf', $rendition->routePlanning->id) }}"
                target="_blank"
                class="px-5 py-2.5 bg-rose-600 text-white text-[11px] font-black uppercase tracking-[0.1em] rounded-xl border border-rose-500 hover:bg-rose-500 transition-all hover:-translate-y-0.5 flex items-center gap-2 shadow-lg shadow-rose-600/30">
                    PDF Planificación
                </a>

                <a href="{{ $backRoute }}"
                class="px-5 py-2.5 bg-blue-600 text-white text-[11px] font-black uppercase tracking-[0.1em] rounded-xl border border-blue-500 hover:bg-blue-500 transition-all hover:-translate-y-0.5 flex items-center gap-2 shadow-lg shadow-blue-600/30 group">
                    <svg class="w-4 h-4 text-blue-200 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ $backLabel }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#0f172a] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Import Flatpickr -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
            <style>
                .flatpickr-calendar.dark { background: #0f172a; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid #1e293b; border-radius: 12px; }
                .flatpickr-day.selected { background: #3b82f6 !important; border-color: #3b82f6 !important; }
            </style>

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900 border border-emerald-500/30 rounded-2xl shadow-2xl p-4 flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-white uppercase tracking-tight">¡Éxito!</p>
                        <p class="text-xs text-slate-400 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-8 bg-rose-500/5 border border-rose-500/20 rounded-3xl p-6 shadow-2xl">
                    <h3 class="text-sm font-black text-rose-400 flex items-center gap-2 mb-4 uppercase tracking-widest">
                        Error al enviar rendición
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
                $isOwner = $rendition->user_id === auth()->id();

                $isControlling = auth()->user()->departamento === \App\Helpers\WorkflowHelper::DEPARTMENT_CONTROLLING;

                $canManageExpenses = $isOwner && in_array($rendition->status, ['draft', 'rejected']);

                $canAuditExpenses = $isControlling
                    && $rendition->status === 'pending_controlling'
                    && !$isOwner
                    && !($isLocked ?? false);
            @endphp

            @if($rendition->status === 'rejected' && $rendition->observations->count() > 0)
                <div class="mb-8 bg-rose-500/5 border border-rose-500/20 rounded-3xl p-6 shadow-2xl relative overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-rose-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                    <h3 class="text-sm font-black text-rose-400 flex items-center gap-2 mb-6 uppercase tracking-widest">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center border border-rose-500/20">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        Observaciones de Revisión
                    </h3>
                    <div class="space-y-4">
                        @foreach($rendition->observations as $obs)
                            <div class="bg-slate-900/50 p-4 rounded-2xl border border-slate-800/50 flex flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-[11px] text-white uppercase tracking-tight">{{ $obs->user->name }}</span> 
                                    <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                    <span class="text-[10px] text-slate-500 font-bold uppercase">{{ $obs->created_at->format('d/m, H:i') }}</span>
                                </div>
                                <span class="text-sm text-slate-300 font-medium leading-relaxed">{{ $obs->observation }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($isLocked ?? false)
                <div class="mb-8 bg-amber-500/10 border border-amber-500/30 rounded-[2rem] p-6 shadow-2xl relative overflow-hidden flex items-center gap-4">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-amber-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20 shadow-inner flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-amber-400 uppercase tracking-widest">Tarea Bloqueada / En Auditoría</h3>
                        <p class="text-xs text-slate-300 font-medium mt-1">El auditor <span class="text-white font-bold">{{ $lockOwnerName }}</span> se encuentra trabajando en esta rendición. Las acciones de auditoría están temporalmente deshabilitadas para evitar conflictos de concurrencia.</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Columna Izquierda: Documentos (8 cols) -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Tarjeta de Documentos Justificativos -->
                    <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative">
                        <div class="px-8 py-6 border-b border-slate-800 flex justify-between items-center bg-slate-900/50 backdrop-blur-md sticky top-0 z-10">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-white tracking-tight">Documentos Justificativos</h3>
                                    <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest mt-0.5">Respaldos de gastos declarados</p>
                                </div>
                            </div>
                            <div class="px-4 py-1.5 bg-slate-800/80 rounded-full border border-slate-700/50 text-[10px] font-black text-slate-300 uppercase tracking-widest">
                                {{ $rendition->expenses->count() }} Archivos
                            </div>
                        </div>
                        
                        <div class="p-8">
                            @if($rendition->expenses->isEmpty())
                                <div class="py-16 text-center border-2 border-dashed border-slate-800 rounded-[2rem]">
                                    <div class="mx-auto w-20 h-20 bg-slate-800/50 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h3 class="text-lg font-black text-white">No hay gastos registrados</h3>
                                    <p class="mt-2 text-sm text-slate-500 font-medium">Use el formulario inferior para comenzar la carga.</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($rendition->expenses as $expense)
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-slate-800/20 rounded-3xl border border-slate-800/50 hover:border-blue-500/30 hover:bg-slate-800/40 transition-all group" x-data="{ showDeleteModal: false, showEditModal: false }">
                                            <div class="flex items-center gap-5">
                                                <div class="w-14 h-14 bg-slate-900 rounded-2xl flex items-center justify-center text-slate-500 group-hover:text-blue-400 transition-colors border border-slate-800 shadow-inner">
                                                    @if($expense->document_type === 'boleta')
                                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" /></svg>
                                                    @elseif($expense->document_type === 'factura')
                                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                                    @else
                                                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-base font-black text-white group-hover:text-blue-400 transition-colors uppercase tracking-tight">
                                                        {{ $expense->provider }}
                                                        @if($expense->document_type === 'factura' && $expense->provider_rut)
                                                            <span class="text-xs text-slate-400 font-bold lowercase"> (RUT: {{ $expense->provider_rut }})</span>
                                                        @endif
                                                    </p>
                                                    @if($expense->document_type === 'boleta' && $expense->justification)
                                                        <p class="text-xs text-slate-300 font-medium mt-0.5 italic">{{ $expense->justification }}</p>
                                                    @endif
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest bg-blue-500/10 px-2 py-0.5 rounded-md">{{ $expense->document_type }}</span>
                                                        @php
                                                            $categoryLabels = [
                                                                'bencina' => 'Bencina',
                                                                'peaje' => 'Peaje',
                                                                'estacionamiento_transbordador' => 'Estac./Transb.',
                                                                'alojamiento' => 'Alojamiento',
                                                                'comida' => 'Comida',
                                                                'otros' => 'Otros',
                                                            ];

                                                            $categoryLabel = $categoryLabels[$expense->expense_category] ?? 'Otros';
                                                        @endphp

                                                        <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-2 py-0.5 rounded-md">
                                                            {{ $categoryLabel }}
                                                        </span>
                                                        <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">{{ \Carbon\Carbon::parse($expense->date)->format('d M, Y') }}</span>
                                                        @if($expense->document_number) 
                                                            <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                                                            <span class="text-[10px] font-mono text-slate-400">#{{ $expense->document_number }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-row sm:flex-col items-center sm:items-end gap-3 sm:gap-2 mt-4 sm:mt-0">
                                                <p class="text-xl font-black text-white leading-none tracking-tight">${{ number_format($expense->amount, 0, ',', '.') }}</p>
                                                @if($canAuditExpenses || !$expense->is_valid)
                                                    <div class="flex flex-col items-end gap-1 mt-2 max-w-[260px]">
                                                        @if($expense->is_valid)
                                                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                                                Válido
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                                                Observado
                                                            </span>

                                                            @if($expense->rejection_reason)
                                                                <span class="text-[10px] text-rose-300 font-medium text-right leading-snug">
                                                                    {{ $expense->rejection_reason }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('renditions.expenses.attachment', $expense) }}" target="_blank" class="p-2 bg-indigo-600/10 text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-indigo-600/5" title="Ver Documento">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    </a>
                                                    @if($canAuditExpenses)
                                                        <form action="{{ route('renditions.expenses.validate', $expense->id) }}" method="POST">
                                                            @csrf
                                                            <button
                                                                type="submit"
                                                                class="p-2 bg-emerald-600/10 text-emerald-400 rounded-xl hover:bg-emerald-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-emerald-600/5"
                                                                title="Marcar como válido">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </form>

                                                        <div x-data="{ showObserveExpense: false }">
                                                            <button
                                                                type="button"
                                                                @click="showObserveExpense = true"
                                                                class="p-2 bg-rose-600/10 text-rose-500 rounded-xl hover:bg-rose-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-rose-600/5"
                                                                title="Observar documento">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>

                                                            <div
                                                                x-show="showObserveExpense"
                                                                x-cloak
                                                                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md"
                                                                x-transition>
                                                                <div
                                                                    class="bg-slate-900 border border-rose-500/30 rounded-[2rem] p-8 max-w-md w-full shadow-2xl relative overflow-hidden"
                                                                    @click.away="showObserveExpense = false">
                                                                    
                                                                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-rose-500/10 rounded-full blur-[60px] pointer-events-none"></div>

                                                                    <div class="relative z-10">
                                                                        <div class="flex items-center gap-4 mb-6">
                                                                            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center border border-rose-500/20">
                                                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008z" />
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a1.5 1.5 0 001.29 2.25h17.78A1.5 1.5 0 0022.18 18L13.71 3.86a1.5 1.5 0 00-2.42 0z" />
                                                                                </svg>
                                                                            </div>

                                                                            <div>
                                                                                <h3 class="text-xl font-black text-white uppercase tracking-tight">
                                                                                    Observar Documento
                                                                                </h3>
                                                                                <p class="text-[10px] text-rose-400 font-black uppercase tracking-[0.2em] mt-1">
                                                                                    Revisión Controlling
                                                                                </p>
                                                                            </div>
                                                                        </div>

                                                                        <form action="{{ route('renditions.expenses.invalidate', $expense->id) }}" method="POST">
                                                                            @csrf

                                                                            <label class="block text-[10px] font-black text-slate-500 mb-3 uppercase tracking-[0.2em]">
                                                                                Motivo de observación
                                                                            </label>

                                                                            <textarea
                                                                                name="rejection_reason"
                                                                                rows="4"
                                                                                class="w-full text-sm bg-slate-950 border border-slate-800 text-white rounded-2xl focus:ring-rose-500 focus:border-rose-500 placeholder-slate-700 font-bold"
                                                                                required
                                                                                placeholder="Ej: Documento ilegible, monto no coincide, falta información..."></textarea>

                                                                            <div class="mt-6 flex justify-end gap-3">
                                                                                <button
                                                                                    type="button"
                                                                                    @click="showObserveExpense = false"
                                                                                    class="px-6 py-3 text-xs font-black text-slate-500 uppercase tracking-widest hover:text-white transition-colors cursor-pointer">
                                                                                    Cancelar
                                                                                </button>

                                                                                <button
                                                                                    type="submit"
                                                                                    class="px-7 py-3 bg-rose-600 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-rose-600/20 hover:bg-rose-500 hover:-translate-y-0.5 transition-all cursor-pointer">
                                                                                    Observar
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($canManageExpenses)
                                                        <button type="button" @click="showEditModal = true" class="p-2 bg-blue-600/10 text-blue-400 rounded-xl hover:bg-blue-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-blue-600/5" title="Editar">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                        </button>
                                                        <button type="button" @click="showDeleteModal = true" class="p-2 bg-rose-600/10 text-rose-500 rounded-xl hover:bg-rose-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-rose-600/5" title="Eliminar">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($canManageExpenses)
                                            <!-- Edit Modal -->
                                            <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md" style="display: none;" x-transition>
                                                <div class="bg-slate-900 border border-slate-800 rounded-[2rem] p-8 max-w-xl w-full mx-auto shadow-2xl relative" @click.away="showEditModal = false">
                                                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                                                    <h3 class="text-xl font-black text-white mb-8 text-left flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                        </div>
                                                        Editar Documento
                                                    </h3>
                                                    <form action="{{ route('renditions.expenses.update', [$rendition->id, $expense->id]) }}" method="POST" enctype="multipart/form-data" class="text-left space-y-6" x-data="{ docType: '{{ $expense->document_type }}' }">
                                                        @csrf @method('PUT')
                                                        <div class="group">
                                                            <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Proveedor / Local</label>
                                                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 focus-within:bg-slate-950 transition-all">
                                                                <input type="text" name="provider" value="{{ $expense->provider }}" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" required>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-6">
                                                            <div class="group">
                                                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Fecha</label>
                                                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                                                    <input type="date" name="date" value="{{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}" class="w-full bg-transparent border-none outline-none text-white text-sm font-bold" required>
                                                                </div>
                                                            </div>
                                                            <div class="group">
                                                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Monto ($)</label>
                                                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                                                    <input type="number" name="amount" value="{{ $expense->amount }}" min="1" class="w-full bg-transparent border-none outline-none text-white text-sm font-bold" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-6">
                                                            <div class="group">
                                                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Tipo</label>
                                                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-blue-500 transition-all">
                                                                    <select name="document_type" x-model="docType" class="w-full bg-transparent border-none text-white text-sm font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900" required>
                                                                        <option value="boleta">Boleta</option>
                                                                        <option value="factura">Factura</option>
                                                                        <option value="vale">Vale</option>
                                                                        <option value="otro">Otro</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="group" x-show="docType === 'factura'" x-transition>
                                                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">RUT del Proveedor</label>
                                                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                                                    <input type="text" name="provider_rut" :required="docType === 'factura'" value="{{ $expense->provider_rut }}" class="w-full bg-transparent border-none outline-none text-white text-sm font-bold" placeholder="Ej: 76.123.456-7">
                                                                </div>
                                                            </div>

                                                            <div class="group" x-show="docType === 'boleta'" x-transition>
                                                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Justificación del Gasto</label>
                                                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                                                    <input type="text" name="justification" :required="docType === 'boleta'" value="{{ $expense->justification }}" class="w-full bg-transparent border-none outline-none text-white text-sm font-bold" placeholder="¿En qué se gastó? Ej: Almuerzo">
                                                                </div>
                                                            </div>

                                                            <div class="group">
                                                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">
                                                                    Concepto del Gasto
                                                                </label>

                                                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-blue-500 transition-all">
                                                                    <select
                                                                        name="expense_category"
                                                                        class="w-full bg-transparent border-none text-white text-sm font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900"
                                                                        required
                                                                    >
                                                                        <option value="bencina" {{ $expense->expense_category == 'bencina' ? 'selected' : '' }}>Bencina</option>
                                                                        <option value="peaje" {{ $expense->expense_category == 'peaje' ? 'selected' : '' }}>Peaje</option>
                                                                        <option value="estacionamiento_transbordador" {{ $expense->expense_category == 'estacionamiento_transbordador' ? 'selected' : '' }}>
                                                                            Estacionamiento / Transbordador
                                                                        </option>
                                                                        <option value="alojamiento" {{ $expense->expense_category == 'alojamiento' ? 'selected' : '' }}>Alojamiento</option>
                                                                        <option value="comida" {{ $expense->expense_category == 'comida' ? 'selected' : '' }}>Comida</option>
                                                                        <option value="otros" {{ $expense->expense_category == 'otros' ? 'selected' : '' }}>Otros</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="group">
                                                                <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Nº Documento</label>
                                                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                                                    <input type="text" name="document_number" value="{{ $expense->document_number }}" class="w-full bg-transparent border-none outline-none text-white text-sm font-bold" placeholder="Opcional">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="group">
                                                            <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Actualizar Archivo (Opcional)</label>
                                                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-all cursor-pointer bg-slate-950/50 border border-slate-800 rounded-2xl p-1.5" />
                                                        </div>
                                                        
                                                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-800">
                                                            <button type="button" @click="showEditModal = false" class="px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-white transition-colors cursor-pointer">Cancelar</button>
                                                            <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-600/20 hover:bg-blue-500 hover:-translate-y-0.5 transition-all cursor-pointer">Guardar Cambios</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md" style="display: none;" x-transition>
                                                <div class="bg-slate-900 border border-slate-800 rounded-[2rem] p-8 max-w-sm w-full mx-auto shadow-2xl relative overflow-hidden" @click.away="showDeleteModal = false">
                                                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-rose-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                                                    <div class="flex items-center gap-4 mb-6">
                                                        <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center border border-rose-500/20">
                                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </div>
                                                        <h3 class="text-xl font-black text-white leading-tight uppercase tracking-tight">Eliminar Documento</h3>
                                                    </div>
                                                    <p class="text-sm text-slate-400 mb-8 leading-relaxed font-medium">¿Eliminar el gasto de <span class="text-white font-black">{{ $expense->provider }}</span> por <span class="text-white font-black">${{ number_format($expense->amount, 0, ',', '.') }}</span>? Esta acción no se puede deshacer.</p>
                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" @click="showDeleteModal = false" class="px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-white transition-colors cursor-pointer">Cancelar</button>
                                                        <form action="{{ route('renditions.expenses.destroy', [$rendition->id, $expense->id]) }}" method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="px-8 py-3 bg-rose-600 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-rose-600/20 hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">Sí, Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($canManageExpenses)
                    <!-- Formulario de Nuevo Gasto -->
                    <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5 relative">
                        <div class="p-6 border-b border-slate-700/40 bg-[#1e293b] sticky top-0 z-10 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white tracking-tight">Agregar Nuevo Gasto</h3>
                        </div>
                        <div class="p-8">
                            <form 
                                action="{{ route('renditions.expenses.store', $rendition->id) }}"
                                method="POST"
                                enctype="multipart/form-data"
                                class="space-y-8"
                                x-data="{ loading: false, docType: 'boleta' }"
                                @submit="loading = true"
                            >
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    
                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Fecha del Gasto</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-4 py-2.5 focus-within:border-blue-500 transition-all">
                                            <input type="text" name="date" id="expense_date" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold cursor-pointer" required placeholder="Seleccionar fecha...">
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Proveedor / Local</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-4 py-2.5 focus-within:border-blue-500 transition-all">
                                            <input type="text" name="provider" placeholder="Ej: Restaurante El Paso" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" required>
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Tipo de Documento</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 overflow-hidden focus-within:border-blue-500 transition-all">
                                            <select name="document_type" x-model="docType" class="w-full bg-transparent border-none text-white text-sm font-bold py-2.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-800" required>
                                                <option value="boleta">Boleta</option>
                                                <option value="factura">Factura</option>
                                                <option value="vale">Vale de Peaje/Estacionamiento</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="group" x-show="docType === 'factura'" x-transition>
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">RUT del Proveedor</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-4 py-2.5 focus-within:border-blue-500 transition-all">
                                            <input type="text" name="provider_rut" :required="docType === 'factura'" placeholder="Ej: 76.123.456-7" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold">
                                        </div>
                                    </div>

                                    <div class="group" x-show="docType === 'boleta'" x-transition>
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Justificación del Gasto</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-4 py-2.5 focus-within:border-blue-500 transition-all">
                                            <input type="text" name="justification" :required="docType === 'boleta'" placeholder="¿En qué se gastó? Ej: Almuerzo en ruta" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold">
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">
                                            Concepto del Gasto
                                        </label>

                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 overflow-hidden focus-within:border-blue-500 transition-all">
                                            <select
                                                name="expense_category"
                                                class="w-full bg-transparent border-none text-white text-sm font-bold py-2.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-800"
                                                required
                                            >
                                                <option value="bencina">Bencina</option>
                                                <option value="peaje">Peaje</option>
                                                <option value="estacionamiento_transbordador">Estacionamiento / Transbordador</option>
                                                <option value="alojamiento">Alojamiento</option>
                                                <option value="comida">Comida</option>
                                                <option value="otros">Otros</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Nº Documento (Opcional)</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-4 py-2.5 focus-within:border-blue-500 transition-all">
                                            <input type="text" name="document_number" placeholder="Ej: 154822" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold">
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Monto Total ($)</label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-slate-900 px-4 py-2.5 focus-within:border-blue-500 transition-all relative">
                                            <span class="text-blue-400 font-bold mr-2">$</span>
                                            <input type="number" name="amount" min="1" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" placeholder="0" required>
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">Archivo Adjunto (Foto/PDF)</label>
                                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-all cursor-pointer bg-slate-900 border border-slate-700 rounded-lg p-1.5 focus:outline-none" required>
                                    </div>
                                    
                                </div>

                                <div class="pt-8 border-t border-slate-700 flex justify-end">
                                    <button type="submit" :disabled="loading" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-lg transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span x-show="!loading" class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Subir y Guardar Gasto
                                        </span>

                                        <span x-show="loading" class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            Procesando...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Columna Derecha: Panel Financiero (4 cols) -->
                <div class="lg:col-span-4 space-y-8">
                    
                    <!-- Botones para ver Historial y Firmas en Modales -->
                    <div class="flex gap-4">
                        <button type="button" x-data="" @click.prevent="$dispatch('open-modal', 'approvals-history-modal')" class="flex-1 py-3 px-4 bg-slate-900/50 hover:bg-slate-800/50 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all border border-slate-800/60 hover:border-slate-700/60 shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Historial
                        </button>
                        <button type="button" x-data="" @click.prevent="$dispatch('open-modal', 'digital-signatures-modal')" class="flex-1 py-3 px-4 bg-slate-900/50 hover:bg-slate-800/50 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all border border-slate-800/60 hover:border-slate-700/60 shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Firmas
                        </button>
                    </div>

                    <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5 p-6 relative">
                        <div class="relative z-10">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                Estado de Caja
                            </h3>
                            
                            <div class="space-y-6">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Fondos Recibidos</p>
                                    <p class="text-4xl font-bold text-white tracking-tighter">${{ number_format($rendition->funds_received, 0, ',', '.') }}</p>
                                </div>

                                <div class="pt-6 border-t border-slate-700">
                                    <div class="flex justify-between items-end mb-4">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Rendido</p>
                                        <p class="text-2xl font-bold text-white tracking-tighter">${{ number_format($rendition->total_declared, 0, ',', '.') }}</p>
                                    </div>
                                    
                                    @php
                                        $cashDifference = $rendition->funds_received - $rendition->total_declared;

                                        if ($cashDifference > 0) {
                                            $cashLabel = 'A devolver a empresa';
                                            $cashAmount = $cashDifference;
                                            $cashClass = 'text-emerald-400';
                                        } elseif ($cashDifference < 0) {
                                            $cashLabel = 'Reembolso al trabajador';
                                            $cashAmount = abs($cashDifference);
                                            $cashClass = 'text-amber-400';
                                        } else {
                                            $cashLabel = 'Sin saldo pendiente';
                                            $cashAmount = 0;
                                            $cashClass = 'text-blue-400';
                                        }
                                    @endphp
                                    <div class="p-4 rounded-3xl bg-slate-950/50 border border-slate-800/80 backdrop-blur-sm">
                                        <div class="flex justify-between items-center gap-4">
                                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                                {{ $cashLabel }}
                                            </p>

                                            <p class="text-lg font-black {{ $cashClass }}">
                                                @if($cashAmount > 0)
                                                    ${{ number_format($cashAmount, 0, ',', '.') }}
                                                @else
                                                    $0
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($rendition->status === 'approved')
                                    <div class="mt-4 p-4 rounded-xl bg-slate-900/50 border border-slate-700/50">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                                            Resultado final Finanzas
                                        </p>

                                        @if($rendition->refund_to_company)
                                            <p class="text-sm font-black text-emerald-400">
                                                Trabajador debe devolver a empresa:
                                                ${{ number_format(abs($rendition->difference), 0, ',', '.') }}
                                            </p>

                                        @elseif($rendition->refund_to_worker)
                                            <p class="text-sm font-black text-amber-400">
                                                Empresa debe reembolsar al trabajador:
                                                ${{ number_format(abs($rendition->difference), 0, ',', '.') }}
                                            </p>

                                        @else
                                            <p class="text-sm font-black text-blue-400">
                                                Rendición exacta. No existen saldos pendientes.
                                            </p>
                                        @endif

                                        @if($rendition->refund_resolved_at)
                                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter mt-2">
                                                Aprobado por Finanzas el {{ \Carbon\Carbon::parse($rendition->refund_resolved_at)->format('d/m/Y H:i') }}
                                            </p>
                                        @endif

                                        @if($rendition->status === 'approved')
                                            @if($rendition->payment_completed)
                                                <div class="mt-3 p-3 rounded-2xl bg-emerald-500/5 border border-emerald-500/20">
                                                    <p class="text-[10px] text-emerald-400 font-black uppercase tracking-widest">
                                                        Cierre financiero confirmado
                                                    </p>

                                                    @if($rendition->payment_completed_at)
                                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter mt-1">
                                                            Confirmado el {{ \Carbon\Carbon::parse($rendition->payment_completed_at)->format('d/m/Y H:i') }}
                                                        </p>
                                                    @endif

                                                    @if($rendition->payment_observation)
                                                        <p class="text-xs text-slate-300 font-medium mt-2 leading-relaxed">
                                                            {{ $rendition->payment_observation }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @elseif($rendition->refund_to_company || $rendition->refund_to_worker)
                                                <div class="mt-3 p-3 rounded-2xl bg-amber-500/5 border border-amber-500/20">
                                                    <p class="text-[10px] text-amber-400 font-black uppercase tracking-widest">
                                                        Cierre financiero pendiente
                                                    </p>

                                                    <p class="text-xs text-slate-400 font-medium mt-2 leading-relaxed">
                                                        Finanzas aún debe confirmar la devolución o reembolso correspondiente.
                                                    </p>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                                
                                @if($rendition->status === 'approved' && $rendition->refund_to_company && !$rendition->payment_completed)
                                    <div class="mt-6 pt-6 border-t border-slate-800/60">
                                        @if($isOwner)
                                            <div class="p-4 rounded-3xl bg-slate-950/40 border border-slate-800/80">
                                                <h4 class="text-[11px] font-black text-white uppercase tracking-wider mb-3">Comprobante de Devolución</h4>
                                                @if($rendition->transfer_proof_path)
                                                    <div class="flex items-center justify-between mb-4 bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-2xl">
                                                        <span class="text-xs text-emerald-400 font-bold">¡Comprobante adjuntado!</span>
                                                        <a href="{{ route('renditions.download-transfer-proof', $rendition->id) }}" class="text-xs text-blue-400 hover:underline font-bold">Descargar actual</a>
                                                    </div>
                                                @endif
                                                <form action="{{ route('renditions.upload-transfer-proof', $rendition->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                    @csrf
                                                    <div>
                                                        <label class="block text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-2">Subir nuevo comprobante (PDF, JPG, PNG)</label>
                                                        <input type="file" name="transfer_proof" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-all cursor-pointer bg-slate-950/50 border border-slate-800 rounded-2xl p-1.5" required>
                                                    </div>
                                                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                                                        Subir Comprobante
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            @if($rendition->transfer_proof_path)
                                                <div class="p-4 rounded-3xl bg-slate-950/40 border border-slate-800/80">
                                                    <h4 class="text-[11px] font-black text-white uppercase tracking-wider mb-2">Comprobante de Devolución</h4>
                                                    <p class="text-xs text-slate-400 mb-3">El colaborador ha adjuntado el comprobante de transferencia.</p>
                                                    <a href="{{ route('renditions.download-transfer-proof', $rendition->id) }}" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition-all inline-flex items-center justify-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Descargar Comprobante
                                                    </a>
                                                </div>
                                            @else
                                                <div class="p-4 rounded-3xl bg-slate-950/40 border border-slate-800/80">
                                                    <h4 class="text-[11px] font-black text-white uppercase tracking-wider mb-2">Comprobante de Devolución</h4>
                                                    <span class="text-xs text-rose-400 font-bold">El colaborador aún no ha subido el comprobante de transferencia.</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @elseif($rendition->status === 'approved' && $rendition->refund_to_company && $rendition->payment_completed && $rendition->transfer_proof_path)
                                    <div class="mt-6 pt-6 border-t border-slate-800/60">
                                        <div class="p-4 rounded-3xl bg-slate-950/40 border border-slate-800/80">
                                            <h4 class="text-[11px] font-black text-white uppercase tracking-wider mb-2">Comprobante de Devolución</h4>
                                            <a href="{{ route('renditions.download-transfer-proof', $rendition->id) }}" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all inline-flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Descargar Comprobante Adjunto
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                
                                </div>
                            </div>

                            <!-- Botón Enviar -->
                            @if($canManageExpenses)
                            <div class="mt-10 pt-8 border-t border-slate-800" x-data="{ showSubmitModal: false }">
                                <button type="button" @click="showSubmitModal = true" class="w-full py-4 px-6 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-600/20 hover:bg-emerald-500 hover:-translate-y-1 hover:shadow-emerald-600/40 transition-all flex items-center justify-center gap-3 cursor-pointer group">
                                    <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Terminar y Enviar
                                </button>
                                <p class="text-[10px] text-slate-500 text-center mt-2.5 font-bold uppercase tracking-tighter opacity-60 italic">Cierra la edición de esta rendición</p>

                                <!-- Submit Modal (Style inspired by Edit Modal) -->
                                <div x-show="showSubmitModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" style="display: none;" x-transition>
                                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 max-w-lg w-full mx-auto shadow-2xl relative" @click.away="showSubmitModal = false">
                                        <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                                        
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center border border-emerald-500/20">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-white uppercase tracking-tight">Enviar Rendición</h3>
                                                <p class="text-[10px] font-black text-emerald-500/80 uppercase tracking-widest mt-1">Confirmación Final</p>
                                            </div>
                                        </div>

                                        <p class="text-sm text-slate-400 mb-6 leading-relaxed font-medium">
                                            ¿Está seguro de enviar esta rendición?
                                            <span class="text-white font-semibold">Ya no podrá agregar ni modificar documentos</span>
                                            una vez que el proceso sea enviado a revisión.
                                        </p>

                                        <form 
                                            action="{{ route('renditions.submit', $rendition->id) }}"
                                            method="POST"
                                            x-data="{ loading: false }"
                                            @submit="loading = true"
                                            class="space-y-6"
                                        >
                                            @csrf

                                            <div>
                                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wider">
                                                    Observación adicional del trabajador
                                                </label>

                                                <textarea
                                                    name="user_observation"
                                                    rows="4"
                                                    maxlength="1000"
                                                    class="w-full bg-slate-900 border border-slate-700 rounded-lg text-sm text-white placeholder-slate-600 focus:border-emerald-500 focus:ring-emerald-500"
                                                    placeholder="Opcional. Ej: Se adjuntan boletas correspondientes al viaje realizado..."
                                                ></textarea>

                                                <p class="mt-2 text-[10px] text-slate-500 font-bold uppercase tracking-tighter">
                                                    Este comentario quedará registrado en el historial de la rendición.
                                                </p>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-4 border-t border-slate-700">
                                                <button
                                                    type="button"
                                                    @click="showSubmitModal = false"
                                                    class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors cursor-pointer"
                                                >
                                                    Cancelar
                                                </button>

                                                <button
                                                    type="submit"
                                                    :disabled="loading"
                                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-500 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                                >
                                                    <span x-show="!loading">
                                                        Aceptar y Enviar
                                                    </span>

                                                    <span x-show="loading" class="flex items-center gap-2">
                                                        <svg class="animate-spin h-4 w-4 text-white"
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25"
                                                                    cx="12"
                                                                    cy="12"
                                                                    r="10"
                                                                    stroke="currentColor"
                                                                    stroke-width="4">
                                                            </circle>
                                                            <path class="opacity-75"
                                                                  fill="currentColor"
                                                                  d="M4 12a8 8 0 018-8v8H4z">
                                                            </path>
                                                        </svg>
                                                        Procesando...
                                                    </span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info del Viaje Card -->
                    <div class="bg-white dark:bg-[#1e293b] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 dark:border-slate-700/60 ring-1 ring-black/5 dark:ring-white/5 p-6 relative">
                        <h4 class="text-xs font-bold text-slate-400 mb-6 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                            Información del Viaje
                        </h4>
                        <div class="space-y-8">
                            <div class="group">
                                <dt class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5 group-hover:text-indigo-400 transition-colors">Destino</dt>
                                <dd class="text-base font-black text-white leading-tight uppercase tracking-tight">{{ $rendition->routePlanning->destination }}</dd>
                            </div>
                            <div class="group">
                                <dt class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Motivo del Desplazamiento</dt>
                                <dd class="text-sm font-medium text-slate-400 leading-relaxed">{{ $rendition->routePlanning->motive }}</dd>
                            </div>
                            <div class="pt-6 border-t border-slate-800 flex justify-between items-center">
                                <div>
                                    <dt class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1">Asignación Amipass</dt>
                                    <dd class="mt-1">
                                        @if($rendition->routePlanning->requires_amipass)
                                            <div class="flex flex-col gap-1">
                                                <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-2 py-1 w-fit uppercase tracking-widest">
                                                    {{ $rendition->routePlanning->amipass_business_days ?? $rendition->routePlanning->amipass_days }} día(s)
                                                </span>

                                                <span class="text-sm font-black text-white tracking-tight">
                                                    ${{ number_format($rendition->routePlanning->amipass_amount ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter italic">
                                                No solicitado
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="text-right">
                                    <dt class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1">Solicitado por</dt>
                                    <dd class="text-[10px] font-black text-white uppercase tracking-tight">{{ $rendition->user->name }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div> <!-- Closes lg:col-span-4 -->
            </div> <!-- Closes grid-cols-12 -->

            <!-- Modal: Historial de Aprobaciones -->
            <x-modal name="approvals-history-modal" maxWidth="xl">
                <div class="bg-slate-900 border border-slate-850 rounded-2xl p-8 relative max-h-[85vh] flex flex-col">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                        <h3 class="text-lg font-black text-white flex items-center gap-2 uppercase tracking-tight">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Historial de Aprobaciones
                        </h3>
                        <button type="button" @click="$dispatch('close-modal', 'approvals-history-modal')" class="text-slate-400 hover:text-white text-xs font-black uppercase cursor-pointer">Cerrar</button>
                    </div>

                    <div class="overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-800 font-medium max-h-[60vh]">

                        @if($rendition->workflowHistories->isEmpty())
                            <p class="text-sm text-slate-500 font-medium">
                                Aún no hay movimientos registrados para esta rendición.
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach($rendition->workflowHistories->sortByDesc('created_at') as $history)
                                    @php
                                        $actionLabels = [
                                            'submitted_by_worker' => 'Enviado por trabajador',
                                            'approved_by_jefatura' => 'Aprobado por Jefatura',
                                            'rejected_by_jefatura' => 'Rechazado por Jefatura',
                                            'approved_by_controlling' => 'Aprobado por Controlling',
                                            'rejected_by_controlling' => 'Rechazado por Controlling',
                                            'approved_by_finances' => 'Aprobado por Finanzas',
                                            'rejected_by_finances' => 'Rechazado por Finanzas',
                                            'payment_completed_by_finances' => 'Cierre financiero confirmado por Finanzas',
                                            'payment_completed_automatically' => 'Cierre financiero automático',
                                            'expense_validated_by_controlling' => 'Documento validado por Controlling',
                                            'expense_observed_by_controlling' => 'Documento observado por Controlling',
                                        ];

                                        $actionLabel = $actionLabels[$history->action] ?? ucfirst(str_replace('_', ' ', $history->action));

                                        $isRejected = str_contains($history->action, 'rejected') || str_contains($history->action, 'observed') || str_contains($history->action, 'returned');
                                        $isApproved = str_contains($history->action, 'approved')
                                            || str_contains($history->action, 'validated')
                                            || str_contains($history->action, 'completed')
                                            || str_contains($history->action, 'automatically');
                                    @endphp

                                    <div class="p-4 rounded-xl bg-slate-900/50 border border-slate-700/50 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 h-full w-1
                                            @if($isRejected)
                                                bg-rose-500
                                            @elseif($isApproved)
                                                bg-emerald-500
                                            @else
                                                bg-blue-500
                                            @endif
                                        "></div>

                                        <div class="pl-3">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="text-sm font-black text-white uppercase tracking-tight">
                                                        {{ $history->user->name ?? 'Sistema' }}
                                                    </p>

                                                    <p class="text-[11px] text-slate-400 font-bold mt-1">
                                                        {{ $actionLabel }}
                                                    </p>
                                                </div>

                                                <span class="text-[10px] text-slate-600 font-black uppercase tracking-tighter whitespace-nowrap">
                                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <span class="px-2 py-1 rounded-lg bg-slate-800 text-slate-400 text-[9px] font-black uppercase tracking-widest border border-slate-700">
                                                    {{ $history->from_status ?? 'N/A' }}
                                                </span>

                                                <span class="text-slate-600 text-xs font-black">→</span>

                                                <span class="px-2 py-1 rounded-lg bg-slate-800 text-slate-300 text-[9px] font-black uppercase tracking-widest border border-slate-700">
                                                    {{ $history->to_status ?? 'N/A' }}
                                                </span>
                                            </div>

                                            @if($history->observation)
                                                <div class="mt-4 text-sm text-slate-300 bg-slate-900/80 rounded-2xl p-3 border border-slate-800 leading-relaxed">
                                                    {{ $history->observation }}
                                                </div>
                                            @endif

                                            @if($history->ip_address)
                                                <p class="mt-3 text-[10px] text-slate-600 font-mono">
                                                    IP: {{ $history->ip_address }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </x-modal>

            <!-- Modal: Firmas Digitales -->
            <x-modal name="digital-signatures-modal" maxWidth="xl">
                <div class="bg-slate-900 border border-slate-850 rounded-2xl p-8 relative max-h-[85vh] flex flex-col">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                        <h3 class="text-lg font-black text-white flex items-center gap-2 uppercase tracking-tight">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            Firmas Digitales
                        </h3>
                        <button type="button" @click="$dispatch('close-modal', 'digital-signatures-modal')" class="text-slate-400 hover:text-white text-xs font-black uppercase cursor-pointer">Cerrar</button>
                    </div>

                    <div class="overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-800 max-h-[60vh]">
                        @if($rendition->digitalSignatures->isEmpty())
                            <p class="text-sm text-slate-500 font-medium">
                                Aún no existen firmas digitales registradas para esta rendición.
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach($rendition->digitalSignatures->sortBy('signed_at') as $signature)
                                    @php
                                        $signatureLabels = [
                                            'rendition_worker_signature' => 'Firma del Trabajador',
                                            'rendition_jefatura_signature' => 'Firma de Jefatura',
                                            'planning_worker_signature' => 'Firma Planificación Trabajador',
                                            'jefatura_approval' => 'Firma Aprobación Jefatura',
                                        ];

                                        $signatureLabel = $signatureLabels[$signature->signature_type] ?? ucfirst(str_replace('_', ' ', $signature->signature_type));
                                        
                                        $isWorker = $signature->signature_type === 'rendition_worker_signature';
                                        $borderColor = $isWorker ? 'border-emerald-500/20 hover:border-emerald-500/40' : 'border-indigo-500/20 hover:border-indigo-500/40';
                                        $glowColor = $isWorker ? 'bg-emerald-500/5 group-hover:bg-emerald-500/10' : 'bg-indigo-500/5 group-hover:bg-indigo-500/10';
                                        $iconBg = $isWorker ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                                        $badgeBg = $isWorker ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                                        $tokenColor = $isWorker ? 'text-emerald-400' : 'text-indigo-400';
                                        $helpIconColor = $isWorker ? 'group-hover/token:text-emerald-400' : 'group-hover/token:text-indigo-400';
                                    @endphp

                                    <div class="p-5 rounded-3xl bg-slate-950/80 border-2 {{ $borderColor }} relative overflow-hidden group transition-all duration-300">
                                        <!-- Decorative stamp glow -->
                                        <div class="absolute -right-12 -bottom-12 w-28 h-28 {{ $glowColor }} rounded-full blur-[25px] transition-all"></div>
                                        
                                        <div class="flex items-start justify-between gap-4 relative z-10">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center border">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-white uppercase tracking-wider">
                                                        {{ $signatureLabel }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">
                                                        Certificado Digital
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-md {{ $badgeBg }} border text-[9px] font-black uppercase tracking-widest">
                                                Verificada
                                            </span>
                                        </div>

                                        <div class="mt-5 space-y-3 relative z-10">
                                            <div class="bg-slate-900/60 p-3.5 rounded-2xl border border-slate-800/80">
                                                <div class="grid grid-cols-2 gap-2 text-[11px]">
                                                    <div>
                                                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest block mb-0.5">Firmante</span>
                                                        <span class="text-white font-bold">{{ $signature->user ? $signature->user->name . ' ' . $signature->user->last_name : 'No disponible' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest block mb-0.5">Rol</span>
                                                        <span class="text-slate-300 font-semibold uppercase tracking-wider">{{ $signature->role ?? ($signature->user ? $signature->user->role : 'N/A') }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-2 gap-2 text-[11px] mt-2.5 pt-2.5 border-t border-slate-800/50">
                                                    <div>
                                                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest block mb-0.5">Fecha y Hora</span>
                                                        <span class="text-slate-300 font-semibold">{{ $signature->signed_at ? $signature->signed_at->format('d/m/Y H:i:s') : 'Sin fecha' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest block mb-0.5">Dirección IP</span>
                                                        <span class="text-slate-400 font-mono">{{ $signature->ip_address ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($signature->verification_token)
                                                <div class="bg-slate-900/40 px-3.5 py-2.5 rounded-2xl border border-slate-800/60 flex items-center justify-between gap-3 group/token hover:bg-slate-900/80 transition-colors">
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest block mb-0.5">Token de Verificación</span>
                                                        <span class="text-[10px] {{ $tokenColor }} font-mono truncate block">{{ $signature->verification_token }}</span>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-600 {{ $helpIconColor }} transition-colors cursor-help flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </div>
                                            @endif

                                            <div>
                                                <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest block mb-1">Firma Digital (Hash SHA-256)</span>
                                                <div class="text-[9px] text-slate-400 font-mono break-all bg-slate-900/80 rounded-xl p-3 border border-slate-800">
                                                    {{ $signature->hash }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </x-modal>

        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('expense_date')) {
                flatpickr("#expense_date", {
                    mode: "single",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d/m/Y",
                    locale: "es",
                    theme: "dark"
                });
            }

            @if(!$isOwner && !($isLocked ?? false))
                // Heartbeat to keep lock active every 2 minutes
                setInterval(function() {
                    window.segesLock.lock('rendiciones', {{ $rendition->id }});
                }, 120000);

                // Release lock on page unload
                window.addEventListener('pagehide', function() {
                    const fd = new FormData();
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    navigator.sendBeacon('/rendiciones/{{ $rendition->id }}/unlock', fd);
                });
            @endif
        });
    </script>
</x-app-layout>

