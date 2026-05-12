<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mis Rendiciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tarjeta Informativa Superior -->
            <div class="bg-indigo-600 rounded-xl shadow-lg mb-8 overflow-hidden">
                <div class="px-6 py-8 sm:p-10 sm:pb-6 relative">
                    <div class="relative z-10">
                        <h3 class="text-xl font-bold text-white mb-2">Bandeja de Rendiciones</h3>
                        <p class="text-indigo-100 max-w-2xl text-sm">
                            Aquí aparecerán automáticamente los fondos que han sido aprobados y depositados para tus viajes.
                            Cuando regreses, debes justificar los gastos subiendo las boletas o facturas correspondientes.
                        </p>
                    </div>
                    <!-- Decoración visual SVG -->
                    <svg class="absolute right-0 bottom-0 text-indigo-500 opacity-30 transform translate-x-1/4 translate-y-1/4" width="200" height="200" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.644 1.59a.75.75 0 01.712 0l9.75 5.25c.343.184.343.682 0 .866l-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25a.75.75 0 010-.866l9.75-5.25z" />
                        <path d="M3.265 10.602l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25z" />
                        <path d="M3.265 14.602l7.668 4.129a2.25 2.25 0 002.134 0l7.668-4.13-9.75 5.25a.75.75 0 01-.712 0l-9.75-5.25z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 dark:border-gray-700">
                
                @if($renditions->isEmpty())
                    <div class="p-10 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sin rendiciones activas</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aún no tienes fondos entregados o pendientes de justificar.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Identificador
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Destino / Fechas
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Fondos Asignados
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Total Rendido
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($renditions as $ren)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        
                                        <!-- ID -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">RND-{{ str_pad($ren->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-xs text-gray-500 mt-1">Ref: REQ-{{ str_pad($ren->route_planning_id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </td>

                                        <!-- Viaje -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $ren->routePlanning->destination }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($ren->routePlanning->start_date)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($ren->routePlanning->end_date)->format('d/m/Y') }}
                                            </div>
                                        </td>

                                        <!-- Fondos Recibidos -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                ${{ number_format($ren->funds_received, 0, ',', '.') }}
                                            </div>
                                        </td>

                                        <!-- Total Rendido -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ren->total_declared > 0)
                                                <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                                    ${{ number_format($ren->total_declared, 0, ',', '.') }}
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-400 italic">
                                                    $0
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Estado -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @switch($ren->status)
                                                @case('draft')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                        Por Rendir
                                                    </span>
                                                    @break
                                                @case('pending_jefatura')
                                                @case('pending_controlling')
                                                @case('pending_finances')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-yellow-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        En Revisión
                                                    </span>
                                                    @break
                                                @case('approved')
                                                @case('closed')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                        Cerrada
                                                    </span>
                                                    @break
                                                @case('rejected')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                        Observada
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($ren->status === 'draft' || $ren->status === 'rejected')
                                                <a href="{{ route('renditions.show', $ren->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none transition shadow-sm">
                                                    Declarar Gastos
                                                </a>
                                            @else
                                                <a href="{{ route('renditions.show', $ren->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium mr-3">
                                                    Ver Detalles
                                                </a>
                                                @if($ren->status === 'approved')
                                                    <a href="{{ route('renditions.pdf', $ren->id) }}" class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 shadow-sm">
                                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                                        PDF
                                                    </a>
                                                @endif
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
