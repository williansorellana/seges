<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Controlling') }}
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
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Auditoría de Requerimientos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Revisa y valida las planificaciones de viaje antes de pasarlas a Finanzas.</p>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-10 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.331.108 2.175 1.071 2.175 2.206v6.518c0 1.078-.79 1.956-1.83 2.12M15 12.75h.008v.008H15v-.008zm0 3h.008v.008H15v-.008zm0 3h.008v.008H15v-.008zM11.25 12.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bandeja Vacía</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No hay solicitudes pendientes de validación en Controlling.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        ID / Solicitante
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Destino y Motivo
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Fondos Requeridos
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Validación
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($plannings as $plan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="{ showReject: false }">
                                        
                                        <!-- ID y Usuario -->
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-mono text-gray-500 mb-1">REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($plan->user->name) }}&color=3B82F6&background=EFF6FF" alt="">
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $plan->user->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->user->departamento ?? 'Sin departamento' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Detalles Viaje -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $plan->destination }} 
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1" title="{{ $plan->motive }}">{{ $plan->motive }}</div>
                                            <div class="text-xs text-blue-600 mt-1 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                                {{ \Carbon\Carbon::parse($plan->start_date)->format('d M') }} al {{ \Carbon\Carbon::parse($plan->end_date)->format('d M') }}
                                            </div>
                                        </td>

                                        <!-- Fondos Requeridos -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($plan->requires_funds)
                                                <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                    <span class="text-green-600 dark:text-green-400">$</span>{{ number_format($plan->requested_funds, 0, ',', '.') }}
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500">No aplica</div>
                                            @endif
                                            
                                            @if($plan->requires_amipass)
                                                <div class="text-xs mt-1 text-green-700 dark:text-green-400 font-semibold flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                    Amipass: {{ $plan->amipass_days }} días
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            
                                            <div class="flex items-center justify-center space-x-2" x-show="!showReject">
                                                <!-- Formulario Validar -->
                                                <form action="{{ route('route-plannings.approve-controlling', $plan->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded shadow hover:bg-blue-700 transition" title="Validar y Escalar a Finanzas">
                                                        Validar
                                                    </button>
                                                </form>
                                                
                                                <!-- Boton Rechazar -->
                                                <button @click="showReject = true" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded border border-red-200 hover:bg-red-100 transition" title="Rechazar Documento">
                                                    Rechazar
                                                </button>
                                            </div>

                                            <!-- Formulario Rechazar (se expande) -->
                                            <div x-show="showReject" x-cloak class="mt-2 text-left bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-800" style="min-width: 250px;">
                                                <form action="{{ route('route-plannings.reject-controlling', $plan->id) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-xs font-medium text-red-800 dark:text-red-300 mb-1">Motivo / Error encontrado:</label>
                                                    <textarea name="observation" rows="2" class="w-full text-sm border-red-300 rounded focus:ring-red-500 focus:border-red-500" required placeholder="Ej: Faltan especificaciones..."></textarea>
                                                    <div class="mt-2 flex justify-end space-x-2">
                                                        <button type="button" @click="showReject = false" class="text-xs text-gray-500 hover:text-gray-700">Cancelar</button>
                                                        <button type="submit" class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded shadow-sm hover:bg-red-700">Devolver</button>
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
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Auditoría de Rendiciones</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Verifica que las boletas coincidan con los fondos entregados.</p>
                    </div>
                </div>

                @if(isset($renditions) && $renditions->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm text-gray-500">No hay rendiciones pendientes de auditoría.</p>
                    </div>
                @elseif(isset($renditions))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Colaborador / Viaje</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Entregado vs Rendido</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Auditoría</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @foreach($renditions as $ren)
                                <tr x-data="{ showReject: false }">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $ren->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $ren->routePlanning->destination }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500">Entregado: ${{ number_format($ren->funds_received, 0, ',', '.') }}</div>
                                        <div class="text-sm font-bold text-indigo-600">Rendido: ${{ number_format($ren->total_declared, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div x-show="!showReject" class="flex justify-center space-x-2">
                                            <a href="{{ route('renditions.show', $ren->id) }}" target="_blank" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded hover:bg-gray-200">Auditar</a>
                                            <form action="{{ route('renditions.approve-controlling-rendition', $ren->id) }}" method="POST">
                                                @csrf <button type="submit" class="px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded">Validar</button>
                                            </form>
                                            <button @click="showReject = true" class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded">Rechazar</button>
                                        </div>
                                        <div x-show="showReject" x-cloak class="mt-2 text-left bg-red-50 p-2 rounded">
                                            <form action="{{ route('renditions.reject-controlling-rendition', $ren->id) }}" method="POST">
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
