<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100" x-data="{ 
                    activeTab: 'assignments',
                    lightboxOpen: false,
                    currentPhoto: '',
                    allPhotos: [],
                    currentIndex: 0,
                    
                    openLightbox(photoUrl, photos, index) {
                        this.currentPhoto = photoUrl;
                        this.allPhotos = photos;
                        this.currentIndex = index;
                        this.lightboxOpen = true;
                        document.body.style.overflow = 'hidden';
                    },
                    
                    closeLightbox() {
                        this.lightboxOpen = false;
                        document.body.style.overflow = 'auto';
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
                    }
                }" @keydown.escape.window="closeLightbox()" @keydown.arrow-right.window="lightboxOpen && nextPhoto()"
                    @keydown.arrow-left.window="lightboxOpen && prevPhoto()">

                    <!-- Header y Navegación -->
                    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <div class="mb-2">
                                <a href="{{ route('assets.index') }}"
                                    class="inline-flex items-center text-gray-400 hover:text-white transition ease-in-out duration-150">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Volver
                                </a>
                            </div>
                            <h2 class="text-xl font-bold text-yellow-500 flex items-center">
                                Historial del Activo
                            </h2>
                        </div>


                        <!-- Formulario de Filtros -->
                        <form method="GET" action="{{ route('assets.history', $asset->id) }}"
                            class="flex flex-wrap items-end gap-2 bg-gray-900 p-3 rounded-lg border border-gray-700">
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
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded transition-colors flex items-center h-[38px] mt-auto">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                        </path>
                                    </svg>
                                    Filtrar
                                </button>

                                <a :href="activeTab === 'assignments' ? '{{ route('assets.history.pdf', ['id' => $asset->id, 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'type' => 'assignments']) }}' : '{{ route('assets.history.pdf', ['id' => $asset->id, 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'type' => 'maintenances']) }}'"
                                    target="_blank"
                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition-colors flex items-center h-[38px] mt-auto"
                                    title="Exportar a PDF">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    PDF
                                </a>

                                <a href="{{ route('assets.history', $asset->id) }}"
                                    class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium rounded transition-colors flex items-center h-[38px] mt-auto"
                                    title="Limpiar Filtros">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Detalles del Activo -->
                    <div class="bg-gray-900 rounded-lg p-4 mb-6 border border-gray-700 flex items-center gap-4">
                        @if($asset->foto_path)
                            <img src="{{ Storage::url($asset->foto_path) }}" alt="Foto"
                                class="w-16 h-16 object-cover rounded-full border-2 border-gray-600">
                        @else
                            <div
                                class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center border-2 border-gray-600">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Nombre</span>
                                <span class="text-white font-bold">{{ $asset->nombre }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Código</span>
                                <span class="font-mono text-yellow-400 font-bold">{{ $asset->codigo_interno }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Estado</span>
                                <span class="text-white">
                                    {{ match($asset->estado) {
                                        'available' => 'Disponible',
                                        'assigned' => 'Asignado',
                                        'maintenance' => 'Mantenimiento',
                                        'written_off' => 'Dado de Baja',
                                        default => ucfirst($asset->estado)
                                    } }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Ubicación</span>
                                <span
                                    class="text-green-400 font-bold">{{ $asset->ubicacion ?? 'No especificada' }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-700 mb-6">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="activeTab = 'assignments'"
                                    :class="activeTab === 'assignments' ? 'border-blue-500 text-blue-500' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-150">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    {{ __('Asignaciones') }}
                                </button>

                                <button @click="activeTab = 'maintenances'"
                                    :class="activeTab === 'maintenances' ? 'border-yellow-500 text-yellow-500' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-150">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ __('Mantenciones') }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tabla Asignaciones -->
                        <div x-show="activeTab === 'assignments'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="overflow-x-auto bg-gray-900 rounded-lg shadow-xl border border-gray-700">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead class="bg-gray-800">
                                    <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            {{ __('Asignado A') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            {{ __('Fecha Entrega') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            {{ __('Fecha Devolución') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            {{ __('Estado Devolución') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            {{ __('Comentarios / Incidentes') }}
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            {{ __('Responsable') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($assignments as $assignment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    @if($assignment->user)
                                                        {{ $assignment->user->name }} (Usuario)
                                                    @elseif($assignment->worker)
                                                        {{ $assignment->worker->nombre }} (Externo)
                                                    @else
                                                        {{ $assignment->trabajador_nombre ?? 'N/A' }}
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $assignment->user ? $assignment->user->rut : ($assignment->worker ? $assignment->worker->rut : $assignment->trabajador_rut) }}
                                                </div>
                                            </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ $assignment->fecha_entrega ? $assignment->fecha_entrega->format('d/m/Y') : '-' }}
                                            </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ $assignment->fecha_devolucion ? $assignment->fecha_devolucion->format('d/m/Y H:i') : 'En curso' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($assignment->fecha_devolucion)
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if($assignment->estado_devolucion == 'good') bg-green-900/50 text-green-300 border border-green-700 
                                                        @elseif($assignment->estado_devolucion == 'regular') bg-yellow-900/50 text-yellow-300 border border-yellow-700 
                                                        @elseif($assignment->estado_devolucion == 'bad') bg-orange-900/50 text-orange-300 border border-orange-700 
                                                        @elseif($assignment->estado_devolucion == 'damaged') bg-red-900/50 text-red-300 border border-red-700 
                                                        @else bg-gray-900/50 text-gray-300 border border-gray-700 @endif">
                                                        {{ match ($assignment->estado_devolucion) {
                                                        'good' => 'Bueno',
                                                        'regular' => 'Regular',
                                                        'bad' => 'Malo',
                                                        'damaged' => 'Dañado',
                                                        default => $assignment->estado_devolucion ?? 'Desconocido'
                                                    } }}
                                                                                        </span>
                                                @else
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900/50 text-blue-300 border border-blue-700">
                                                        Activo
                                                    </span>
                                                @endif
                                            </td>
                                                <td class="px-6 py-4 text-sm text-gray-300">
                                                <div class="max-w-xs overflow-hidden text-ellipsis">
                                                    @if($assignment->comentarios_devolucion)
                                                        <div class="font-semibold text-xs text-gray-400">Devolución:</div>
                                                        {{ $assignment->comentarios_devolucion }}
                                                    @endif
                                                    @if($assignment->observaciones)
                                                        <div class="mt-1 border-t border-gray-700 pt-1">
                                                            <span class="font-semibold text-xs text-gray-400">Inicio:</span>
                                                            {{ $assignment->observaciones }}
                                                        </div>
                                                    @endif

                                                    {{-- Fotos de Devolución --}}
                                                    @if($assignment->photos && $assignment->photos->count() > 0)
                                                        <div class="mt-2"
                                                            x-data="{ photosData: {{ json_encode($assignment->photos->pluck('url')->values()->toArray()) }} }">
                                                            <button type="button"
                                                                @click="openLightbox(photosData[0], photosData, 0)"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                                <span>Ver fotos ({{ $assignment->photos->count() }})</span>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $assignment->creator->name ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $assignment->creator->rut ?? '' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center">
                                                <div class="border-2 border-dashed border-gray-700 rounded-lg py-12">
                                                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                    </svg>
                                                    <p class="mt-2 text-sm text-gray-400">No hay historial de asignaciones para este activo.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        </div>

                        <!-- Tabla Mantenciones -->
                        <div x-show="activeTab === 'maintenances'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                            style="display: none;">
                            <div class="overflow-x-auto bg-gray-900 rounded-lg shadow-xl border border-gray-700">
                                <table class="min-w-full divide-y divide-gray-700">
                                    <thead class="bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                {{ __('Tipo') }}
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                {{ __('Fecha Inicio') }}
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                {{ __('Fecha Término') }}
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                {{ __('Detalles del Problema') }}
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                {{ __('Solución / Resultado') }}
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                {{ __('Costo') }}
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                {{ __('Responsable') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700 bg-transparent">
                                    @forelse($maintenances as $maintenance)
                                        <tr class="hover:bg-gray-800/50 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $maintenance->tipo === 'preventiva' ? 'bg-blue-900/50 text-blue-300 border border-blue-700' : 'bg-red-900/50 text-red-300 border border-red-700' }}">
                                                    {{ ucfirst($maintenance->tipo) }}
                                                </span>
                                            </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                {{ $maintenance->fecha ? $maintenance->fecha->format('d/m/Y') : '-' }}
                                            </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                @if($maintenance->fecha_termino)
                                                    {{ $maintenance->fecha_termino->format('d/m/Y') }}
                                                @else
                                                    <span class="text-yellow-500 flex items-center">
                                                        <svg class="w-4 h-4 mr-1 animate-pulse" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        En Proceso
                                                    </span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                                                {{ $maintenance->descripcion }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                                                @if($maintenance->detalles_solucion)
                                                    <span class="text-green-400 block mb-1 font-medium">Resuelto:</span>
                                                    {{ $maintenance->detalles_solucion }}

                                                    {{-- Botón de Fotos --}}
                                                    @if($maintenance->photos && $maintenance->photos->count() > 0)
                                                        <div class="mt-2"
                                                            x-data="{ photosData: {{ json_encode($maintenance->photos->pluck('url')->values()->toArray()) }} }">
                                                            <button type="button"
                                                                @click="openLightbox(photosData[0], photosData, 0)"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                                <span>Ver fotos ({{ $maintenance->photos->count() }})</span>
                                                            </button>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-500 italic">Sin detalles aún</span>
                                                @endif
                                            </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                                @if($maintenance->costo)
                                                    ${{ number_format($maintenance->costo, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $maintenance->creator->name ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $maintenance->creator->rut ?? '' }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center">
                                                <div class="border-2 border-dashed border-gray-700 rounded-lg py-12">
                                                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <p class="mt-2 text-sm text-gray-400">No hay historial de mantenciones para este activo.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Lightbox Modal -->
                    <div x-show="lightboxOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click="closeLightbox()"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4"
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
</x-app-layout>