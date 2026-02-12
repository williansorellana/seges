<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Dashboard de Activos') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openViewModal: false, viewingAsset: {} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Statistics Cards -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 mb-12 border border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 divide-x-0 md:divide-x divide-gray-700">
                    <div class="text-center px-4">
                        @if(Auth::user()->role === 'viewer')
                            <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Disponibles</span>
                                <span class="block text-4xl font-black text-emerald-500 transition-transform duration-200">{{ $countDisponible }}</span>
                            </div>
                        @else
                            <a href="{{ route('assets.index', ['estado' => 'available']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Disponibles</span>
                                <span class="block text-4xl font-black text-emerald-500 group-hover:scale-110 transition-transform duration-200">{{ $countDisponible }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role === 'viewer')
                            <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Asignados</span>
                                <span class="block text-4xl font-black text-blue-500 transition-transform duration-200">{{ $countAsignado }}</span>
                            </div>
                        @else
                            <a href="{{ route('assets.index', ['estado' => 'assigned']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Asignados</span>
                                <span class="block text-4xl font-black text-blue-500 group-hover:scale-110 transition-transform duration-200">{{ $countAsignado }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role === 'viewer')
                             <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Dados de Baja</span>
                                <span class="block text-4xl font-black text-red-500 transition-transform duration-200">{{ $countBaja }}</span>
                            </div>
                        @else
                            <a href="{{ route('assets.index', ['estado' => 'written_off']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Dados de Baja</span>
                                <span class="block text-4xl font-black text-red-500 group-hover:scale-110 transition-transform duration-200">{{ $countBaja }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role === 'viewer')
                             <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Mantención</span>
                                <span class="block text-4xl font-black text-amber-500 transition-transform duration-200">{{ $countMantenimiento }}</span>
                            </div>
                        @else
                            <a href="{{ route('assets.index', ['estado' => 'maintenance']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Mantención</span>
                                <span class="block text-4xl font-black text-amber-500 group-hover:scale-110 transition-transform duration-200">{{ $countMantenimiento }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Asset Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($assets as $asset)
                    @php 
                        $status = $asset->estado; 
                    @endphp
                    <div class="bg-gray-800 border border-gray-700 rounded-3xl overflow-hidden hover:ring-2 hover:ring-indigo-500 transition-all duration-300 group shadow-2xl relative">
                        <!-- Clickable Area for Card (except buttons) -->
                        <div class="cursor-pointer" 
                            @if(Auth::user()->role !== 'viewer')
                                @click="viewingAsset = {{ Js::from($asset) }}; viewingAsset.imageUrl = '{{ $asset->foto_path ? Storage::url($asset->foto_path) : '' }}'; viewingAsset.categoryName = '{{ $asset->category->nombre ?? 'N/A' }}'; openViewModal = true"
                            @endif
                            >
                            <div class="relative h-48 bg-gray-900 overflow-hidden">
                                @if($asset->foto_path)
                                    <img src="{{ Storage::url($asset->foto_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-700 font-black text-2xl uppercase italic">Sin Foto</div>
                                @endif
                                
                                <div class="absolute top-4 left-4 flex flex-col items-start gap-1">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black tracking-widest border
                                        {{ $status === 'available' ? 'text-green-400 bg-green-900/30 border-green-500/50' : '' }}
                                        {{ $status === 'written_off' ? 'text-red-400 bg-red-900/30 border-red-500/50' : '' }}
                                        {{ $status === 'maintenance' ? 'text-yellow-400 bg-yellow-900/30 border-yellow-500/50' : '' }}
                                        {{ $status === 'assigned' ? 'text-blue-400 bg-blue-900/30 border-blue-500/50' : '' }}">
                                        
                                        @switch($status)
                                            @case('available') DISPONIBLE @break
                                            @case('written_off') DADO DE BAJA @break
                                            @case('maintenance') MANTENCIÓN @break
                                            @case('assigned') ASIGNADO @break
                                            @default {{ strtoupper($status) }}
                                        @endswitch
                                    </span>
                                    
                                    @if($status === 'assigned' && $asset->activeAssignment)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold tracking-wider text-blue-200 bg-blue-900/80 border border-blue-500/30 backdrop-blur-sm">
                                            {{ Str::limit($asset->activeAssignment->assigned_to_name, 15) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-lg font-black text-white tracking-tighter uppercase truncate pr-2" title="{{ $asset->nombre }}">{{ $asset->nombre }}</h3>
                                    <span class="text-[10px] font-bold text-gray-500 bg-gray-900 px-2 py-1 rounded border border-gray-700 uppercase">{{ $asset->codigo_interno }}</span>
                                </div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">{{ $asset->marca }} {{ $asset->modelo }}</p>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-gray-700/50">
                                    <div>
                                        <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Categoría</span>
                                        <span class="text-sm font-mono text-gray-100">{{ $asset->category->nombre ?? 'N/A' }}</span>
                                    </div>
                                    <div class="text-right">
                                         <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Ubicación</span>
                                         <span class="text-sm font-bold text-gray-300">{{ Str::limit($asset->ubicacion ?? 'N/A', 15) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-gray-900/40 rounded-3xl border-2 border-dashed border-gray-800 flex flex-col items-center">
                        <p class="text-gray-500 text-xs uppercase tracking-[0.2em]">No hay activos para mostrar</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Detail Modal -->
        <div x-show="openViewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="openViewModal = false"></div>
            <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden z-50">
                <div class="p-8">
                    <h2 class="text-xl font-bold text-white mb-6 border-b border-gray-700 pb-2">Ficha del Activo</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-gray-900 rounded-xl p-2 border border-gray-700">
                            <template x-if="viewingAsset.imageUrl">
                                <img :src="viewingAsset.imageUrl" class="w-full h-56 object-cover rounded-lg">
                            </template>
                            <template x-if="!viewingAsset.imageUrl">
                                <div class="w-full h-56 flex items-center justify-center bg-gray-800 text-gray-500 italic rounded-lg">Sin Imagen</div>
                            </template>
                        </div>
                        <div class="space-y-4 text-white">
                            <div><span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Código Interno</span><span class="text-2xl font-black text-indigo-400" x-text="viewingAsset.codigo_interno"></span></div>
                            <div><span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Nombre</span><span class="text-lg" x-text="viewingAsset.nombre"></span></div>
                            <div><span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Marca / Modelo</span><span class="text-lg" x-text="(viewingAsset.marca || '') + ' ' + (viewingAsset.modelo || '')"></span></div>
                            <div><span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Categoría</span><span class="text-lg" x-text="viewingAsset.categoryName || 'N/A'"></span></div>
                            
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Estado</span>
                                <span class="text-lg font-bold" 
                                    :class="{
                                        'text-green-400': viewingAsset.estado === 'available',
                                        'text-blue-400': viewingAsset.estado === 'assigned',
                                        'text-red-400': viewingAsset.estado === 'written_off',
                                        'text-yellow-400': viewingAsset.estado === 'maintenance'
                                    }"
                                    x-text="viewingAsset.estado === 'assigned' ? 'ASIGNADO' : (viewingAsset.estado === 'written_off' ? 'DADO DE BAJA' : (viewingAsset.estado === 'available' ? 'DISPONIBLE' : (viewingAsset.estado === 'maintenance' ? 'MANTENCIÓN' : viewingAsset.estado)))">
                                </span>
                            </div>
                            
                            <!-- Assigned User (Visible only if assigned) -->
                             <template x-if="viewingAsset.estado === 'assigned' && viewingAsset.active_assignment">
                                <div>
                                    <span class="block text-[10px] text-gray-400 uppercase tracking-widest font-bold">Asignado a</span>
                                    <span class="text-lg font-bold text-blue-300" x-text="viewingAsset.active_assignment.assigned_to_name"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        @if(Auth::user()->role !== 'viewer')
                        <a :href="`{{ route('assets.index') }}?search=${viewingAsset.codigo_interno}`" 
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition uppercase text-xs tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Ver Detalles Completos
                        </a>
                        @endif
                        <button @click="openViewModal = false" class="px-8 py-2 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition uppercase text-xs tracking-widest">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
