<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Historial de Rendiciones y Planificaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- SECCIÓN PLANIFICACIONES -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Historial de Solicitudes de Viaje</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Solicitudes que han sido aprobadas o rechazadas definitivamente.</p>
                    </div>
                </div>

                @if($plannings->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm text-gray-500">No hay registros en el historial de solicitudes.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha / Folio</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Destino</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @foreach($plannings as $plan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $plan->updated_at->format('d/m/Y H:i') }}<br>
                                        <span class="text-xs font-bold">REQ-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $plan->user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $plan->destination }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($plan->status === 'approved')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aprobado</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                                        @endif
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Historial de Rendiciones de Gastos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Rendiciones que completaron su ciclo contable.</p>
                    </div>
                </div>

                @if($renditions->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-sm text-gray-500">No hay registros en el historial de rendiciones.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha Cierre</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Colaborador</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Montos</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado / Doc</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                @foreach($renditions as $ren)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ren->updated_at->format('d/m/Y H:i') }}<br>
                                        <span class="text-xs font-bold text-indigo-600">RND-{{ str_pad($ren->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ren->user->name }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="text-gray-500 text-xs">Asignado: ${{ number_format($ren->funds_received, 0, ',', '.') }}</div>
                                        <div class="text-gray-900 font-bold">Rendido: ${{ number_format($ren->total_declared, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center space-y-2">
                                        <div>
                                            @if($ren->status === 'approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">Cerrado</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                                            @endif
                                        </div>
                                        @if($ren->status === 'approved')
                                            <div>
                                                <a href="{{ route('renditions.pdf', $ren->id) }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-900 font-bold underline">Descargar PDF</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $renditions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
