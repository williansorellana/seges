<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestión de Solicitudes
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <form method="GET" action="{{ route('requests.index') }}" class="mb-6 flex gap-3">
                    <select name="status"
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="all">Todos</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobadas</option>
                        <option value="in_trip" {{ request('status') === 'in_trip' ? 'selected' : '' }}>En viaje</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Finalizadas</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazadas</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-md font-semibold text-sm">
                        Filtrar
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-900 text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">Usuario</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">Vehículo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">Periodo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-700 text-gray-200">
                            @forelse($requests as $request)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $request->user->name ?? 'Sin usuario' }}
                                        {{ $request->user->last_name ?? '' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        @if($request->vehicle)
                                            {{ $request->vehicle->brand }} {{ $request->vehicle->model }}
                                            <br>
                                            <span class="text-xs text-gray-400">{{ $request->vehicle->plate }}</span>
                                        @else
                                            <span class="text-red-400">Vehículo eliminado</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        {{ $request->start_date->format('d/m/Y H:i') }}
                                        <br>
                                        {{ $request->end_date->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-gray-700">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        @if(in_array($request->status, ['pending', 'approved']))
                                            <form method="POST" action="{{ route('requests.cancel.supervisor', $request->id) }}"
                                                onsubmit="return confirm('¿Está seguro de cancelar esta solicitud?');"
                                                class="space-y-2">
                                                @csrf

                                                <textarea name="cancellation_reason"
                                                    required
                                                    rows="2"
                                                    placeholder="Motivo de cancelación..."
                                                    class="w-full rounded-md border-gray-700 bg-gray-900 text-gray-200 text-xs"></textarea>

                                                <button type="submit"
                                                    class="px-3 py-2 bg-red-600 hover:bg-red-500 text-white rounded-md text-xs font-bold">
                                                    Cancelar
                                                </button>
                                            </form>                                         
                                        @else
                                            <span class="text-xs text-gray-500">Sin acciones</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                        No hay solicitudes registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
