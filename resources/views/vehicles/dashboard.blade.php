<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Panel de Vehículos') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openViewModal: false, viewingVehicle: {} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 mb-12 border border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 divide-x-0 md:divide-x divide-gray-700">
                    <div class="text-center px-4">
                        @if(Auth::user()->role === 'viewer')
                            <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Disponibles</span>
                                <span class="block text-4xl font-black text-emerald-500 transition-transform duration-200">{{ $countDisponible }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'available']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Disponibles</span>
                                <span class="block text-4xl font-black text-emerald-500 group-hover:scale-110 transition-transform duration-200">{{ $countDisponible }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role === 'viewer')
                             <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Reservados</span>
                                <span class="block text-4xl font-black text-blue-500 transition-transform duration-200">{{ $countAsignado }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'occupied']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Reservados</span>
                                <span class="block text-4xl font-black text-blue-500 group-hover:scale-110 transition-transform duration-200">{{ $countAsignado }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role === 'viewer')
                             <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Fuera de Servicio</span>
                                <span class="block text-4xl font-black text-red-500 transition-transform duration-200">{{ $countFueraDeServicio }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'out_of_service']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Fuera de Servicio</span>
                                <span class="block text-4xl font-black text-red-500 group-hover:scale-110 transition-transform duration-200">{{ $countFueraDeServicio }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role === 'viewer')
                            <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Mantenimiento</span>
                                <span class="block text-4xl font-black text-amber-500 transition-transform duration-200">{{ $countMantenimiento }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'maintenance']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Mantenimiento</span>
                                <span class="block text-4xl font-black text-amber-500 group-hover:scale-110 transition-transform duration-200">{{ $countMantenimiento }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($vehicles as $vehicle)
                    @php 
                        // USAMOS EL ESTADO DIRECTO DE LA BD PARA EVITAR ERRORES
                        $status = $vehicle->status; 
                    @endphp
                    <div class="bg-gray-800 border border-gray-700 rounded-3xl overflow-hidden hover:ring-2 hover:ring-indigo-500 transition-all duration-300 group shadow-2xl">
                        <div class="relative h-48 bg-gray-900 overflow-hidden">
                            @if($vehicle->image_path)
                                <img src="{{ Storage::url($vehicle->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-700 font-black text-2xl uppercase italic">Sin Foto</div>
                            @endif
                            
                            <div class="absolute top-4 left-4 flex flex-col items-start gap-1">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black tracking-widest border
                                    {{ $status === 'available' ? 'text-green-400 bg-green-900/30 border-green-500/50' : '' }}
                                    {{ $status === 'out_of_service' ? 'text-red-400 bg-red-900/30 border-red-500/50' : '' }}
                                    {{ $status === 'maintenance' ? 'text-yellow-400 bg-yellow-900/30 border-yellow-500/50' : '' }}
                                    {{ $status === 'occupied' ? 'text-blue-400 bg-blue-900/30 border-blue-500/50' : '' }}">
                                    
                                    @switch($status)
                                        @case('available') DISPONIBLE @break
                                        @case('out_of_service') FUERA DE SERVICIO @break
                                        @case('maintenance') MANTENCIÓN @break
                                        @case('occupied') RESERVADO @break
                                        @default {{ strtoupper($status) }}
                                    @endswitch
                                </span>
                                
                                @if($status === 'occupied' && $vehicle->active_reservation)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold tracking-wider text-blue-200 bg-blue-900/80 border border-blue-500/30 backdrop-blur-sm">
                                        {{ Str::limit($vehicle->active_reservation->user->name, 15) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-2xl font-black text-white tracking-tighter uppercase">{{ $vehicle->plate }}</h3>
                                <span class="text-[10px] font-bold text-gray-500 bg-gray-900 px-2 py-1 rounded border border-gray-700">{{ $vehicle->year }}</span>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-700/50">
                                <div>
                                    <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Kilometraje</span>
                                    <span class="text-sm font-mono text-gray-100">{{ number_format($vehicle->mileage, 0, '', '.') }} KM</span>
                                </div>
                                <div class="text-right mr-4">
                                     <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Eficiencia</span>
                                    @if($vehicle->average_efficiency)
                                        <span class="text-sm font-bold text-blue-400">{{ number_format($vehicle->average_efficiency, 1, ',', '.') }} km/L</span>
                                    @else
                                        <span class="text-xs text-gray-600 italic">--</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2 mt-2 border-t border-gray-700/30">
                                <div>
                                    <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Costo/Km</span>
                                    @if($vehicle->cost_per_km)
                                        <span class="text-xs font-bold text-gray-300">${{ number_format($vehicle->cost_per_km, 0, '', '.') }}</span>
                                    @else
                                        <span class="text-[10px] text-gray-600 italic">--</span>
                                    @endif
                                </div>
                                <div class="text-right mr-4">
                                    <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Mantención en</span>
                                    @php $dist = $vehicle->maintenance_remaining_km; @endphp
                                    @if($dist !== null)
                                        <span class="text-xs font-bold {{ $dist < 0 ? 'text-red-500' : ($dist < 1000 ? 'text-yellow-500' : 'text-emerald-400') }}">
                                            {{ number_format($dist, 0, '', '.') }} km
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-600 italic">No Data</span>
                                    @endif
                                </div>
                                @if(Auth::user()->role !== 'viewer')
                                <button @click="viewingVehicle = { 
                                    plate: '{{ $vehicle->plate }}', 
                                    brand: '{{ $vehicle->brand }}', 
                                    model: '{{ $vehicle->model }}', 
                                    year: {{ $vehicle->year }}, 
                                    mileage: {{ $vehicle->mileage }}, 
                                    status: '{{ $status }}', 
                                    imageUrl: '{{ $vehicle->image_path ? Storage::url($vehicle->image_path) : '' }}',
                                    assignedUser: '{{ ($status === 'occupied' && $vehicle->active_reservation) ? $vehicle->active_reservation->user->name : '' }}'
                                }; openViewModal = true" class="p-2 bg-gray-700 hover:bg-emerald-600 rounded-xl text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-gray-900/40 rounded-3xl border-2 border-dashed border-gray-800 flex flex-col items-center">
                        <p class="text-gray-500 text-xs uppercase tracking-[0.2em]">No hay vehículos activos para mostrar</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div x-show="openViewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="openViewModal = false"></div>
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden z-50">
                <div class="p-8">
                    <h2 class="text-xl font-bold text-white mb-6 border-b border-gray-700 pb-2">Ficha del Vehículo</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-gray-900 rounded-xl p-2 border border-gray-700">
                            <template x-if="viewingVehicle.imageUrl">
                                <img :src="viewingVehicle.imageUrl" class="w-full h-56 object-cover rounded-lg">
                            </template>
                            <template x-if="!viewingVehicle.imageUrl">
                                <div class="w-full h-56 flex items-center justify-center bg-gray-800 text-gray-500 italic rounded-lg">Sin Imagen</div>
                            </template>
                        </div>
                        <div class="space-y-4 text-white">
                            <div><span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Patente</span><span class="text-2xl font-black text-indigo-400" x-text="viewingVehicle.plate"></span></div>
                            <div><span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Marca / Modelo</span><span class="text-lg" x-text="viewingVehicle.brand + ' ' + viewingVehicle.model"></span></div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Estado</span>
                                <span class="text-lg font-bold" 
                                    :class="{
                                        'text-green-400': viewingVehicle.status === 'available',
                                        'text-blue-400': viewingVehicle.status === 'occupied',
                                        'text-red-400': viewingVehicle.status === 'out_of_service',
                                        'text-yellow-400': viewingVehicle.status === 'maintenance'
                                    }"
                                    x-text="viewingVehicle.status === 'occupied' ? 'RESERVADO' : (viewingVehicle.status === 'out_of_service' ? 'FUERA DE SERVICIO' : (viewingVehicle.status === 'available' ? 'DISPONIBLE' : viewingVehicle.status.toUpperCase()))">
                                </span>
                            </div>
                            
                            <!-- Assigned User (Visible only if reserved) -->
                            <template x-if="viewingVehicle.status === 'occupied' && viewingVehicle.assignedUser">
                                <div>
                                    <span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Asignado a</span>
                                    <span class="text-lg font-bold text-blue-300" x-text="viewingVehicle.assignedUser"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <a :href="`{{ route('vehicles.index') }}?search=${viewingVehicle.plate}`" 
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition uppercase text-xs tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Ver Detalles Completos
                        </a>
                        <button @click="openViewModal = false" class="px-8 py-2 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition uppercase text-xs tracking-widest">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>