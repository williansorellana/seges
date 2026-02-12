<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100" x-data="{ 
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

                    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                        <div>
                            <div class="mb-2">
                                <a href="{{ route('assets.users-history-index') }}" 
                                   class="inline-flex items-center text-gray-400 hover:text-white transition ease-in-out duration-150">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Volver a Lista
                                </a>
                            </div>
                            <h2 class="text-xl font-bold text-blue-500 flex items-center">
                                Historial del Usuario
                            </h2>
                        </div>

                        <form method="GET" action="{{ url()->current() }}"
                            class="flex flex-wrap items-end gap-2 bg-gray-900 p-3 rounded-lg border border-gray-700">
                            <div>
                                <label for="start_date" class="block text-xs text-gray-400 mb-1">Desde</label>
                                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded p-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs text-gray-400 mb-1">Hasta</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                    class="bg-gray-800 border-gray-700 text-white text-sm rounded p-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="return_status" class="block text-xs text-gray-400 mb-1">Estado</label>
                                <select id="return_status" name="return_status" class="bg-gray-800 border-gray-700 text-white text-sm rounded p-2 focus:ring-blue-500 w-full md:w-auto">
                                    <option value="">Todos</option>
                                    <option value="pending" {{ request('return_status') == 'pending' ? 'selected' : '' }}>En Uso (Pendiente)</option>
                                    <option value="good" {{ request('return_status') == 'good' ? 'selected' : '' }}>Bueno</option>
                                    <option value="regular" {{ request('return_status') == 'regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="bad" {{ request('return_status') == 'bad' ? 'selected' : '' }}>Malo</option>
                                    <option value="damaged" {{ request('return_status') == 'damaged' ? 'selected' : '' }}>Dañado</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded h-[38px] flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                        </path>
                                    </svg>
                                    Filtrar
                                </button>

                                <a href="{{ route('users.asset-history.pdf', ['id' => $recipient->id, 'type' => (isset($recipient->nombre) ? 'worker' : 'user'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'return_status' => request('return_status')]) }}"
                                    target="_blank"
                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded h-[38px] flex items-center"
                                    title="Exportar a PDF">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="ml-1">PDF</span>
                                </a>

                                <a href="{{ url()->current() }}"
                                    class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded h-[38px] flex items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="bg-gray-900 rounded-lg p-4 mb-6 border border-gray-700 flex items-center gap-4">
                        <div class="flex-shrink-0">
                            @if(isset($recipient->profile_photo_path) && $recipient->profile_photo_path)
                                <img src="{{ Storage::url($recipient->profile_photo_path) }}" alt="Foto"
                                    class="w-16 h-16 object-cover rounded-full border-2 border-blue-500">
                            @else
                                <div class="w-16 h-16 bg-blue-900/50 rounded-full flex items-center justify-center border-2 border-blue-500 text-blue-200 font-bold text-2xl">
                                    {{ substr($recipient->name ?? $recipient->nombre, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Nombre</span>
                                <span class="text-white font-bold">{{ $recipient->name ?? $recipient->nombre }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">RUT</span>
                                <span class="font-mono text-blue-400 font-bold">{{ $recipient->rut ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Cargo</span>
                                <span class="text-white">{{ $recipient->cargo ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-xs uppercase">Departamento</span>
                                <span class="text-white font-bold">{{ $recipient->departamento ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-700 mb-4">
                        <h3 class="text-lg font-medium text-gray-200 pb-2">Activos Asignados Históricos</h3>
                    </div>

                    <div class="overflow-x-auto bg-gray-900 rounded-lg shadow-xl border border-gray-700">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        {{ __('Activo Asignado') }}
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
                                        {{ __('Asignado Por') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($assignments as $assignment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 mr-3">
                                                    @if($assignment->asset->foto_path)
                                                        <img class="h-10 w-10 rounded-full object-cover border border-gray-600" 
                                                             src="{{ Storage::url($assignment->asset->foto_path) }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-xs text-gray-400 border border-gray-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-white">
                                                        <a href="{{ route('assets.history', $assignment->asset->id) }}" class="hover:text-blue-400 hover:underline">
                                                            {{ $assignment->asset->nombre }}
                                                        </a>
                                                    </div>
                                                    <div class="text-sm text-yellow-500 font-mono">
                                                        {{ $assignment->asset->codigo_interno }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $assignment->fecha_entrega ? $assignment->fecha_entrega->format('d/m/Y') : '-' }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            @if(!$assignment->fecha_devolucion)
                                                <span class="text-green-400 font-bold flex items-center">
                                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                                    En Uso
                                                </span>
                                            @else
                                                {{ $assignment->fecha_devolucion->format('d/m/Y H:i') }}
                                            @endif
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
                                                        default => $assignment->estado_devolucion ?? 'N/A'
                                                    } }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-500">-</span>
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
                                                        <span class="font-semibold text-xs text-gray-400">Entrega:</span>
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
                                            <div class="text-sm text-gray-400">
                                                {{ $assignment->creator->name ?? 'Sistema' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="border-2 border-dashed border-gray-700 rounded-lg py-12">
                                                <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-400">Este usuario no tiene historial de activos asignados.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div x-show="lightboxOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" @click="closeLightbox()"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 p-4"
                        style="display: none;">

                        <button @click="closeLightbox()"
                            class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <div class="absolute top-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-sm">
                            <span x-text="(currentIndex + 1) + ' / ' + allPhotos.length"></span>
                        </div>

                        <button @click.stop="prevPhoto()" x-show="lightboxOpen && currentIndex > 0"
                            class="absolute left-4 text-white hover:text-gray-300 transition-colors p-2 bg-black bg-opacity-50 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <div @click.stop class="max-w-6xl max-h-full flex items-center justify-center">
                            <img :src="currentPhoto" alt="Foto ampliada"
                                class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100">
                        </div>

                        <button @click.stop="nextPhoto()" x-show="lightboxOpen && currentIndex < allPhotos.length - 1"
                            class="absolute right-4 text-white hover:text-gray-300 transition-colors p-2 bg-black bg-opacity-50 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>