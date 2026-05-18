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
            <a href="{{ route('renditions.index') }}" class="px-5 py-2.5 bg-slate-900 text-slate-300 text-[11px] font-black uppercase tracking-[0.1em] rounded-xl border border-slate-800 hover:bg-slate-800 hover:text-white hover:border-slate-600 transition-all flex items-center gap-2 shadow-lg shadow-black/20 group">
                <svg class="w-4 h-4 text-slate-500 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                {{ __('Volver a mis rendiciones') }}
            </a>
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
                                                    <p class="text-base font-black text-white group-hover:text-blue-400 transition-colors uppercase tracking-tight">{{ $expense->provider }}</p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest bg-blue-500/10 px-2 py-0.5 rounded-md">{{ $expense->document_type }}</span>
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
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ asset('storage/' . $expense->attachment_path) }}" target="_blank" class="p-2 bg-indigo-600/10 text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-indigo-600/5" title="Ver Documento">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    </a>
                                                    @if($rendition->status === 'draft' || $rendition->status === 'rejected')
                                                        <button type="button" @click="showEditModal = true" class="p-2 bg-blue-600/10 text-blue-400 rounded-xl hover:bg-blue-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-blue-600/5" title="Editar">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                        </button>
                                                        <button type="button" @click="showDeleteModal = true" class="p-2 bg-rose-600/10 text-rose-500 rounded-xl hover:bg-rose-600 hover:text-white transition-all cursor-pointer shadow-lg shadow-rose-600/5" title="Eliminar">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($rendition->status === 'draft' || $rendition->status === 'rejected')
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
                                                    <form action="{{ route('renditions.expenses.update', [$rendition->id, $expense->id]) }}" method="POST" enctype="multipart/form-data" class="text-left space-y-6">
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
                                                                    <select name="document_type" class="w-full bg-transparent border-none text-white text-sm font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-900" required>
                                                                        <option value="boleta" {{ $expense->document_type == 'boleta' ? 'selected' : '' }}>Boleta</option>
                                                                        <option value="factura" {{ $expense->document_type == 'factura' ? 'selected' : '' }}>Factura</option>
                                                                        <option value="vale" {{ $expense->document_type == 'vale' ? 'selected' : '' }}>Vale</option>
                                                                        <option value="otro" {{ $expense->document_type == 'otro' ? 'selected' : '' }}>Otro</option>
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

                    @if($rendition->status === 'draft' || $rendition->status === 'rejected')
                    <!-- Formulario de Nuevo Gasto -->
                    <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative">
                        <div class="px-8 py-6 border-b border-slate-800 bg-slate-900/50 backdrop-blur-md sticky top-0 z-10">
                            <h3 class="text-lg font-black text-white tracking-tight flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                Agregar Nuevo Gasto
                            </h3>
                        </div>
                        <div class="p-8">
                            <form action="{{ route('renditions.expenses.store', $rendition->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Fecha del Gasto</label>
                                        <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 focus-within:bg-slate-950 transition-all">
                                            <input type="text" name="date" id="expense_date" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold cursor-pointer" required placeholder="Seleccionar fecha...">
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Proveedor / Local</label>
                                        <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 focus-within:bg-slate-950 transition-all">
                                            <input type="text" name="provider" placeholder="Ej: Restaurante El Paso" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" required>
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Tipo de Documento</label>
                                        <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 overflow-hidden focus-within:border-blue-500 transition-all">
                                            <select name="document_type" class="w-full bg-transparent border-none text-white text-sm font-bold py-3.5 px-4 focus:ring-0 cursor-pointer [&>option]:bg-slate-950" required>
                                                <option value="boleta">Boleta</option>
                                                <option value="factura">Factura</option>
                                                <option value="vale">Vale de Peaje/Estacionamiento</option>
                                                <option value="otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Nº Documento (Opcional)</label>
                                        <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 focus-within:bg-slate-950 transition-all">
                                            <input type="text" name="document_number" placeholder="Ej: 154822" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold">
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">Monto Total ($)</label>
                                        <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950/50 px-4 py-3.5 focus-within:border-blue-500 focus-within:bg-slate-950 transition-all relative">
                                            <span class="text-blue-400 font-black mr-2">$</span>
                                            <input type="number" name="amount" min="1" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" placeholder="0" required>
                                        </div>
                                    </div>

                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest">Archivo Adjunto (Foto/PDF)</label>
                                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-all cursor-pointer bg-slate-950/50 border border-slate-800 rounded-2xl p-1.5 focus:outline-none" required>
                                    </div>
                                    
                                </div>

                                <div class="pt-8 border-t border-slate-800 flex justify-end">
                                    <button type="submit" class="px-10 py-4 bg-blue-600 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-600/20 hover:bg-blue-500 hover:-translate-y-1 hover:shadow-blue-600/40 transition-all flex items-center gap-3 cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                        Subir y Guardar Gasto
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Columna Derecha: Panel Financiero (4 cols) -->
                <div class="lg:col-span-4 space-y-8">
                    
                    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl p-8">
                        <!-- Glow effect -->
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-600/10 rounded-full blur-[80px] pointer-events-none"></div>
                        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-600/10 rounded-full blur-[80px] pointer-events-none"></div>

                        <div class="relative z-10">
                            <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                Estado de Caja
                            </h3>
                            
                            <div class="space-y-8">
                                <div>
                                    <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-1">Fondos Recibidos</p>
                                    <p class="text-4xl font-black text-white tracking-tighter">${{ number_format($rendition->funds_received, 0, ',', '.') }}</p>
                                </div>

                                <div class="pt-6 border-t border-slate-800">
                                    <div class="flex justify-between items-end mb-4">
                                        <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Total Rendido</p>
                                        <p class="text-2xl font-black text-white tracking-tighter">${{ number_format($rendition->total_declared, 0, ',', '.') }}</p>
                                    </div>
                                    
                                    @php
                                        $diferencia = $rendition->funds_received - $rendition->total_declared;
                                    @endphp
                                    
                                    <div class="p-4 rounded-3xl bg-slate-950/50 border border-slate-800/80 backdrop-blur-sm">
                                        <div class="flex justify-between items-center">
                                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                                                @if($diferencia > 0) Saldo a devolver @elseif($diferencia < 0) Saldo a favor @else Rendición exacta @endif
                                            </p>
                                            <p class="text-lg font-black {{ $diferencia < 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                                                ${{ number_format(abs($diferencia), 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón Enviar -->
                            @if($rendition->status === 'draft' || $rendition->status === 'rejected')
                            <div class="mt-10 pt-8 border-t border-slate-800" x-data="{ showSubmitModal: false }">
                                <button type="button" @click="showSubmitModal = true" class="w-full py-4 px-6 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-600/20 hover:bg-emerald-500 hover:-translate-y-1 hover:shadow-emerald-600/40 transition-all flex items-center justify-center gap-3 cursor-pointer group">
                                    <svg class="w-5 h-5 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Terminar y Enviar
                                </button>
                                <p class="text-[10px] text-slate-500 text-center mt-4 font-bold uppercase tracking-tighter opacity-60 italic">Cierra la edición de esta rendición</p>

                                <!-- Submit Modal (Style inspired by Delete User) -->
                                <div x-show="showSubmitModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/95 backdrop-blur-xl" style="display: none;" x-transition>
                                    <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] p-10 max-w-lg w-full mx-auto shadow-[0_0_100px_rgba(16,185,129,0.1)] relative overflow-hidden" @click.away="showSubmitModal = false">
                                        <div class="absolute -top-32 -right-32 w-64 h-64 bg-emerald-500/10 rounded-full blur-[80px] pointer-events-none"></div>
                                        
                                        <div class="flex items-center gap-5 mb-8">
                                            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center border border-emerald-500/20 shadow-inner">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-2xl font-black text-white leading-tight uppercase tracking-tight">Enviar Rendición</h3>
                                                <p class="text-[10px] font-black text-emerald-500/80 uppercase tracking-[0.2em] mt-1">Confirmación Final</p>
                                            </div>
                                        </div>

                                        <p class="text-base text-slate-300 mb-10 leading-relaxed font-medium">
                                            ¿Está seguro de enviar esta rendición? <span class="text-white font-black">Ya no podrá agregar ni modificar documentos</span> una vez que el proceso sea enviado a revisión por el departamento de Finanzas.
                                        </p>

                                        <div class="flex justify-end gap-4">
                                            <button type="button" @click="showSubmitModal = false" class="px-8 py-3.5 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-white transition-colors cursor-pointer">
                                                Cancelar
                                            </button>
                                            <form action="{{ route('renditions.submit', $rendition->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-10 py-3.5 bg-emerald-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl shadow-emerald-600/30 hover:bg-emerald-500 hover:-translate-y-1 transition-all cursor-pointer">
                                                    Aceptar y Enviar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Info del Viaje Card -->
                    <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl p-8 relative overflow-hidden">
                        <div class="absolute -top-24 -left-24 w-48 h-48 bg-slate-800/10 rounded-full blur-[60px] pointer-events-none"></div>
                        <h4 class="text-[10px] font-black text-slate-500 mb-8 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>
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
                                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase rounded-lg border border-emerald-500/20">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                {{ $rendition->routePlanning->amipass_days }} días
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-600 font-bold uppercase tracking-tighter italic">No solicitado</span>
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
            </div>

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
        });
    </script>
</x-app-layout>

