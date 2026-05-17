<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Finanzas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 dark:border-gray-700">
                
                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 flex items-center space-x-3">
                    <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Tesorería y Fondos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Autoriza la salida de fondos y viáticos para los viajes aprobados por Controlling.</p>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-10 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sin salidas pendientes</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No hay requerimientos financieros pendientes de autorización.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Solicitante / Aprobación
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Motivo
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Total a Liberar
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Gestión
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($plannings as $plan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="{ showReject: false }">
                                        
                                        <!-- Solicitante -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full border-2 border-emerald-200" src="https://ui-avatars.com/api/?name={{ urlencode($plan->user->name) }}&color=059669&background=D1FAE5" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $plan->user->name }}</div>
                                                    <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        @php
                                                            if ($plan->status === 'pending_finances') {

                                                                $approvalLabel = $plan->trip_type === 'reunion'
                                                                    ? 'Visado por Jefatura'
                                                                    : 'Visado por Controlling';

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

                                                        {{ $approvalLabel }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Detalles Viaje -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $plan->destination }} 
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1" title="{{ $plan->motive }}">{{ $plan->motive }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Fechas:</span> {{ \Carbon\Carbon::parse($plan->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($plan->end_date)->format('d/m/Y') }}
                                            </div>
                                        </td>

                                        <!-- Requerimientos -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($plan->requires_funds)
                                                <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                                    ${{ number_format($plan->requested_funds, 0, ',', '.') }}
                                                </div>
                                                <div class="text-xs text-gray-500">Transferencia requerida</div>
                                            @endif
                                            
                                            @if($plan->requires_amipass)
                                                <div class="text-xs mt-2 px-2 py-1 inline-flex items-center bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded font-medium border border-gray-200 dark:border-gray-600">
                                                    <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                    Cargar Amipass: {{ $plan->amipass_days }} días
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            
                                            <div class="flex items-center justify-center space-x-2" x-show="!showReject">
                                                <!-- Formulario Liberar -->
                                                <form action="{{ route('route-plannings.approve-finances', $plan->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded shadow-md hover:bg-emerald-700 transition" title="Aprobar y Generar Firma Digital">
                                                        Liberar Fondos
                                                    </button>
                                                </form>
                                                
                                                <!-- Boton Rechazar -->
                                                <button @click="showReject = true" class="px-3 py-2 bg-red-50 text-red-600 text-xs font-bold rounded border border-red-200 hover:bg-red-100 transition" title="Rechazar y Devolver">
                                                    Rechazar
                                                </button>
                                            </div>

                                            <!-- Formulario Rechazar (se expande) -->
                                            <div x-show="showReject" x-cloak class="mt-2 text-left bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-800" style="min-width: 250px;">
                                                <form action="{{ route('route-plannings.reject-finances', $plan->id) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-xs font-medium text-red-800 dark:text-red-300 mb-1">Motivo / Error en Finanzas:</label>
                                                    <textarea name="observation" rows="2" class="w-full text-sm border-red-300 rounded focus:ring-red-500 focus:border-red-500" required placeholder="Ej: Faltan fondos en presupuesto..."></textarea>
                                                    <div class="mt-2 flex justify-end space-x-2">
                                                        <button type="button" @click="showReject = false" class="text-xs text-gray-500 hover:text-gray-700">Cancelar</button>
                                                        <button type="submit" class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded shadow-sm hover:bg-red-700">Confirmar Rechazo</button>
                                                    </div>
                                                </form>
                                            </div>

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

            <!-- SECCIÓN RENDICIONES -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-200 dark:border-gray-700 flex items-center space-x-3">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Cierre Contable de Rendiciones</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aprueba la conciliación final de los fondos rendidos.</p>
                    </div>
                </div>

                @if(isset($renditions) && $renditions->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm text-gray-500">No hay cierres contables pendientes.</p>
                    </div>
                @elseif(isset($renditions))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Conciliación</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acción Final</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @foreach($renditions as $ren)
                                <tr x-data="{ showReject: false }">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $ren->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500">Entregado: ${{ number_format($ren->funds_received, 0, ',', '.') }} | Rendido: ${{ number_format($ren->total_declared, 0, ',', '.') }}</div>
                                        <div class="text-sm font-bold {{ $ren->funds_received - $ren->total_declared < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            Saldo: ${{ number_format(abs($ren->funds_received - $ren->total_declared), 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div x-show="!showReject" class="flex justify-center space-x-2">
                                            <a href="{{ route('renditions.show', $ren->id) }}" target="_blank" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded hover:bg-gray-200">Revisar</a>
                                            <form action="{{ route('renditions.approve-finances-rendition', $ren->id) }}" method="POST">
                                                @csrf <button type="submit" class="px-2 py-1 bg-emerald-600 text-white text-xs font-bold rounded">Cerrar Proceso</button>
                                            </form>
                                            <button @click="showReject = true" class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded">Rechazar</button>
                                        </div>
                                        <div x-show="showReject" x-cloak class="mt-2 text-left bg-red-50 p-2 rounded">
                                            <form action="{{ route('renditions.reject-finances-rendition', $ren->id) }}" method="POST">
                                                @csrf <textarea name="observation" class="w-full text-xs rounded border-red-300" required placeholder="Motivo..."></textarea>
                                                <button type="submit" class="mt-1 px-2 py-1 bg-red-600 text-white text-xs rounded">Devolver</button>
                                            </form>
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
