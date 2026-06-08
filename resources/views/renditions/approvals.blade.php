<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Aprobaciones de Jefatura') }}
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
                    <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.53a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Panel de Aprobación</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Revisa las solicitudes de fondos y amipass de tu equipo.</p>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-10 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V8.25H8.25m0 0h0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Todo al día</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No tienes solicitudes pendientes de aprobación en este momento.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Colaborador
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Viaje
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Solicitado
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($plannings as $plan)
                                    <!-- Fila Principal -->
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="{ showReject: false }">
                                        
                                        <!-- Usuario -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($plan->user->name) }}&color=7F9CF5&background=EBF4FF" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $plan->user->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $plan->user->departamento ?? 'Sin departamento' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Detalles Viaje -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $plan->destination }} 
                                                <span class="text-xs text-gray-500 font-normal">({{ ucfirst($plan->trip_type) }})</span>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1" title="{{ $plan->motive }}">{{ $plan->motive }}</div>
                                            <div class="text-xs text-orange-600 mt-1">
                                                {{ \Carbon\Carbon::parse($plan->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($plan->end_date)->format('d M, Y') }}
                                            </div>
                                        </td>

                                        <!-- Solicitado -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $requestedFunds = $plan->requested_funds ?? 0;
                                                $amipassAmount = $plan->amipass_amount ?? 0;
                                                $totalRequested = $requestedFunds + $amipassAmount;
                                            @endphp

                                            <div class="flex flex-col gap-1.5">
                                                <div class="text-sm font-black text-green-600 dark:text-green-400">
                                                    Total: ${{ number_format($totalRequested, 0, ',', '.') }}
                                                </div>

                                                @if($plan->requires_funds)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        Fondos:
                                                        <span class="font-bold text-gray-800 dark:text-gray-200">
                                                            ${{ number_format($requestedFunds, 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500">
                                                        Fondos: No solicitado
                                                    </div>
                                                @endif

                                                @if($plan->requires_amipass)
                                                    <div class="text-xs mt-1 px-2 py-0.5 inline-flex bg-green-100 text-green-800 rounded w-fit">
                                                        Amipass: ${{ number_format($amipassAmount, 0, ',', '.') }}
                                                        / {{ $plan->amipass_business_days ?? $plan->amipass_days }} día(s)
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500">
                                                        Amipass: No solicitado
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            
                                            <!-- Botones -->
                                            <div class="flex items-center justify-center space-x-2" x-show="!showReject">
                                                <a href="{{ route('route-plannings.pdf', $plan->id) }}"
                                                target="_blank"
                                                class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded border border-indigo-200 hover:bg-indigo-200 transition"
                                                title="Descargar PDF">
                                                    PDF
                                                </a>

                                                <form action="{{ route('route-plannings.approve-jefatura', $plan->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded shadow hover:bg-green-700 transition" title="Aprobar Solicitud">
                                                        Aprobar
                                                    </button>
                                                </form>
                                                
                                                <button @click="showReject = true"
                                                        class="px-3 py-1.5 bg-red-100 text-red-600 text-xs font-bold rounded border border-red-200 hover:bg-red-200 transition"
                                                        title="Rechazar">
                                                    Devolver
                                                </button>
                                            </div>

                                            <!-- Formulario Rechazar (se expande) -->
                                            <div x-show="showReject" x-cloak class="mt-2 text-left bg-red-50 dark:bg-red-900/20 p-3 rounded border border-red-200 dark:border-red-800" style="min-width: 250px;">
                                                <form action="{{ route('route-plannings.reject-jefatura', $plan->id) }}" method="POST">
                                                    @csrf
                                                    <label class="block text-xs font-medium text-red-800 dark:text-red-300 mb-1">Motivo del Rechazo:</label>
                                                    <textarea name="observation" rows="2" class="w-full text-sm border-red-300 rounded focus:ring-red-500 focus:border-red-500" required placeholder="Faltan detalles..."></textarea>
                                                    <div class="mt-2 flex justify-end space-x-2">
                                                        <button type="button" @click="showReject = false" class="text-xs text-gray-500 hover:text-gray-700">Cancelar</button>
                                                        <button type="submit" class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded">Confirmar Rechazo</button>
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
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Rendiciones de Gastos (Boletas)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Revisa las justificaciones de gastos de tu equipo.</p>
                    </div>
                </div>

                @if(isset($renditions) && $renditions->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm text-gray-500">No hay rendiciones de boletas pendientes.</p>
                    </div>
                @elseif(isset($renditions))
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Monto Rendido</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @foreach($renditions as $ren)
                                <tr x-data="{ showReject: false }">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $ren->user->name }}</div>
                                        <div class="text-xs text-gray-500">Ref: {{ $ren->routePlanning->destination }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-xs text-gray-500">
                                                Asignado:
                                                <span class="font-semibold text-gray-700 dark:text-gray-300">
                                                    ${{ number_format($ren->funds_received, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="font-bold text-indigo-600">
                                                Rendido: ${{ number_format($ren->total_declared, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div x-show="!showReject" class="flex justify-center space-x-2">
                                            <a href="{{ route('renditions.show', $ren->id) }}"
                                            target="_blank"
                                            class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded hover:bg-gray-200 transition">
                                                Ver detalle
                                            </a>

                                            <a href="{{ route('renditions.pdf', $ren->id) }}"
                                            target="_blank"
                                            class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded hover:bg-indigo-200 transition">
                                                PDF
                                            </a>

                                            <form action="{{ route('renditions.approve-jefatura-rendition', $ren->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition">
                                                    Aprobar
                                                </button>
                                            </form>

                                            <button @click="showReject = true"
                                                    class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded hover:bg-red-200 transition">
                                                Rechazar
                                            </button>
                                        </div>
                                        <div x-show="showReject" x-cloak class="mt-2 text-left bg-red-50 p-2 rounded">
                                            <form action="{{ route('renditions.reject-jefatura-rendition', $ren->id) }}" method="POST">
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
