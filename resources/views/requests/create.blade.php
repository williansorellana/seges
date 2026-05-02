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
                        startDate: '{{ old('start_date') }}',
                        endDate: '{{ old('end_date') }}',
                      vehicles: {{ $vehicles->map(function ($vehicle) {
                        return [
                            'id' => $vehicle->id,
                            'label' => $vehicle->brand . ' ' . $vehicle->model,
                            'plate' => $vehicle->plate,
                            'image' => $vehicle->image_path ? asset('storage/' . $vehicle->image_path) : null,
                            'year' => $vehicle->year,
                            'available' => true,
                            'availabilityChecked' => false,
                        ];
                        })->toJson() }},
                        get filteredVehicles() {
                            if (this.search === '') return this.vehicles;
                            return this.vehicles.filter(vehicle => {
                                return vehicle.label.toLowerCase().includes(this.search.toLowerCase()) || 
                                       vehicle.plate.toLowerCase().includes(this.search.toLowerCase());
                            });
                        },
                        async checkAvailability() {
                            // No consultar si aún faltan fechas
                            if (!this.startDate || !this.endDate) return;

                            try {
                                //Armamos parámetros para enviarlos al endpoint
                                const params = new URLSearchParams({
                                    start_date: this.startDate,
                                    end_date: this.endDate
                                });

                                //Llamamos a la ruta que creamos en web.php
                                const response = await fetch(`{{ url('/requests/availability') }}?${params.toString()}`);

                                // Convertimos la respuesta a JSON
                                const data = await response.json();

                                //Recorremos los vehículos actuales y les inyectamos disponibilidad
                                this.vehicles = this.vehicles.map(vehicle => {
                                    const match = data.find(v => v.id === vehicle.id);

                                    return {
                                        ...vehicle,
                                        available: match ? match.available : false,
                                        status_label: match ? match.status_label : 'No disponible',
                                        availabilityChecked: true 
                                    };
                                });

                                // 6) Si el vehículo seleccionado quedó no disponible, lo desmarcamos
                                if (this.selectedId) {
                                    const selectedVehicle = this.vehicles.find(v => v.id == this.selectedId);
                                    if (selectedVehicle && !selectedVehicle.available) {
                                        this.selectedId = '';
                                    }
                                }
                            } catch (error) {
                                // Si algo falla, lo mostramos en consola para depuración
                                console.error('Error checking availability:', error);
                            }
                        }
                    }">

                        <!-- PASO 1: Selección de Vehículo -->
                        <div class="space-y-6">
                            <div
                                class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4 gap-4 md:gap-0">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                        1</div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                                        Selecciona el Vehículo</h3>
                                </div>
                                <!-- Buscador -->
                                <div class="relative w-full md:w-64">
                                    <input type="text" x-model="search" placeholder="Buscar por patente o modelo..."
                                        class="block w-full rounded-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10 py-2">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none pb-0.5">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Input -->
                            <input type="hidden" name="vehicle_id" :value="selectedId">

                            <!-- GRID DE VEHÍCULOS (Scrollable) -->
                            <div class="max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 pb-2">
                                    <template x-for="vehicle in filteredVehicles" :key="vehicle.id">
                                        <div @click="vehicle.available ? selectedId = vehicle.id : selectedId = ''"
                                            class="group relative min-h-[260px] rounded-2xl border-2 transition-all duration-200 ease-in-out overflow-hidden hover:shadow-lg"
                                            :class="[
                                                selectedId == vehicle.id
                                                    ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20 ring-1 ring-indigo-600'
                                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-indigo-300',
                                                vehicle.availabilityChecked && !vehicle.available 
                                                    ? 'opacity-50 cursor-not-allowed'
                                                    : 'cursor-pointer'
                                            ]">
                                                <!-- Availability Badge -->
                                            <div x-show="vehicle.availabilityChecked"
                                                class="absolute top-3 left-3 z-10 px-2 py-1 text-xs font-bold rounded text-white shadow"
                                                :class="vehicle.available ? 'bg-green-600' : 'bg-red-600'">
                                                <span x-text="vehicle.status_label"></span>
                                            </div>

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
                                                    <img x-bin:src="vehicle.image"
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
                                                x-model="startDate" @change="checkAvailability()"
                                                required
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
                                                x-model="endDate" @change="checkAvailability()"
                                                required
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

                                        <!-- Origen -->
                                        <div>
                                            <x-input-label for="origin" :value="__('Origen')"
                                                class="mb-1.5" />
                                            <input type="text" id="origin" name="origin" required value="{{ old('origin') }}"
                                                placeholder="Ej: Oficina Santiago Centro"
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5">
                                            <x-input-error :messages="$errors->get('origin')" class="mt-2" />
                                        </div>

                                        <!-- Destino -->
                                        <div>
                                            <x-input-label for="destination" :value="__('Destino (Opcional)')"
                                                class="mb-1.5" />
                                            <input type="text" id="destination" name="destination"
                                                value="{{ old('destination') }}" placeholder="Ej: Planta Rancagua"
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-2.5">
                                            <x-input-error :messages="$errors->get('destination')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PASO 3: Conductor (Opcional - Admin) -->
                        @if(in_array(Auth::user()->role, ['admin', 'supervisor']))
                            <div class="space-y-6 pt-8">
                                <div class="flex items-center space-x-3 border-b border-gray-200 dark:border-gray-700 pb-4">
                                    <div
                                        class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                                        3</div>
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Asignación de
                                        Conductor <span class="text-xs font-normal text-gray-500 ml-2">(Opcional)</span>
                                    </h3>
                                </div>

                                <div x-data="{ 
                                    isThirdParty: false, 
                                    isNewDriver: false, 
                                    selectedConductorId: '{{ old('conductor_id') }}',
                                    newConductorRut: '{{ old('new_conductor_rut') }}',
                                    formatRut() {
                                        let value = this.newConductorRut.replace(/[^0-9kK]/g, '').toUpperCase();
                                        if (value.length > 1) {
                                            const dv = value.slice(-1);
                                            let body = value.slice(0, -1);
                                            body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                            this.newConductorRut = body + '-' + dv;
                                        } else {
                                            this.newConductorRut = value;
                                        }
                                    }
                                }"
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
                                                    name="new_conductor_rut" x-model="newConductorRut" @input="formatRut()" placeholder="Ej: 12.345.678-9" />
                                            </div>
                                            
                                            {{-- Checkbox para guardar permanentemente --}}
                                            <div class="md:col-span-2 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-800">
                                                <label class="flex items-start cursor-pointer">
                                                    <input type="checkbox" name="save_conductor_permanently" value="1"
                                                        class="rounded text-indigo-600 focus:ring-indigo-500 w-5 h-5 mt-0.5 border-gray-300">
                                                    <span class="ml-3">
                                                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            Guardar conductor permanentemente
                                                        </span>
                                                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            Si marca esta opción, el conductor se agregará a la lista de conductores para futuros viajes.
                                                            Si no la marca, solo se usará para este viaje.
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- PASO 4: Acompañantes (Opcional) --}}
                    <div class="space-y-6 pt-8" x-data="{
                        companions: [],
                        addCompanion() {
                            if (this.companions.length < 5) {
                                this.companions.push({ 
                                    type: 'internal', 
                                    user_id: '', 
                                    external_name: '', 
                                    external_rut: '',
                                    error_message: null,
                                    error_class: null
                                });
                            }
                        },
                        removeCompanion(index) {
                            this.companions.splice(index, 1);
                        },
                        formatRut(index) {
                            let value = this.companions[index].external_rut.replace(/[^0-9kK]/g, '').toUpperCase();
                            if (value.length > 1) {
                                const dv = value.slice(-1);
                                let body = value.slice(0, -1);
                                body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                this.companions[index].external_rut = body + '-' + dv;
                            } else {
                                this.companions[index].external_rut = value;
                            }
                        },
                        async checkRut(index) {
                            const rut = this.companions[index].external_rut;
                            if (!rut || rut.length < 8) return;

                            // Reset error state
                            this.companions[index].error_message = null;
                            this.companions[index].error_class = null;

                            try {
                                const response = await fetch(`{{ route('requests.check-external-rut') }}?rut=${rut}`);
                                const data = await response.json();

                                if (data.exists) {
                                    this.companions[index].error_message = data.message;
                                    this.companions[index].error_class = data.bg_class;
                                    // Optional: clear the RUT to force correct selection
                                    // this.companions[index].external_rut = ''; 
                                }
                            } catch (error) {
                                console.error('Error validation RUT:', error);
                            }
                        }
                    }">
                        <div class="flex items-center space-x-3 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">4</div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                                Acompañantes <span class="text-xs font-normal text-gray-500 ml-2">(Opcional - Máx. 5)</span>
                            </h3>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            {{-- Botón Agregar Acompañante --}}
                            <button type="button" @click="addCompanion()" 
                                x-show="companions.length < 5"
                                class="mb-4 inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar Acompañante
                            </button>

                            {{-- Lista de Acompañantes --}}
                            <div class="space-y-4">
                                <template x-for="(companion, index) in companions" :key="index">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start justify-between mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                Acompañante <span x-text="index + 1"></span>
                                            </h4>
                                            <button type="button" @click="removeCompanion(index)"
                                                class="text-red-600 hover:text-red-800 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        {{-- Toggle: Interno o Externo --}}
                                        <div class="flex space-x-4 mb-4">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" :name="'companion_type_' + index" value="internal" 
                                                    x-model="companions[index].type"
                                                    class="text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Usuario Interno</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" :name="'companion_type_' + index" value="external"
                                                    x-model="companions[index].type"
                                                    class="text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Persona Externa</span>
                                            </label>
                                        </div>

                                        {{-- Usuario Interno --}}
                                        <div x-show="companions[index].type === 'internal'" x-collapse>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Seleccionar Usuario</label>
                                            <select :name="'companions[' + index + '][user_id]'" x-model="companions[index].user_id"
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">-- Seleccione un usuario --</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} {{ $user->last_name }} - {{ $user->cargo ?? 'Sin cargo' }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Persona Externa --}}
                                        <div x-show="companions[index].type === 'external'" x-collapse class="space-y-3">
                                            {{-- Select Persona Frecuente --}}
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Persona Frecuente (Opcional)</label>
                                                <select :name="'companions[' + index + '][frequent_person_id]'" 
                                                    @change="if($event.target.value) { 
                                                        const person = {{ $frequentExternalPersons->toJson() }}.find(p => p.id == $event.target.value);
                                                        if(person) {
                                                            companions[index].external_name = person.name;
                                                            companions[index].external_rut = person.rut;
                                                        }
                                                    }"
                                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">-- O ingresar manualmente abajo --</option>
                                                    @foreach($frequentExternalPersons as $person)
                                                        <option value="{{ $person->id }}">{{ $person->name }} {{ $person->rut ? '- ' . $person->rut : '' }}</option>
                                                    @endforeach
                                                </select>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Si seleccionas una persona frecuente, se cargarán sus datos automáticamente</p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nombre Completo</label>
                                                <input type="text" :name="'companions[' + index + '][external_name]'" x-model="companions[index].external_name"
                                                    placeholder="Ej: Juan Pérez"
                                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">RUT</label>
                                                <input type="text" :name="'companions[' + index + '][external_rut]'" x-model="companions[index].external_rut"
                                                    @input="formatRut(index)"
                                                    @blur="checkRut(index)"
                                                    placeholder="Ej: 12.345.678-9"
                                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                                
                                                <!-- Mensaje de Error / Advertencia -->
                                                <div x-show="companions[index].error_message" 
                                                     x-transition
                                                     :class="companions[index].error_class || 'bg-red-100 text-red-800 border-red-200'"
                                                     class="mt-2 p-3 rounded-lg border text-sm flex items-start gap-2">
                                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span x-text="companions[index].error_message"></span>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cargo (Opcional)</label>
                                                    <input type="text" :name="'companions[' + index + '][external_position]'" x-model="companions[index].external_position"
                                                        placeholder="Ej: Supervisor"
                                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Departamento/Empresa (Opcional)</label>
                                                    <input type="text" :name="'companions[' + index + '][external_department]'" x-model="companions[index].external_department"
                                                        placeholder="Ej: Contratista Z"
                                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                            </div>

                                            {{-- Checkbox para guardar como frecuente --}}
                                            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                                <label class="flex items-start cursor-pointer">
                                                    <input type="checkbox" :name="'companions[' + index + '][save_as_frequent]'" value="1"
                                                        class="rounded text-green-600 focus:ring-green-500 w-5 h-5 mt-0.5 border-gray-300">
                                                    <span class="ml-3">
                                                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            💾 Guardar como persona frecuente
                                                        </span>
                                                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            Si marca esta opción, podrá seleccionar rápidamente a esta persona en futuros viajes
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Empty State --}}
                                <div x-show="companions.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="mt-2 text-sm">No hay acompañantes agregados</p>
                                    <p class="text-xs mt-1">Puedes agregar hasta 5 personas para este viaje</p>
                                </div>
                            </div>
                        </div>
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