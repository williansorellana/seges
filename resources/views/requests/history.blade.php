<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Historial de Uso de Vehículos') }}
            </h2>
            <a href="{{ route('requests.history.trash') }}"
                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                {{ __('Papelera') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        filtersOpen: false,
        searchTerm: '',
        filterType: '{{ request('filter_type', 'day') }}',
        updateInputType() {
            const input = this.$refs.dateInput;
            if (this.filterType === 'day') {
                input.type = 'date';
            } else if (this.filterType === 'month') {
                input.type = 'month';
            } else if (this.filterType === 'year') {
                input.type = 'number';
                input.min = '2020';
                input.max = new Date().getFullYear();
            }
        }
    }" x-init="updateInputType()">
        <div class="max-w-[75%] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Barra de Búsqueda -->
            <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center">
                <div class="relative w-full sm:max-w-md">
                    <input type="text" x-model="searchTerm" placeholder="Buscar por nombre del empleado..."
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-shadow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="button" @click="filtersOpen = true"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-bold text-sm transition-colors flex items-center gap-2 border border-gray-300 dark:border-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                        Filtros
                        @if(request('filter_type') || request('cargo'))
                            <span class="bg-indigo-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">
                                {{ (request('filter_type') ? 1 : 0) + (request('cargo') ? 1 : 0) }}
                            </span>
                        @endif
                    </button>

                    <template x-if="'{{ request('filter_type') }}' || '{{ request('cargo') }}'">
                        <a href="{{ route('requests.history.index') }}"
                            class="px-3 py-2 text-gray-500 hover:text-red-500 transition-colors"
                            title="Limpiar Filtros">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Filter Sidebar (Off-canvas) -->
            <div x-show="filtersOpen" class="fixed inset-0 z-50 flex justify-end" style="display: none;">
                <!-- Backdrop -->
                <div @click="filtersOpen = false" x-show="filtersOpen"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <!-- Sidebar Content -->
                <div x-show="filtersOpen" x-transition:enter="transition transform ease-out duration-300"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition transform ease-in duration-300"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="relative w-80 bg-white dark:bg-gray-800 h-full shadow-2xl p-6 overflow-y-auto border-l border-gray-700">

                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                </path>
                            </svg>
                            Filtros
                        </h3>
                        <button @click="filtersOpen = false" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="GET" action="{{ route('requests.history.index') }}">

                        <!-- Filtro de Fecha -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Filtrar
                                por Fecha</label>
                            <div class="space-y-3">
                                <select name="filter_type" x-model="filterType" @change="updateInputType()"
                                    class="w-full rounded-lg bg-gray-700 border border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">
                                    <option value="day">Día</option>
                                    <option value="month">Mes</option>
                                    <option value="year">Año</option>
                                </select>
                                <input type="date" name="filter_value" x-ref="dateInput"
                                    value="{{ request('filter_value') }}"
                                    class="w-full rounded-lg bg-gray-700 border border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">
                            </div>
                        </div>

                        <!-- Filtro por Cargo -->
                        <div class="mb-6">
                            <label
                                class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Cargo</label>
                            <select name="cargo"
                                class="w-full rounded-lg bg-gray-700 border border-gray-600 text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3">
                                <option value="">Todos los cargos</option>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo }}" {{ request('cargo') == $cargo ? 'selected' : '' }}>
                                        {{ $cargo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-700 flex flex-col gap-3">
                            <button type="submit"
                                class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-all shadow-lg shadow-indigo-500/30">
                                Aplicar Filtros
                            </button>
                            @if(request('filter_type') || request('cargo'))
                                <a href="{{ route('requests.history.index') }}"
                                    class="w-full py-3 text-center text-gray-400 hover:text-white font-medium hover:bg-gray-700 rounded-lg transition-colors">
                                    Limpiar Filtros
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if($requests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Empleado
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Cargo / Dpto.
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Rol
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Vehículo
                                        </th>

                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Conductor
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha Inicio
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha Fin
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Destino
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($requests as $request)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-l-4 {{ request('highlight_id') == $request->id ? ($request->vehicleReturn && $request->vehicleReturn->body_damage_reported ? 'border-l-red-500 bg-red-50 dark:bg-red-900/20 animate-pulse' : 'border-l-blue-500 bg-blue-50 dark:bg-blue-900/20') : 'border-l-transparent' }}"
                                            id="row-{{ $request->id }}"
                                            x-show="searchTerm === '' || '{{ strtolower($request->user->name ?? '') }}'.includes(searchTerm.toLowerCase())"
                                            x-transition>
                                            <!-- Empleado -->
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($request->user && $request->user->profile_photo)
                                                            <img class="h-10 w-10 rounded-full object-cover"
                                                                src="{{ asset('storage/' . $request->user->profile_photo) }}"
                                                                alt="{{ $request->user->name }}">
                                                        @else
                                                            <div
                                                                class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                                                {{ $request->user ? strtoupper(substr($request->user->name, 0, 2)) : '??' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ $request->user ? $request->user->short_name : 'Usuario eliminado' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Cargo -->
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $request->user->cargo ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $request->user->departamento ?? 'N/A' }}
                                                </div>
                                            </td>

                                            <!-- Rol -->
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($request->user && $request->user->role === 'admin')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                        Admin
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        Usuario
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Vehículo -->
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    @if($request->vehicle)
                                                        {{ $request->vehicle->brand }} {{ $request->vehicle->model }}
                                                        <span
                                                            class="text-xs text-gray-500 dark:text-gray-400 block">({{ $request->vehicle->plate }})</span>
                                                    @else
                                                        <span class="text-red-500 italic">Vehículo Eliminado</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Fotos Iniciales -->


                                                    <!-- Conductor -->
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                                            @if($request->conductor)
                                                                <div class="flex items-center">
                                                                    @if($request->conductor->fotografia)
                                                                        <img class="h-8 w-8 rounded-full object-cover mr-2" src="{{ asset('storage/' . $request->conductor->fotografia) }}" alt="{{ $request->conductor->nombre }}">
                                                                    @endif
                                                                    {{ $request->conductor->nombre }}
                                                                </div>
                                                            @else
                                                                <span class="text-gray-500 dark:text-gray-400 italic">Solicitante</span>
                                                            @endif
                                                        </div>
                                                    </td>


                                                    <!-- Fecha Inicio -->
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}
                                                    </td>

                                                    <!-- Fecha Fin -->
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                                                    </td>


                                                    <!-- Estado -->
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        @if($request->status == 'pending')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Pendiente
                                                            </span>
                                                        @elseif($request->status == 'approved')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                Aprobada
                                                            </span>
                                                        @elseif($request->status == 'rejected')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                Rechazada
                                                            </span>
                                                        @elseif($request->status == 'completed')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                Completada
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <!-- Destino -->
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        @if($request->destination_type == 'local')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                                Local
                                                            </span>
                                                        @else
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                                Externo
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <!-- Acciones -->
                                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                                        <div class="flex justify-center items-center gap-2">
                                                            @if($request->status == 'completed' && $request->vehicleReturn)
                                                                <a href="{{ route('admin.returns.index', ['request_id' => $request->id]) }}" 
                                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition shadow-sm"
                                                                   title="Ver detalles de la entrega">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                    </svg>
                                                                    Ver Entrega
                                                                </a>
                                                            @endif

                                                            <button @click="$dispatch('open-modal', 'delete-request-{{ $request->id }}')" type="button"
                                                                class="p-2 text-red-600 hover:text-red-900 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full transition-colors"
                                                                title="Eliminar">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <!-- Modal Confirmar Eliminar -->
                                                        <template x-teleport="body">
                                                            <x-modal name="delete-request-{{ $request->id }}" :show="false" focusable>
                                                                <form method="POST" action="{{ route('requests.history.destroy', $request->id) }}" class="p-6">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                                        {{ __('¿Mover a la papelera?') }}
                                                                    </h2>
                                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 whitespace-normal">
                                                                        {{ __('La solicitud se moverá a la papelera de reciclaje. Podrás restaurarla o eliminarla permanentemente desde allí.') }}
                                                                    </p>
                                                                    <div class="mt-6 flex justify-end">
                                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                                            {{ __('Cancelar') }}
                                                                        </x-secondary-button>
                                                                        <x-danger-button class="ml-3">
                                                                            {{ __('Mover a Papelera') }}
                                                                        </x-danger-button>
                                                                    </div>
                                                                </form>
                                                            </x-modal>
                                                        </template>
                                                    </td>
                                                </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <div class="mt-6">
                                {{ $requests->links() }}
                            </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Sin resultados</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                No se encontraron registros con los filtros aplicados.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>



    <!-- Script para sroll automático si hay highlight -->
    @if(request('highlight_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const row = document.getElementById('row-{{ request('highlight_id') }}');
                if (row) {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        </script>
    @endif
</x-app-layout>
