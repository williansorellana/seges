<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Declaración de Gastos: RND-') }}{{ str_pad($rendition->id, 4, '0', STR_PAD_LEFT) }}
            </h2>
            <a href="{{ route('renditions.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Volver a mis rendiciones</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($rendition->status === 'rejected' && $rendition->observations->count() > 0)
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 shadow-sm">
                    <h3 class="text-sm font-bold text-red-800 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Rendición Devuelta con Observaciones
                    </h3>
                    <div class="mt-3 space-y-2">
                        @foreach($rendition->observations as $obs)
                            <div class="bg-white p-3 rounded text-sm border border-red-100">
                                <span class="font-bold text-gray-900">{{ $obs->user->name }}:</span> 
                                <span class="text-gray-700">{{ $obs->observation }}</span>
                                <div class="text-xs text-gray-400 mt-1">{{ $obs->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Columna Izquierda: Detalles y Carga -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- Tarjeta de Gastos Subidos -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Documentos Justificativos</h3>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">{{ $rendition->expenses->count() }} subidos</span>
                        </div>
                        
                        <div class="p-6">
                            @if($rendition->expenses->isEmpty())
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay gastos ingresados</h3>
                                    <p class="mt-1 text-sm text-gray-500">Comienza a subir tus boletas y facturas usando el formulario.</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($rendition->expenses as $expense)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <div class="flex items-center space-x-4">
                                                <div class="p-2 bg-indigo-100 text-indigo-600 rounded">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $expense->provider }}</p>
                                                    <p class="text-xs text-gray-500 flex items-center mt-0.5">
                                                        <span class="uppercase mr-1">{{ $expense->document_type }}</span>
                                                        @if($expense->document_number) #{{ $expense->document_number }} @endif
                                                        &bull; {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">${{ number_format($expense->amount, 0, ',', '.') }}</p>
                                                <a
                                                    href="{{ route('renditions.expenses.attachment', $expense) }}"
                                                    target="_blank"
                                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center mt-1">
                                                    Ver Archivo
                                                    <svg class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($rendition->status === 'draft' || $rendition->status === 'rejected')
                    <!-- Formulario de Nuevo Gasto -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Agregar Nuevo Gasto</h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('renditions.expenses.store', $rendition->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha del Gasto</label>
                                        <input type="date" name="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor / Local</label>
                                        <input type="text" name="provider" placeholder="Ej: Restaurante El Paso" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de Documento</label>
                                        <select name="document_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            <option value="boleta">Boleta</option>
                                            <option value="factura">Factura</option>
                                            <option value="vale">Vale de Peaje/Estacionamiento</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nº Documento (Opcional)</label>
                                        <input type="text" name="document_number" placeholder="Ej: 154822" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Monto Total ($)</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" name="amount" min="1" class="pl-7 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Archivo Adjunto (Foto/PDF)</label>
                                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                    </div>
                                    
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Subir y Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Columna Derecha: Panel de Control Financiero -->
                <div class="space-y-6">
                    
                    <div class="bg-indigo-700 rounded-xl shadow-lg p-6 text-white">
                        <h3 class="text-lg font-medium text-indigo-100 mb-1">Estado de Caja</h3>
                        <p class="text-3xl font-bold mb-6">${{ number_format($rendition->funds_received, 0, ',', '.') }} <span class="text-sm font-normal text-indigo-300">Entregados</span></p>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-end border-b border-indigo-500/50 pb-2">
                                <span class="text-sm text-indigo-200">Total Rendido</span>
                                <span class="text-xl font-bold">${{ number_format($rendition->total_declared, 0, ',', '.') }}</span>
                            </div>
                            
                            @php
                                $diferencia = $rendition->funds_received - $rendition->total_declared;
                            @endphp
                            
                            <div class="flex justify-between items-end pt-1">
                                <span class="text-sm text-indigo-200">
                                    @if($diferencia > 0) Saldo a devolver @elseif($diferencia < 0) Saldo a tu favor @else Rendición exacta @endif
                                </span>
                                <span class="text-lg font-bold {{ $diferencia < 0 ? 'text-red-300' : 'text-green-300' }}">
                                    ${{ number_format(abs($diferencia), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Botón Enviar -->
                        @if($rendition->status === 'draft' || $rendition->status === 'rejected')
                        <div class="mt-8">
                            <form action="{{ route('renditions.submit', $rendition->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de enviar esta rendición? Ya no podrás agregar ni modificar las boletas.');">
                                @csrf
                                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-indigo-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition">
                                    Terminar y Enviar a Revisión
                                </button>
                            </form>
                            <p class="text-xs text-indigo-200 text-center mt-2">No podrás agregar más boletas después de enviar.</p>
                        </div>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 uppercase tracking-wider">Info del Viaje</h4>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500">Destino</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-200">{{ $rendition->routePlanning->destination }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Motivo</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-200">{{ $rendition->routePlanning->motive }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Amipass</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-200">
                                    {{ $rendition->routePlanning->requires_amipass ? $rendition->routePlanning->amipass_days . ' días' : 'No solicitado' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 uppercase tracking-wider">
                            Historial de Aprobaciones
                        </h4>

                        @if($rendition->workflowHistories->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Aún no hay movimientos registrados para esta rendición.
                            </p>
                        @else
                            <div class="space-y-4">
                                @foreach($rendition->workflowHistories->sortByDesc('created_at') as $history)
                                    <div class="border-l-4 pl-4
                                        @if(str_contains($history->action, 'rejected') || str_contains($history->action, 'returned'))
                                            border-red-500
                                        @elseif(str_contains($history->action, 'approved'))
                                            border-green-500
                                        @else
                                            border-indigo-500
                                        @endif
                                    ">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $history->user->name ?? 'Sistema' }}
                                        </div>

                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Acción: {{ str_replace('_', ' ', $history->action) }}
                                        </div>

                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Estado: {{ $history->from_status ?? 'N/A' }} → {{ $history->to_status }}
                                        </div>

                                        @if($history->observation)
                                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/40 rounded p-2">
                                                {{ $history->observation }}
                                            </div>
                                        @endif

                                        <div class="text-[11px] text-gray-400 mt-1">
                                            {{ $history->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
