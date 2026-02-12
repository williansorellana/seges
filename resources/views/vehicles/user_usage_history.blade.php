<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100" x-data="{ width: window.innerWidth }">

                    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <div class="mb-2">
                                <a href="{{ route('vehicles.users-history-index') }}"
                                    class="inline-flex items-center text-gray-400 hover:text-white transition">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Volver a la lista
                                </a>
                            </div>
                            <h2 class="text-xl font-bold text-white uppercase tracking-tight">Historial de Uso de
                                Vehículo por Usuario</h2>
                        </div>

                        <form method="GET"
                            class="flex flex-wrap items-end gap-2 bg-gray-900 p-3 rounded-lg border border-gray-700">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Desde</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded p-2 focus:ring-yellow-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Hasta</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded p-2 focus:ring-yellow-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Estado</label>
                                <select name="status"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded p-2 focus:ring-yellow-500 w-full md:w-auto">
                                    <option value="">Todos</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Solicitado</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                        Aprobado</option>
                                    <option value="in_trip" {{ request('status') == 'in_trip' ? 'selected' : '' }}>En
                                        Viaje</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                        Finalizado</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                        Rechazado</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        Cancelado</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded h-[38px] flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                        </path>
                                    </svg>
                                    Filtrar
                                </button>

                                <a href="{{ route('vehicles.user-history.pdf', ['id' => $recipient->id, 'type' => isset($recipient->nombre) ? 'worker' : 'user', 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'status' => request('status')]) }}"
                                    target="_blank"
                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded h-[38px] flex items-center"
                                    title="Exportar PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="ml-1">PDF</span>
                                </a>

                                <a href="{{ url()->current() }}"
                                    class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded h-[38px]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    <div
                        class="bg-gray-900 rounded-lg p-4 mb-6 border border-gray-700 flex items-center gap-4 shadow-inner">
                        <div
                            class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-2xl border-2 border-gray-600 uppercase">
                            {{ substr($recipient->name ?? $recipient->nombre, 0, 1) }}
                        </div>
                        <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <span class="block text-gray-500 text-xs uppercase font-bold">Usuario</span>
                                <span class="text-white font-bold">{{ $recipient->name ?? $recipient->nombre }}
                                    {{ $recipient->last_name ?? '' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase font-bold">RUT</span>
                                <span class="font-mono text-white font-bold">{{ $recipient->rut }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase font-bold">Departamento</span>
                                <span class="text-white">
                                    @if(isset($recipient->departamento))
                                        {{ $recipient->departamento }}
                                    @elseif(isset($recipient->nombre))
                                        {{ __('Externo') }}
                                    @else
                                        {{ __('No asignado') }}
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase font-bold">Viajes Totales</span>
                                <span class="text-green-400 font-bold">{{ $usageHistory->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-900 rounded-lg shadow-xl border border-gray-700 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Vehículo
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Inicio /
                                        Término</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Destino /
                                        Uso</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">
                                        Acompañantes</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">Estado
                                    </th>
                                </tr>
                            </thead>
                            @foreach($usageHistory as $usage)
                                <tbody class="divide-y divide-gray-700" x-data="{ open: false }">
                                    <tr class="hover:bg-gray-800/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-white">{{ $usage->vehicle->brand }}
                                                {{ $usage->vehicle->model }}
                                            </div>
                                            <div class="text-xs font-mono text-yellow-500">{{ $usage->vehicle->plate }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-300">
                                            <div class="flex flex-col">
                                                <span>{{ $usage->start_date->format('d/m/Y H:i') }}</span>
                                                <span
                                                    class="text-gray-500 text-xs">{{ $usage->end_date->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($usage->destination)
                                                <span class="text-white font-medium">{{ $usage->destination }}</span>
                                            @else
                                                <span
                                                    class="italic text-gray-500">{{ $usage->destination_type === 'outside' ? 'Fuera de la ciudad' : 'Local' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($usage->companions->count() > 0)
                                                <button @click="open = !open"
                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-gray-800 border border-gray-600 text-indigo-400 text-xs font-bold hover:bg-indigo-600 hover:text-white transition">
                                                    {{ $usage->companions->count() }} <span class="ml-1"
                                                        x-text="open ? 'Cerrar' : 'Ver'"></span>
                                                </button>
                                            @else
                                                <span class="text-gray-600 text-xs italic">Solo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusMap = [
                                                    'completed' => ['l' => 'Finalizado', 'c' => 'bg-green-900 text-green-400 border-green-800'],
                                                    'approved' => ['l' => 'Aprobado', 'c' => 'bg-blue-900 text-blue-400 border-blue-800'],
                                                    'in_trip' => ['l' => 'En Viaje', 'c' => 'bg-indigo-900 text-indigo-400 border-indigo-800'],
                                                    'pending' => ['l' => 'Pendiente', 'c' => 'bg-yellow-900 text-yellow-400 border-yellow-800'],
                                                    'rejected' => ['l' => 'Rechazado', 'c' => 'bg-red-900 text-red-400 border-red-800'],
                                                    'cancelled' => ['l' => 'Cancelado', 'c' => 'bg-gray-700 text-gray-400 border-gray-600'],
                                                ];
                                                $st = $statusMap[$usage->status] ?? ['l' => $usage->status, 'c' => 'bg-gray-800 text-gray-400'];
                                            @endphp
                                            <span
                                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $st['c'] }}">
                                                {{ strtoupper($st['l']) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr x-show="open" x-cloak x-transition
                                        class="bg-gray-800/40 border-l-4 border-indigo-500">
                                        <td colspan="6" class="px-6 py-4">
                                            <div class="flex flex-wrap gap-2">

                                                @foreach($usage->companions as $companion)
                                                    <div
                                                        class="flex items-center bg-gray-900 border border-gray-700 rounded-full px-3 py-1.5 shadow-sm">
                                                        <div
                                                            class="w-6 h-6 rounded-full bg-indigo-600 flex items-center justify-center text-[10px] font-bold text-white mr-2 uppercase">
                                                            {{ substr($companion->user ? $companion->user->name : $companion->external_name, 0, 1) }}
                                                        </div>
                                                        <span
                                                            class="text-xs text-gray-200">{{ $companion->user ? ($companion->user->name . ' ' . $companion->user->last_name) : $companion->external_name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>