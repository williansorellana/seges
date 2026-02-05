<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Solicitar Vehículo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Container -->
            <div class="bg-gray-50 dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-xl">

                @if ($errors->any())
                    <div class="mb-4 mx-6 mt-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <p class="font-bold">Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('requests.store') }}" class="p-6 md:p-8 space-y-12">
                    @csrf

                    <div x-data="{ 
                        selectedId: '{{ old('vehicle_id') }}', 
                        search: '',
                        vehicles: {{ $vehicles->map(function ($vehicle) {
    return [
        'id' => $vehicle->id,
        'label' => $vehicle->brand . ' ' . $vehicle->model,
        'plate' => $vehicle->plate,
        'image' => $vehicle->image_path ? asset('storage/' . $vehicle->image_path) : null,
        'year' => $vehicle->year,
    ];
})->toJson() }},
                        get filteredVehicles() {
                            if (this.search === '') return this.vehicles;
                            return this.vehicles.filter(vehicle => {
                                return vehicle.label.toLowerCase().includes(this.search.toLowerCase()) || 
                                       vehicle.plate.toLowerCase().includes(this.search.toLowerCase());
                            });
                        }
                    }">

                        <!-- PASO 1: Selección de Vehículo -->
                        <div class="space-y-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4 gap-4 md:gap-0">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">1</div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Selecciona el Vehículo</h3>
                                </div>
                                <!-- Buscador -->
                                <div class="relative w-full md:w-64">
                                    <input type="text" x-model="search" placeholder="Buscar por patente o modelo..." 
                                        class="block w-full rounded-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10 py-2">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none pb-0.5">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Input -->
                            <input type="hidden" name="vehicle_id" :value="selectedId">

                            <!-- GRID DE VEHÍCULOS (Scrollable) -->
                            <div class="max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 pb-2">
                                    <template x-for="vehicle in filteredVehicles" :key="vehicle.id">
                                        <div @click="selectedId = vehicle.id"
                                            class="group relative cursor-pointer rounded-2xl border-2 transition-all duration-200 ease-in-out overflow-hidden hover:shadow-lg"
                                            :class="selectedId == vehicle.id ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20 ring-1 ring-indigo-600' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-indigo-300'">

                                            <!-- Checkmark Badge -->
                                            <div x-show="selectedId == vehicle.id"
                                                class="absolute top-3 right-3 z-10 bg-indigo-600 text-white rounded-full p-1 shadow-md"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-50"
                                                x-transition:enter-end="opacity-100 scale-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>

                                            <!-- Imagen -->
                                            <div
                                                class="aspect-w-16 aspect-h-9 w-full bg-gray-200 dark:bg-gray-700 h-40 overflow-hidden relative">
                                                <template x-if="vehicle.image">
                                                    <img :src="vehicle.image"
                                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                </template>
                                                <template x-if="!vehicle.image">
                                                    <div
                                                        class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-12 h-12" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </template>
                                                <!-- Degradado inferior -->
                                                <div
                                                    class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/60 to-transparent">
                                                </div>
                                                <div class="absolute bottom-3 left-4 text-white font-bold tracking-wide"
                                                    x-text="vehicle.plate"></div>
                                            </div>

                                            <!-- Info -->
                                            <div class="p-4">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase"
                                                    x-text="vehicle.label"></h4>
                                                <p class="text-xs text-gray-500 mt-1"
                                                    x-text="'Año: ' + (vehicle.year || 'N/A')"></p>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- No Results Message -->
                                    <div x-show="filteredVehicles.length === 0"
                                        class="col-span-full py-8 text-center text-gray-500">
                                        No se encontraron vehículos que coincidan con tu búsqueda.
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                        </div>

                        <!-- PASO 2: Datos del Viaje -->
                        <div class="space-y-6 pt-8">
                            <div class="flex items-center space-x-3 border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                    2</div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Fechas y
                                    Destino</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Fechas -->
                                <div
                                    class="col-span-1 md:col-span-2 lg:col-span-1 bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 tracking-wider">¿Cuándo lo
                                        necesitas?</h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                                        <div class="relative">
                                            <x-input-label for="start_date" :value="__('Retiro')" class="mb-1.5" />
                                            <input type="datetime-local" id="start_date" name="start_date"
                                                :value="'{{ old('start_date') }}'" required
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-sm py-2.5">
                                        </div>

                                        <div class="hidden md:flex justify-center pt-6 text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </div>

                                        <div class="relative">
                                            <x-input-label for="end_date" :value="__('Devolución')" class="mb-1.5" />
                                            <input type="datetime-local" id="end_date" name="end_date"
                                                :value="'{{ old('end_date') }}'" required
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors text-sm py-2.5">
                                        </div>
                                    </div>
                                    <p class="text-xs text-indigo-500 mt-3 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Verificaremos la disponibilidad al enviar.
                                    </p>
                                </div>

                                <!-- Destino -->
                                <div
                                    class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm h-full">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 tracking-wider">¿A dónde
                                        vas?</h4>

                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="destination_type" :value="__('Tipo de Viaje')"
                                                class="mb-1.5" />
                                            <select id="destination_type" name="destination_type" required
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5">
                                                <option value="" disabled selected>-- Selecciona opción --</option>
                                                <option value="local">🏙️ Local (Urbano)</option>
                                                <option value="outside">🛣️ Fuera de Ciudad (Carretera/Rural)</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('destination_type')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PASO 3: Conductor (Opcional - Admin) -->
                        @if(Auth::user()->role === 'admin')
                            <div class="space-y-6 pt-8">
                                <div class="flex items-center space-x-3 border-b border-gray-200 dark:border-gray-700 pb-4">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                        3</div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Asignación de
                                        Conductor <span class="text-xs font-normal text-gray-500 ml-2">(Opcional)</span>
                                    </h3>
                                </div>

                                <div x-data="{ isThirdParty: false, isNewDriver: false, selectedConductorId: '{{ old('conductor_id') }}' }"
                                    class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

                                    <label
                                        class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors w-fit">
                                        <input type="checkbox" name="is_third_party" x-model="isThirdParty" value="1"
                                            class="rounded text-indigo-600 focus:ring-indigo-500 w-5 h-5 border-gray-300">
                                        <span class="ml-3 font-medium text-gray-700 dark:text-gray-300">Solicitar para un
                                            Tercero / Conductor Externo</span>
                                    </label>

                                    <div x-show="isThirdParty" x-collapse
                                        class="mt-6 pl-4 border-l-4 border-indigo-100 dark:border-indigo-900 space-y-6">

                                        <!-- Tabs Nuevo/Existente -->
                                        <div class="flex space-x-6 border-b border-gray-200 dark:border-gray-700">
                                            <button type="button" @click="isNewDriver = false"
                                                :class="!isNewDriver ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                                class="pb-2 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                                                Buscar Existente
                                            </button>
                                            <button type="button" @click="isNewDriver = true"
                                                :class="isNewDriver ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                                class="pb-2 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                                                Crear Nuevo
                                            </button>
                                        </div>

                                        <!-- Buscador Conductor -->
                                        <div x-show="!isNewDriver" class="max-w-md">
                                            <x-input-label for="conductor_id" :value="__('Seleccionar de la lista')"
                                                class="mb-2" />
                                            <div class="relative">
                                                <select name="conductor_id" x-model="selectedConductorId"
                                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 py-2.5">
                                                    <option value="">-- Seleccionar --</option>
                                                    @foreach($conductors as $conductor)
                                                        <option value="{{ $conductor->id }}">{{ $conductor->nombre }}
                                                            ({{ $conductor->cargo ?? 'Sin cargo' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Formulario Nuevo -->
                                        <div x-show="isNewDriver" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                                            <div>
                                                <x-input-label for="new_conductor_name" :value="__('Nombre Completo')" />
                                                <x-text-input id="new_conductor_name" class="block w-full mt-1" type="text"
                                                    name="new_conductor_name" placeholder="Ej: Juan Pérez" />
                                            </div>
                                            <div>
                                                <x-input-label for="new_conductor_rut" :value="__('RUT (Opcional)')" />
                                                <x-text-input id="new_conductor_rut" class="block w-full mt-1" type="text"
                                                    name="new_conductor_rut" placeholder="Ej: 12.345.678-9" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Botón Flotante / Inferior -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-end">
                            <button type="submit"
                                class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-indigo-600 hover:bg-indigo-700 md:text-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span>Confirmar Solicitud</span>
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* Custom Scrollbar for the vehicle grid */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(229, 231, 235, 0.5);
        /* gray-200 / 0.5 */
        border-radius: 4px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(55, 65, 81, 0.5);
        /* gray-700 / 0.5 */
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #a5b4fc;
        /* indigo-300 */
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #6366f1;
        /* indigo-500 */
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #4f46e5;
        /* indigo-600 */
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #4338ca;
        /* indigo-700 */
    }
</style>