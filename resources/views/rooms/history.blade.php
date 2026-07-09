<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Historial de Reservas Aprobadas') }}
            </h2>
            
            <div class="flex space-x-2">
                <button @click="$dispatch('open-report-modal')" 
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-bold hover:bg-emerald-500 transition-all hover:-translate-y-0.5 shadow-md shadow-emerald-500/20 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generar Informe
                </button>

                <a href="{{ route('rooms.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-bold hover:bg-blue-500 transition-all hover:-translate-y-0.5 shadow-md shadow-blue-500/20 cursor-pointer">
                    ← Volver a Salas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        openReportModal: false,
        selectedMonth: {{ now()->month }},
        selectedYear: {{ now()->year }}
    }"
    @open-report-modal.window="openReportModal = true"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if($reservations->isEmpty())
                        <div class="text-center py-10 text-gray-500">
                            No hay historial de reservas aprobadas aún.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead class="bg-gray-800 text-gray-300">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Sala</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Solicitante</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Propósito</th>
                                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">Estado Tiempo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700 bg-gray-900 text-gray-300">
                                    @foreach($reservations as $res)
                                        <tr class="hover:bg-gray-800 transition duration-150">
                                            <td class="px-5 py-4 text-sm">
                                                <div class="font-bold text-white">{{ $res->start_time->format('d/m/Y') }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $res->start_time->format('H:i') }} - {{ $res->end_time->format('H:i') }}
                                                </div>
                                            </td>

                                            <td class="px-5 py-4 text-sm font-medium text-blue-400">
                                                {{ $res->meetingRoom->name ?? 'Sala Eliminada' }}
                                            </td>

                                            <td class="px-5 py-4 text-sm">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white mr-2">
                                                        {{ substr($res->user->name ?? 'X', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-white">{{ $res->user->name ?? 'Usuario Eliminado' }}</div>
                                                        <div class="text-xs text-gray-500">{{ $res->user->email ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-5 py-4 text-sm">
                                                <div class="text-white italic">"{{ $res->purpose }}"</div>
                                                @if($res->resources)
                                                    <div class="mt-1 text-xs text-indigo-300">
                                                        <span class="font-bold">Recursos:</span> {{ $res->resources }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-5 py-4 text-center">

                                                @if($res->status === 'cancelled')
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                        CANCELADA
                                                    </span>
                                                @elseif($res->end_time->isPast())
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-700 text-gray-300 border border-gray-600">
                                                        FINALIZADA
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900 text-green-200 border border-green-700 animate-pulse">
                                                        PROGRAMADA
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $reservations->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <template x-teleport="body">
            <div x-show="openReportModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="openReportModal" 
                         x-transition.opacity
                         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                         @click="openReportModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div x-show="openReportModal" 
                         x-transition.scale
                         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm w-full border border-gray-200 dark:border-gray-700 relative z-50">
                        
                        <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Periodo del Informe
                            </h3>
                            <button @click="openReportModal = false" class="text-indigo-200 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="px-6 py-6 bg-white dark:bg-gray-800">
                            
                            <form action="{{ route('rooms.report') }}" method="GET" target="_blank" @submit="setTimeout(() => openReportModal = false, 500)">
                                <div class="space-y-4 mb-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Desde:
                                        </label>
                                        <input type="text"
                                                name="start_date"
                                                required
                                                placeholder="Seleccione fecha"
                                                class="flatpickr-date w-full cursor-pointer rounded-lg border-gray-300 dark:border-gray-700 bg-white text-black dark:bg-gray-900 dark:text-white">
                                        
                                            
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Hasta:
                                        </label>
                                        <input type="text"
                                            name="end_date"
                                            required
                                            placeholder="Seleccione fecha"
                                            class="flatpickr-date w-full cursor-pointer rounded-lg border-gray-300 dark:border-gray-700 bg-white text-black dark:bg-gray-900 dark:text-white">
                                            
                                    </div>
                                    
                                </div>

                                <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-lg px-4 py-3 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:scale-[1.02]">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Descargar Informe
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </template>
        
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('.flatpickr-date', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd-m-Y',
                locale: 'es',
                allowInput: false,
                disableMobile: true
            });
        });
    </script>
    @endpush
</x-app-layout>