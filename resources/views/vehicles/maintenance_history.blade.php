<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100" x-data="{ 
                    width: window.innerWidth,
                    activeTab: '{{ request('tab', 'maintenance') }}',
                    lightboxOpen: false,
                    currentPhoto: '',
                    allPhotos: [],
                    currentIndex: 0,
                    
                    // Modal de Detalle
                    detailModalOpen: false,
                    selectedReturn: null,
                    
                    openDetailModal(returnItem) {
                        this.selectedReturn = returnItem;
                        this.detailModalOpen = true;
                        document.body.style.overflow = 'hidden';
                    },
                    
                    closeDetailModal() {
                        this.detailModalOpen = false;
                        this.selectedReturn = null;
                        document.body.style.overflow = 'auto';
                    },

                    // Modal de Término Anticipado
                    terminationModalOpen: false,
                    terminationReason: '',
                    terminationOriginalDate: '',

                    openTerminationModal(reason, originalDate) {
                        this.terminationReason = reason;
                        this.terminationOriginalDate = originalDate;
                        this.terminationModalOpen = true;
                        document.body.style.overflow = 'hidden';
                    },

                    closeTerminationModal() {
                        this.terminationModalOpen = false;
                        this.terminationReason = '';
                        this.terminationOriginalDate = '';
                        document.body.style.overflow = 'auto';
                    },

                    openLightbox(photoUrl, photos, index) {
                        this.currentPhoto = photoUrl;
                        this.allPhotos = photos;
                        this.currentIndex = index;
                        this.lightboxOpen = true;
                        // document.body.style.overflow = 'hidden'; // Ya gestionado por el modal si se abre desde ahí
                    },
                    
                    closeLightbox() {
                        this.lightboxOpen = false;
                        if (!this.detailModalOpen) {
                            document.body.style.overflow = 'auto';
                        }
                    },
                    
                    nextPhoto() {
                        if (this.currentIndex < this.allPhotos.length - 1) {
                            this.currentIndex++;
                            this.currentPhoto = this.allPhotos[this.currentIndex];
                        }
                    },
                    
                    prevPhoto() {
                        if (this.currentIndex > 0) {
                            this.currentIndex--;
                            this.currentPhoto = this.allPhotos[this.currentIndex];
                        }
                    },
                    
                    // Helpers
                    formatDate(dateString) {
                         if (!dateString) return '-';
                         return new Date(dateString).toLocaleDateString('es-CL', {
                             day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                         });
                    },
                    
                    formatNumber(num) {
                        return new Intl.NumberFormat('es-CL').format(num);
                    }
                }" @resize.window="width = window.innerWidth" @keydown.escape.window="closeLightbox(); closeDetailModal()" @keydown.arrow-right.window="lightboxOpen && nextPhoto()"
                    @keydown.arrow-left.window="lightboxOpen && prevPhoto()">
                    
                    <!-- Header y Filtros (IGUAL QUE ANTES) -->
                    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <div class="mb-2">
                                <a href="{{ route('vehicles.index') }}"
                                    class="inline-flex items-center text-gray-400 hover:text-white transition ease-in-out duration-150">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Volver
                                </a>
                            </div>
                            <h2 class="text-xl font-bold text-yellow-500 flex items-center">
                                Historial del Vehículo
                            </h2>
                        </div>

                       <!-- Formulario de Filtros (IGUAL QUE ANTES) -->
                        <form method="GET" action="{{ route('vehicles.maintenance.history', $vehicle->id) }}"
                            class="flex flex-wrap items-end gap-2 bg-gray-900 p-3 rounded-lg border border-gray-700">
                            
                            <input type="hidden" name="tab" x-model="activeTab">

                            <div>
                                <label for="start_date" class="block text-xs text-gray-400 mb-1">Desde</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2">
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs text-gray-400 mb-1">Hasta</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2">
                            </div>

                            <div x-show="activeTab === 'returns' || '{{ request('has_damages') }}' !== ''" x-transition>
                                <label for="has_damages" class="block text-xs text-gray-400 mb-1">¿Con Daños?</label>
                                <select id="has_damages" name="has_damages"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2">
                                    <option value="">Todos</option>
                                    <option value="yes" {{ request('has_damages') === 'yes' ? 'selected' : '' }}>Sí, con daños</option>
                                    <option value="no" {{ request('has_damages') === 'no' ? 'selected' : '' }}>No, sin daños</option>
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded transition-colors flex items-center h-[38px] mt-auto">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filtrar
                                </button>
                                
                                <a :href="'{{ route('vehicles.maintenance.history.pdf', $vehicle->id) }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}&has_damages={{ request('has_damages') }}&tab=' + activeTab"
                                   target="_blank"
                                   class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors flex items-center h-[38px] mt-auto" 
                                   title="Exportar a PDF">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PDF
                                </a>

                                <a href="{{ route('vehicles.maintenance.history', ['vehicle' => $vehicle->id, 'tab' => request('tab', 'maintenance')]) }}"
                                    class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium rounded transition-colors flex items-center h-[38px] mt-auto" title="Limpiar Filtros">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Detalles del Vehículo (IGUAL QUE ANTES) -->
                    <div class="bg-gray-900 rounded-lg p-4 mb-6 border border-gray-700 flex items-center gap-4">
                         @if($vehicle->image_path)
                            <img src="{{ Storage::url($vehicle->image_path) }}" alt="Foto" 
                                class="w-16 h-16 object-cover rounded-full border-2 border-gray-600">
                        @else
                            <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center border-2 border-gray-600">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Vehículo</span>
                                <span class="text-white font-bold">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Patente</span>
                                <span class="font-mono text-yellow-400 font-bold">{{ $vehicle->plate }}</span>
                            </div>
                             <div>
                                <span class="block text-gray-500 text-xs uppercase">Año</span>
                                <span class="text-white">{{ $vehicle->year }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Kilometraje</span>
                                <span class="text-green-400 font-bold">{{ number_format($vehicle->mileage, 0, '', '.') }} km</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation (IGUAL QUE ANTES) -->
                    <div class="border-b border-gray-700 mb-6 overflow-x-auto overflow-y-hidden [&::-webkit-scrollbar]:hidden" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button @click="activeTab = 'maintenance'" :class="activeTab === 'maintenance' ? 'border-yellow-500 text-yellow-500' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Mantenciones
                            </button>
                            <button @click="activeTab = 'usage'" :class="activeTab === 'usage' ? 'border-blue-500 text-blue-500' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Historial de Uso
                            </button>
                            <button @click="activeTab = 'returns'" :class="activeTab === 'returns' ? 'border-green-500 text-green-500' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                Devoluciones (Entregas)
                            </button>
                        </nav>
                    </div>

                    <!-- Tabla de Historial Mantenimiento (IGUAL QUE ANTES) -->
                    <div x-show="activeTab === 'maintenance'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        @if($requests->isEmpty())
                           <!-- Empty State -->
                           <div class="flex flex-col items-center justify-center py-12 text-gray-500 bg-gray-900 rounded-lg border border-dashed border-gray-700">
                                <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                <p class="text-lg font-medium">No hay registros de mantenimiento.</p>
                                @if(request('start_date') || request('end_date')) <p class="text-sm text-gray-400 mt-2">Prueba cambiando los filtros de fecha.</p> @endif
                            </div>
                        @else
                            <div class="bg-gray-900 rounded-lg shadow-xl border border-gray-700">
                                {{-- Vista Desktop --}}
                                <div x-show="width >= 768" class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-700">
                                        <thead class="bg-gray-800">
                                            <tr>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Fecha Solicitud</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tipo</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Descripción / Detalle</th>
                                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Estado</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Completado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700 bg-transparent">
                                            @foreach($requests as $req)
                                                <tr class="hover:bg-gray-800/50 transition duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                        {{ $req->created_at->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        @switch($req->type)
                                                            @case('oil') <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-900/50 text-yellow-300 border border-yellow-700">🛢️ Aceite</span> @break
                                                            @case('tires') <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900/50 text-blue-300 border border-blue-700">🛞 Neumáticos</span> @break
                                                            @case('mechanics') <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-900/50 text-red-300 border border-red-700">🔧 Mecánica</span> @break
                                                            @default <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-700 text-gray-200 border border-gray-600">📋 General</span>
                                                        @endswitch
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-300 max-w-md">
                                                        <div class="line-clamp-2" title="{{ $req->description }}">{{ $req->description }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        @switch($req->status)
                                                            @case('pending')
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    <span class="w-2 h-2 mr-1 bg-yellow-500 rounded-full animate-pulse"></span> Pendiente
                                                                </span>
                                                                @break
                                                            @case('in_progress')
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    <span class="w-2 h-2 mr-1 bg-blue-500 rounded-full animate-pulse"></span> En Taller
                                                                </span>
                                                                @break
                                                            @case('completed')
                                                                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                    Finalizado
                                                                </span>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                                        @if($req->status === 'completed')
                                                            {{ $req->updated_at->format('d/m/Y H:i') }}
                                                        @else
                                                            <span class="text-xs italic text-gray-500">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Vista Mobile (Tarjetas) --}}
                                <div x-show="width < 768" class="space-y-4 p-4">
                                    @foreach($requests as $req)
                                        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow-sm">
                                            {{-- Header --}}
                                            <div class="flex justify-between items-start mb-3 border-b border-gray-700 pb-2">
                                                <div class="text-sm font-bold text-white">
                                                    {{ $req->created_at->format('d/m/Y') }}
                                                </div>
                                                <div>
                                                    @switch($req->status)
                                                        @case('pending')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-yellow-100 text-yellow-800">
                                                                Pendiente
                                                            </span>
                                                            @break
                                                        @case('in_progress')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800">
                                                                En Taller
                                                            </span>
                                                            @break
                                                        @case('completed')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800">
                                                                Finalizado
                                                            </span>
                                                            @break
                                                    @endswitch
                                                </div>
                                            </div>

                                            {{-- Detalles --}}
                                            <div class="space-y-3">
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-400">Tipo:</span>
                                                    <div>
                                                        @switch($req->type)
                                                            @case('oil') <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-yellow-900/50 text-yellow-300 border border-yellow-700">🛢️ Aceite</span> @break
                                                            @case('tires') <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-blue-900/50 text-blue-300 border border-blue-700">🛞 Neumáticos</span> @break
                                                            @case('mechanics') <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-red-900/50 text-red-300 border border-red-700">🔧 Mecánica</span> @break
                                                            @default <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-gray-700 text-gray-200 border border-gray-600">📋 General</span>
                                                        @endswitch
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <span class="text-xs text-gray-500 block mb-1">Descripción:</span>
                                                    <p class="text-sm text-gray-300 bg-gray-900/50 p-2 rounded border border-gray-700/50">
                                                        {{ $req->description }}
                                                    </p>
                                                </div>

                                                @if($req->status === 'completed')
                                                    <div class="flex justify-between text-sm pt-2 border-t border-gray-700/50">
                                                        <span class="text-gray-400">Completado el:</span>
                                                        <span class="text-gray-200">{{ $req->updated_at->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tabla de Historial Uso (IGUAL QUE ANTES) -->
                    <div x-show="activeTab === 'usage'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                        @if($usageHistory->isEmpty())
                             <!-- Empty State -->
                             <div class="flex flex-col items-center justify-center py-12 text-gray-500 bg-gray-900 rounded-lg border border-dashed border-gray-700">
                                <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-lg font-medium">No hay registros de uso.</p>
                                @if(request('start_date') || request('end_date')) <p class="text-sm text-gray-400 mt-2">Prueba cambiando los filtros de fecha.</p> @endif
                                @if(request('has_damages')) <p class="text-sm text-gray-400 mt-2">El filtro de daños puede estar ocultando resultados sin reporte.</p> @endif
                            </div>
                        @else
                            <div class="bg-gray-900 rounded-lg shadow-xl border border-gray-700">
                                {{-- Vista Desktop --}}
                                <div x-show="width >= 768" class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-700">
                                        <thead class="bg-gray-800">
                                            <tr>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Solicitado Por</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Fecha Inicio</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Fecha Término</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Destino / Uso</th>
                                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700 bg-transparent">
                                            @foreach($usageHistory as $usage)
                                                <tr class="hover:bg-gray-800/50 transition duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-white">
                                                            @if($usage->conductor)
                                                                Conductor: {{ $usage->conductor->nombre }}
                                                            @elseif($usage->user)
                                                                {{ $usage->user->name }}
                                                            @else
                                                                Usuario Eliminado
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                        {{ $usage->start_date ? $usage->start_date->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                        {{ $usage->end_date ? $usage->end_date->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                       {{ ucfirst($usage->destination_type ?? 'Uso General') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        @switch($usage->status)
                                                            @case('approved') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">En Curso / Aprobado</span> @break
                                                            @case('completed') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Finalizado</span> @break
                                                            @case('pending') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pendiente</span> @break
                                                            @case('rejected') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rechazado</span> @break
                                                            @case('cancelled') <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Cancelado</span> @break
                                                            @default <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500 text-white">{{ ucfirst($usage->status) }}</span>
                                                        @endswitch
                                                        @if($usage->early_termination_reason)
                                                            <div class="mt-2 text-center">
                                                                <button @click="openTerminationModal('{{ addslashes($usage->early_termination_reason) }}', '{{ $usage->original_end_date ? $usage->original_end_date->format('d/m/Y') : '-' }}')"
                                                                    class="inline-flex items-center px-2.5 py-1 rounded bg-red-900/30 text-red-300 border border-red-800 hover:bg-red-900/50 transition-colors text-xs">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                    Ver motivo término
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Vista Mobile (Tarjetas) --}}
                                <div x-show="width < 768" class="space-y-4 p-4">
                                    @foreach($usageHistory as $usage)
                                        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow-sm">
                                            {{-- Header --}}
                                            <div class="flex justify-between items-start mb-3 border-b border-gray-700 pb-2">
                                                <div class="text-sm font-bold text-white">
                                                    @if($usage->conductor)
                                                        {{ $usage->conductor->nombre }} (C)
                                                    @elseif($usage->user)
                                                        {{ $usage->user->name }}
                                                    @else
                                                        Usuario Eliminado
                                                    @endif
                                                </div>
                                                <div>
                                                    @switch($usage->status)
                                                        @case('approved') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800">En Curso</span> @break
                                                        @case('completed') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800">Finalizado</span> @break
                                                        @case('pending') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-yellow-100 text-yellow-800">Pendiente</span> @break
                                                        @case('rejected') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-800">Rechazado</span> @break
                                                        @case('cancelled') <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-800">Cancelado</span> @break
                                                        @default <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-500 text-white">{{ ucfirst($usage->status) }}</span>
                                                    @endswitch
                                                </div>
                                            </div>

                                            {{-- Detalles --}}
                                            <div class="space-y-2 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Inicio:</span>
                                                    <span class="text-gray-200">{{ $usage->start_date ? $usage->start_date->format('d/m/Y H:i') : '-' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Término:</span>
                                                    <span class="text-gray-200">{{ $usage->end_date ? $usage->end_date->format('d/m/Y H:i') : '-' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Destino:</span>
                                                    <span class="text-gray-200">{{ ucfirst($usage->destination_type ?? 'Uso General') }}</span>
                                                </div>
                                                
                                                @if($usage->early_termination_reason)
                                                    <div class="pt-2 text-center">
                                                        <button @click="openTerminationModal('{{ addslashes($usage->early_termination_reason) }}', '{{ $usage->original_end_date ? $usage->original_end_date->format('d/m/Y') : '-' }}')"
                                                            class="w-full inline-flex justify-center items-center px-3 py-1.5 rounded bg-red-900/30 text-red-300 border border-red-800 hover:bg-red-900/50 transition-colors text-xs">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            Ver motivo término ant.
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                     <!-- Tabla de Devoluciones (Entregas) -->
                    <div x-show="activeTab === 'returns'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                        @php
                            $returns = $usageHistory->filter(fn($u) => $u->vehicleReturn)->map(fn($u) => $u); // Keep the Usage object wrapper to easier access relations
                        @endphp

                        @if($returns->isEmpty())
                            <div class="flex flex-col items-center justify-center py-12 text-gray-500 bg-gray-900 rounded-lg border border-dashed border-gray-700">
                                <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-lg font-medium">No hay registros de devoluciones/entregas.</p>
                                @if(request('has_damages') === 'yes') <p class="text-sm text-red-400 mt-2">No se encontraron entregas con daños reportados en este rango.</p> @endif
                            </div>
                        @else
                            <div class="bg-gray-900 rounded-lg shadow-xl border border-gray-700">
                                {{-- Vista Desktop --}}
                                <div x-show="width >= 768" class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-700">
                                        <thead class="bg-gray-800">
                                            <tr>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Fecha Devolución</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Usuario / Conductor</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Estado Entregado</th>
                                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Observaciones</th>
                                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700 bg-transparent">
                                            @foreach($returns as $usage)
                                                <tr id="return-row-{{ $usage->id }}" 
                                                    class="hover:bg-gray-800/50 transition duration-150 {{ request('highlight_id') == $usage->id ? ($usage->vehicleReturn->body_damage_reported ? 'bg-red-900/20 ring-2 ring-inset ring-red-500' : 'bg-blue-900/20 ring-2 ring-inset ring-blue-500') : '' }}">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                        {{ $usage->vehicleReturn->created_at->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                         <div class="text-sm font-medium text-white">
                                                            @if($usage->conductor)
                                                                {{ Str::of($usage->conductor->nombre)->explode(' ')->first() }} {{ Str::of($usage->conductor->nombre)->explode(' ')->last() }} (C)
                                                            @elseif($usage->user)
                                                                {{ Str::of($usage->user->name)->explode(' ')->first() }} {{ Str::of($usage->user->last_name)->explode(' ')->first() }}
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            Km: {{ number_format($usage->vehicleReturn->return_mileage, 0, '', '.') }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <div class="flex flex-col gap-1">
                                                             <span class="px-2 w-fit inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                {{ $usage->vehicleReturn->body_damage_reported ? 'bg-red-900/50 text-red-300 border border-red-700' : 'bg-green-900/50 text-green-300 border border-green-700' }}">
                                                                {{ $usage->vehicleReturn->body_damage_reported ? '⚠ Con Daños' : '✓ Sin Daños' }}
                                                            </span>
                                                             <span class="px-2 w-fit inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900/50 text-blue-300 border border-blue-700">
                                                                ⛽ {{ $usage->vehicleReturn->fuel_level }}%
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-300 max-w-md">
                                                         <div class="max-w-xs overflow-hidden text-ellipsis line-clamp-2" title="{{ $usage->vehicleReturn->comments }}">
                                                            {{ $usage->vehicleReturn->comments ?: 'Sin observaciones' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                        <button @click="openDetailModal({{ json_encode([
                                                                'id' => $usage->id,
                                                                'return_date' => $usage->vehicleReturn->created_at->format('Y-m-d H:i:s'),
                                                                'start_date' => $usage->start_date ? $usage->start_date->format('Y-m-d H:i:s') : null,
                                                                'end_date' => $usage->end_date ? $usage->end_date->format('Y-m-d H:i:s') : null,
                                                                'destination' => $usage->destination_type,
                                                                'user_name' => $usage->user ? (Str::of($usage->user->name)->explode(' ')->first() . ' ' . Str::of($usage->user->last_name)->explode(' ')->first()) : ($usage->conductor ? $usage->conductor->nombre : 'Desconocido'),
                                                                'user_role' => $usage->conductor ? 'Conductor' : 'Usuario',
                                                                'user_email' => $usage->user ? $usage->user->email : ($usage->conductor ? $usage->conductor->rut : ''),
                                                                'user_photo' => $usage->user && $usage->user->profile_photo_path ? Storage::url($usage->user->profile_photo_path) : ($usage->conductor && $usage->conductor->profile_photo_path ? Storage::url($usage->conductor->profile_photo_path) : null),
                                                                'completed_by_name' => $usage->completedBy ? $usage->completedBy->name : null,
                                                                'completed_by_email' => $usage->completedBy ? $usage->completedBy->email : null,
                                                                'completed_by_photo' => ($usage->completedBy && $usage->completedBy->profile_photo_path) ? Storage::url($usage->completedBy->profile_photo_path) : null,
                                                                'return_data' => $usage->vehicleReturn,
                                                                'fuel_loads' => $usage->fuelLoads,
                                                                'photos' => is_array($usage->vehicleReturn->photos_paths) ? collect($usage->vehicleReturn->photos_paths)->map(fn($p) => asset('storage/'.$p))->values()->toArray() : []
                                                            ]) }})"
                                                            class="inline-flex items-center px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-white rounded-md transition-colors border border-gray-600">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            Ver Detalle
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Vista Mobile (Tarjetas) --}}
                                <div x-show="width < 768" class="space-y-4 p-4">
                                    @foreach($returns as $usage)
                                        <div id="return-card-{{ $usage->id }}" 
                                             class="bg-gray-800 rounded-lg p-4 border border-gray-700 shadow-sm {{ request('highlight_id') == $usage->id ? ($usage->vehicleReturn->body_damage_reported ? 'bg-red-900/20 ring-2 ring-inset ring-red-500' : 'bg-blue-900/20 ring-2 ring-inset ring-blue-500') : '' }}">
                                            
                                            {{-- Header Tarjeta --}}
                                            <div class="flex justify-between items-start mb-3 border-b border-gray-700 pb-2">
                                                <div>
                                                    <div class="text-sm font-bold text-white">
                                                        {{ $usage->vehicleReturn->created_at->format('d/m/Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $usage->vehicleReturn->created_at->format('H:i') }} hrs
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-end gap-1">
                                                     <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-semibold rounded-full 
                                                        {{ $usage->vehicleReturn->body_damage_reported ? 'bg-red-900/50 text-red-300 border border-red-700' : 'bg-green-900/50 text-green-300 border border-green-700' }}">
                                                        {{ $usage->vehicleReturn->body_damage_reported ? 'Con Daños' : 'Sin Daños' }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Contenido --}}
                                            <div class="space-y-2 mb-4">
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-400">Usuario:</span>
                                                    <span class="text-gray-200 font-medium">
                                                        @if($usage->conductor)
                                                            {{ Str::of($usage->conductor->nombre)->explode(' ')->first() }} {{ Str::of($usage->conductor->nombre)->explode(' ')->last() }} (C)
                                                        @elseif($usage->user)
                                                            {{ Str::of($usage->user->name)->explode(' ')->first() }} {{ Str::of($usage->user->last_name)->explode(' ')->first() }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-400">Kilometraje:</span>
                                                    <span class="text-gray-200">{{ number_format($usage->vehicleReturn->return_mileage, 0, '', '.') }} km</span>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-400">Combustible:</span>
                                                    <span class="text-blue-300">⛽ {{ $usage->vehicleReturn->fuel_level }}%</span>
                                                </div>
                                                @if($usage->vehicleReturn->comments)
                                                    <div class="pt-2">
                                                        <span class="text-xs text-gray-500 block mb-1">Observaciones:</span>
                                                        <p class="text-xs text-gray-300 italic bg-gray-900/50 p-2 rounded border border-gray-700/50">
                                                            "{{ $usage->vehicleReturn->comments }}"
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Footer Acciones --}}
                                            <button @click="openDetailModal({{ json_encode([
                                                    'id' => $usage->id,
                                                    'return_date' => $usage->vehicleReturn->created_at->format('Y-m-d H:i:s'),
                                                    'start_date' => $usage->start_date ? $usage->start_date->format('Y-m-d H:i:s') : null,
                                                    'end_date' => $usage->end_date ? $usage->end_date->format('Y-m-d H:i:s') : null,
                                                    'destination' => $usage->destination_type,
                                                    'user_name' => $usage->user ? (Str::of($usage->user->name)->explode(' ')->first() . ' ' . Str::of($usage->user->last_name)->explode(' ')->first()) : ($usage->conductor ? $usage->conductor->nombre : 'Desconocido'),
                                                    'user_role' => $usage->conductor ? 'Conductor' : 'Usuario',
                                                    'user_email' => $usage->user ? $usage->user->email : ($usage->conductor ? $usage->conductor->rut : ''),
                                                    'user_photo' => $usage->user && $usage->user->profile_photo_path ? Storage::url($usage->user->profile_photo_path) : ($usage->conductor && $usage->conductor->profile_photo_path ? Storage::url($usage->conductor->profile_photo_path) : null),
                                                    'completed_by_name' => $usage->completedBy ? $usage->completedBy->name : null,
                                                    'completed_by_email' => $usage->completedBy ? $usage->completedBy->email : null,
                                                    'completed_by_photo' => ($usage->completedBy && $usage->completedBy->profile_photo_path) ? Storage::url($usage->completedBy->profile_photo_path) : null,
                                                    'return_data' => $usage->vehicleReturn,
                                                    'fuel_loads' => $usage->fuelLoads,
                                                    'photos' => is_array($usage->vehicleReturn->photos_paths) ? collect($usage->vehicleReturn->photos_paths)->map(fn($p) => asset('storage/'.$p))->values()->toArray() : []
                                                ]) }})"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition-colors border border-gray-600">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Ver Detalle
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- DETALLE MODAL -->
                    <div x-show="detailModalOpen"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-black/75 transition-opacity" @click="closeDetailModal()"></div>

                        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
                            <div x-show="detailModalOpen"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="relative bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full border border-gray-700">
                                
                                <!-- Header Modal -->
                                <div class="bg-gray-800 px-4 py-3 border-b border-gray-700 flex justify-between items-center">
                                    <h3 class="text-lg leading-6 font-medium text-white flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Detalle de Entrega / Devolución
                                    </h3>
                                    <button type="button" class="text-gray-400 hover:text-white focus:outline-none" @click="closeDetailModal()">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="px-4 py-5 sm:p-6" x-if="selectedReturn">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        
                                        <!-- Columna Izquierda: Información Usuario y Viaje -->
                                        <div class="space-y-6">
                                            <!-- Tarjeta Usuario -->
                                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Responsable</h4>
                                                <div class="flex items-center">
                                                    
                                                    <template x-if="selectedReturn.user_photo">
                                                        <img :src="selectedReturn.user_photo" class="h-12 w-12 rounded-full object-cover mr-4 border border-gray-600">
                                                    </template>
                                                    <template x-if="!selectedReturn.user_photo">
                                                        <div class="h-12 w-12 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-200 font-bold text-lg mr-4 border border-indigo-700/50">
                                                            <span x-text="selectedReturn.user_name ? selectedReturn.user_name.charAt(0) : '?'"></span>
                                                        </div>
                                                    </template>
                                                    <div>
                                                        <div class="text-white font-bold text-lg" x-text="selectedReturn.user_name"></div>
                                                        <div class="text-indigo-400 text-xs font-bold uppercase tracking-wide" x-text="selectedReturn.user_role"></div>
                                                        <div class="text-gray-400 text-xs mt-0.5" x-text="selectedReturn.user_email"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Información de Finalización (Separado) -->
                                            <!-- Información de Finalización (Separado) -->
                                            <template x-if="selectedReturn.completed_by_name">
                                                 <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 text-red-400">Terminado Por</h4>
                                                    <div class="flex items-center">
                                                        <template x-if="selectedReturn.completed_by_photo">
                                                            <img :src="selectedReturn.completed_by_photo" class="h-12 w-12 rounded-full object-cover mr-4 border border-gray-600">
                                                        </template>
                                                        <template x-if="!selectedReturn.completed_by_photo">
                                                            <div class="h-12 w-12 rounded-full bg-red-900/50 flex items-center justify-center text-red-200 font-bold text-lg mr-4 border border-red-700/50">
                                                                <span x-text="selectedReturn.completed_by_name.charAt(0)"></span>
                                                            </div>
                                                        </template>
                                                        <div>
                                                            <div class="text-white font-bold text-lg" x-text="selectedReturn.completed_by_name"></div>
                                                            <div class="text-gray-400 text-xs mt-0.5" x-text="selectedReturn.completed_by_email"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <!-- Espaciador si es necesario -->

                                            <!-- Tarjeta Viaje -->
                                            <div>
                                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Detalles del Uso</h4>
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div class="bg-gray-800 p-3 rounded border border-gray-700">
                                                        <span class="block text-gray-400 text-xs">Inicio</span>
                                                        <span class="text-white font-medium" x-text="formatDate(selectedReturn.start_date)"></span>
                                                    </div>
                                                    <div class="bg-gray-800 p-3 rounded border border-gray-700">
                                                        <span class="block text-gray-400 text-xs">Entrega</span>
                                                        <span class="text-white font-medium" x-text="formatDate(selectedReturn.return_date)"></span>
                                                    </div>
                                                    <div class="col-span-2 bg-gray-800 p-3 rounded border border-gray-700">
                                                        <span class="block text-gray-400 text-xs">Destino / Propósito</span>
                                                        <span class="text-white font-medium" x-text="selectedReturn.destination"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Cargas de Combustible -->
                                            <div>
                                                 <h4 class="text-xs font-bold text-gray-500 uppercase mb-2 flex items-center justify-between">
                                                    <span>Cargas de Combustible</span>
                                                    <span class="text-xs bg-gray-700 px-2 rounded-full text-white" x-text="selectedReturn.fuel_loads.length"></span>
                                                 </h4>
                                                 
                                                 <div x-show="selectedReturn.fuel_loads.length === 0" class="text-sm text-gray-500 italic p-3 bg-gray-800 rounded border border-gray-700">
                                                    No se registraron cargas de combustible durante este uso.
                                                 </div>

                                                 <div x-show="selectedReturn.fuel_loads.length > 0" class="overflow-hidden border border-gray-700 rounded-lg">
                                                    <table class="min-w-full divide-y divide-gray-700">
                                                        <thead class="bg-gray-800">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-400 uppercase">Fecha</th>
                                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-400 uppercase">Litros</th>
                                                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-400 uppercase">Monto</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-gray-900 divide-y divide-gray-700">
                                                            <template x-for="load in selectedReturn.fuel_loads" :key="load.id">
                                                                <tr class="text-xs text-gray-300">
                                                                    <td class="px-3 py-2" x-text="formatDate(load.date)"></td>
                                                                    <td class="px-3 py-2" x-text="load.liters + ' L'"></td>
                                                                    <td class="px-3 py-2 text-right" x-text="'$' + formatNumber(load.total_cost)"></td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                                 </div>
                                            </div>
                                        </div>

                                        <!-- Columna Derecha: Estado de Devolución y Fotos -->
                                        <div class="space-y-6">
                                            <!-- Estado General -->
                                            <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Estado de Recepción</h4>
                                                
                                                <div class="grid grid-cols-2 gap-4 mb-4">
                                                     <div class="text-center p-2 rounded bg-gray-900 border border-gray-700">
                                                        <div class="text-xs text-gray-400 uppercase">Kilometraje</div>
                                                        <div class="text-lg font-bold text-green-400" x-text="formatNumber(selectedReturn.return_data.return_mileage) + ' km'"></div>
                                                    </div>
                                                    <div class="text-center p-2 rounded bg-gray-900 border border-gray-700">
                                                        <div class="text-xs text-gray-400 uppercase">Combustible</div>
                                                        <div class="text-lg font-bold text-blue-400" x-text="selectedReturn.return_data.fuel_level + '%'"></div>
                                                    </div>
                                                </div>

                                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                                    <span class="text-sm text-gray-300">Limpieza Interior/Exterior</span>
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold"
                                                        :class="selectedReturn.return_data.cleanliness === 'clean' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                                                        x-text="selectedReturn.return_data.cleanliness === 'clean' ? 'IMPECABLE' : 'SUCIO/REGULAR'">
                                                    </span>
                                                </div>
                                                <div class="flex justify-between items-center py-2 border-b border-gray-700">
                                                    <span class="text-sm text-gray-300">Neumáticos Delanteros</span>
                                                    <span class="text-sm text-white" x-text="selectedReturn.return_data.tire_status_front === 'good' ? 'Bueno' : 'Revisar'"></span>
                                                </div>
                                                <div class="flex justify-between items-center py-2">
                                                    <span class="text-sm text-gray-300">Neumáticos Traseros</span>
                                                    <span class="text-sm text-white" x-text="selectedReturn.return_data.tire_status_rear === 'good' ? 'Bueno' : 'Revisar'"></span>
                                                </div>

                                                <div class="mt-4 p-3 bg-red-900/20 border border-red-900/50 rounded" x-show="selectedReturn.return_data.body_damage_reported">
                                                    <div class="flex items-start text-red-400">
                                                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        <div>
                                                            <strong class="block text-sm">Daños Reportados</strong>
                                                            <p class="text-xs mt-1 text-gray-300" x-text="selectedReturn.return_data.comments || 'Sin detalles adicionales.'"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                 <div class="mt-4 p-3 bg-gray-900 border border-gray-700 rounded" x-show="!selectedReturn.return_data.body_damage_reported">
                                                     <p class="text-xs text-gray-400">Comentarios: <span class="text-gray-300" x-text="selectedReturn.return_data.comments || 'Ninguno'"></span></p>
                                                 </div>
                                            </div>

                                            <!-- Galería de Fotos -->
                                            <div x-show="selectedReturn.photos.length > 0">
                                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Evidencia Fotográfica</h4>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <template x-for="(photo, index) in selectedReturn.photos">
                                                        <div class="relative aspect-square cursor-pointer group rounded-lg overflow-hidden border border-gray-600"
                                                             @click="openLightbox(photo, selectedReturn.photos, index)">
                                                            <img :src="photo" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                            <div x-show="selectedReturn.photos.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                                Sin fotos adjuntas.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="closeDetailModal()">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Término Anticipado -->
                    <div x-show="terminationModalOpen"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        
                        <div class="fixed inset-0 bg-black/75 transition-opacity" @click="closeTerminationModal()"></div>

                        <div class="flex items-center justify-center min-h-screen p-4 text-center">
                            <div class="bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-700 p-6">
                                <div class="text-center">
                                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-900/50 mb-4">
                                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg leading-6 font-medium text-white mb-2">Término Anticipado</h3>
                                    
                                    <div class="bg-gray-900 p-3 rounded border border-gray-700 text-left mb-4">
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Motivo</p>
                                        <p class="text-gray-300 text-sm italic" x-text="terminationReason"></p>
                                    </div>

                                    <div class="bg-gray-900 p-3 rounded border border-gray-700 text-left mb-6" x-show="terminationOriginalDate !== '-'">
                                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Fecha Original Prevista</p>
                                        <p class="text-white text-sm font-mono" x-text="terminationOriginalDate"></p>
                                    </div>

                                    <div class="flex justify-center">
                                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:text-sm" @click="closeTerminationModal()">
                                            Entendido
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lightbox Modal (IGUAL QUE ANTES) -->
                    <div x-show="lightboxOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click="closeLightbox()"
                        class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-90 p-4"
                        style="display: none;">

                        <!-- Close Button -->
                        <button @click="closeLightbox()"
                            class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Photo Counter -->
                        <div
                            class="absolute top-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-sm">
                            <span x-text="(currentIndex + 1) + ' / ' + allPhotos.length"></span>
                        </div>

                        <!-- Previous Button -->
                        <button @click.stop="prevPhoto()" x-show="lightboxOpen && currentIndex > 0"
                            class="absolute left-4 text-white hover:text-gray-300 transition-colors p-2 bg-black bg-opacity-50 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <!-- Image Container -->
                        <div @click.stop class="max-w-6xl max-h-full flex items-center justify-center">
                            <img :src="currentPhoto" alt="Foto ampliada"
                                class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100">
                        </div>

                        <!-- Next Button -->
                        <button @click.stop="nextPhoto()" x-show="lightboxOpen && currentIndex < allPhotos.length - 1"
                            class="absolute right-4 text-white hover:text-gray-300 transition-colors p-2 bg-black bg-opacity-50 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>

                        <!-- Keyboard Instructions -->
                        <div
                            class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-xs flex items-center gap-4">
                            <span class="flex items-center gap-1">
                                <kbd class="px-2 py-1 bg-gray-700 rounded">←</kbd>
                                <kbd class="px-2 py-1 bg-gray-700 rounded">→</kbd>
                                Navegar
                            </span>
                            <span class="flex items-center gap-1">
                                <kbd class="px-2 py-1 bg-gray-700 rounded">ESC</kbd>
                                Cerrar
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Auto-scroll script -->
    @if(request('highlight_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const row = document.getElementById('return-row-{{ request('highlight_id') }}');
                if (row) {
                    setTimeout(() => {
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 500); // Small delay to allow tabs/Vue to render
                }
            });
        </script>
    @endif
</x-app-layout>
