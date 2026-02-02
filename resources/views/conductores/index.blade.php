<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Gestión de Conductores') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('conductores.trash') }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    {{ __('Papelera') }}
                </a>
                <button @click="$dispatch('open-create-modal')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    + Nuevo Conductor
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12"
        x-data="{ openDeleteModal: false, openViewModal: false, openEditModal: false, openCreateModal: false, deleteAction: '', viewingConductor: {}, editingConductor: {} }"
        @open-create-modal.window="openCreateModal = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Cargo / Depto</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Estado Licencia
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($conductores as $conductor)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="h-10 w-10 rounded-full overflow-hidden border border-gray-500">
                                        @if($conductor->fotografia)
                                            <img src="{{ asset('storage/' . $conductor->fotografia) }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div
                                                class="h-full w-full bg-gray-700 flex items-center justify-center text-[10px] text-gray-400 font-bold">
                                                N/A</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-100">{{ $conductor->nombre }}</td>
                                <td class="px-6 py-4 text-sm text-gray-300">{{ $conductor->cargo }} /
                                    {{ $conductor->departamento }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php $vencida = $conductor->fecha_licencia->isPast(); @endphp
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-lg {{ $vencida ? 'bg-red-900/30 text-red-400' : 'bg-emerald-900/30 text-emerald-400' }}">
                                        {{ $vencida ? 'VENCIDA' : 'VENCE: ' . $conductor->fecha_licencia->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        <button
                                            @click="viewingConductor = { 
                                                                                                                        nombre: '{{ $conductor->nombre }}',
                                                                                                                        rut: '{{ $conductor->rut ?? '' }}',
                                                                                                                        cargo: '{{ $conductor->cargo }}', 
                                                                                                                        depto: '{{ $conductor->departamento }}', 
                                                                                                                        vencimiento: '{{ $conductor->fecha_licencia->format('d/m/Y') }}', 
                                                                                                                        foto: '{{ $conductor->fotografia ? asset('storage/' . $conductor->fotografia) : '' }}',
                                                                                                                        is_expired: {{ $conductor->fecha_licencia->isPast() ? 'true' : 'false' }}
                                                                                                                    }; openViewModal = true"
                                            class="text-green-400 hover:text-green-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="editingConductor = {
                                                                                                            id: {{ $conductor->id }},
                                                                                                            nombre: '{{ $conductor->nombre }}',
                                                                                                            rut: '{{ $conductor->rut ?? '' }}',
                                                                                                            cargo: '{{ $conductor->cargo }}',
                                                                                                            depto: '{{ $conductor->departamento }}',
                                                                                                            vencimiento: '{{ $conductor->fecha_licencia->format('Y-m-d') }}',
                                                                                                            foto: '{{ $conductor->fotografia ? asset('storage/' . $conductor->fotografia) : '' }}',
                                                                                                            has_foto: {{ $conductor->fotografia ? 'true' : 'false' }}
                                                                                                        }; openEditModal = true"
                                            class="text-blue-400 hover:text-blue-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteAction = '{{ route('conductores.destroy', $conductor) }}'; openDeleteModal = true"
                                            class="text-red-400 hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-500">No hay conductores registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- View Modal -->
        <div x-show="openViewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openViewModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/75 transition-opacity"
                    @click="openViewModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openViewModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-gray-700">

                    <div class="p-6 text-gray-100">
                        <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-2">
                            <h2 class="text-xl font-bold text-gray-100">
                                Detalle del Conductor
                            </h2>
                            <button @click="openViewModal = false" class="text-gray-400 hover:text-gray-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Foto Grande -->
                            <div
                                class="flex flex-col items-center justify-center bg-gray-900 rounded-lg p-4 border border-gray-700 min-h-[250px]">
                                <template x-if="viewingConductor.foto">
                                    <img :src="viewingConductor.foto"
                                        class="w-full h-64 object-cover rounded-md shadow-lg border border-gray-600">
                                </template>
                                <template x-if="!viewingConductor.foto">
                                    <div
                                        class="w-full h-64 flex flex-col items-center justify-center bg-gray-800 text-gray-500 rounded-md border border-gray-700 border-dashed">
                                        <svg class="h-12 w-12 mb-2 text-gray-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-sm">Sin fotografía</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Datos -->
                            <div class="space-y-6">
                                <div>
                                    <span class="block text-xs text-gray-400 uppercase tracking-widest mb-1">Nombre
                                        Completo</span>
                                    <span class="text-2xl font-bold text-white tracking-wider leading-tight"
                                        x-text="viewingConductor.nombre"></span>
                                </div>

                                <div x-show="viewingConductor.rut">
                                    <span class="block text-xs text-gray-400 uppercase tracking-widest mb-1">RUT</span>
                                    <span class="text-lg font-mono text-gray-200" x-text="viewingConductor.rut"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span
                                            class="block text-xs text-gray-400 uppercase tracking-widest mb-1">Cargo</span>
                                        <span class="text-base text-gray-200 font-medium"
                                            x-text="viewingConductor.cargo"></span>
                                    </div>
                                    <div>
                                        <span
                                            class="block text-xs text-gray-400 uppercase tracking-widest mb-1">Departamento</span>
                                        <span class="text-base text-gray-200 font-medium"
                                            x-text="viewingConductor.depto"></span>
                                    </div>
                                </div>

                                <div class="bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                                    <span class="block text-xs text-gray-400 uppercase tracking-widest mb-2">Estado
                                        Licencia</span>
                                    <div class="flex items-center">
                                        <span class="text-lg font-mono font-bold"
                                            :class="viewingConductor.is_expired ? 'text-red-400' : 'text-green-400'"
                                            x-text="viewingConductor.vencimiento"></span>

                                        <span x-show="viewingConductor.is_expired"
                                            class="ml-2 px-2 py-0.5 text-xs font-bold bg-red-900/50 text-red-200 rounded border border-red-800">
                                            VENCIDA
                                        </span>
                                        <span x-show="!viewingConductor.is_expired"
                                            class="ml-2 px-2 py-0.5 text-xs font-bold bg-green-900/50 text-green-200 rounded border border-green-800">
                                            VIGENTE
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end pt-4 border-t border-gray-700">
                            <button type="button"
                                class="px-5 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-md border border-gray-600 transition-colors shadow-sm font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                @click="openViewModal = false">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="openCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/75 transition-opacity"
                    @click="openCreateModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openCreateModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-700">

                    <form action="{{ route('conductores.store') }}" method="POST" enctype="multipart/form-data" x-data="{
                            photoPreview: null,
                            isCompressing: false,
                            async compressImage(file) {
                                this.isCompressing = true;
                                return new Promise((resolve) => {
                                    const reader = new FileReader();
                                    reader.readAsDataURL(file);
                                    reader.onload = (event) => {
                                        const img = new Image();
                                        img.src = event.target.result;
                                        img.onload = () => {
                                            const canvas = document.createElement('canvas');
                                            const ctx = canvas.getContext('2d');
                                            const MAX_WIDTH = 1920;
                                            let width = img.width;
                                            let height = img.height;

                                            if (width > MAX_WIDTH) {
                                                height *= MAX_WIDTH / width;
                                                width = MAX_WIDTH;
                                            }

                                            canvas.width = width;
                                            canvas.height = height;
                                            ctx.drawImage(img, 0, 0, width, height);

                                            canvas.toBlob((blob) => {
                                                const compressedFile = new File([blob], file.name, {
                                                    type: 'image/jpeg',
                                                    lastModified: Date.now(),
                                                });
                                                resolve(compressedFile);
                                            }, 'image/jpeg', 0.8);
                                        };
                                    };
                                });
                            }
                        }">
                        @csrf

                        <div class="p-6 text-gray-100">
                            <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-3">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-100">Registrar Nuevo Conductor</h2>
                                    <p class="text-sm text-gray-400 mt-1">Complete los datos para dar de alta a un nuevo
                                        conductor</p>
                                </div>
                                <button type="button" @click="openCreateModal = false"
                                    class="text-gray-400 hover:text-gray-200">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <label for="create-nombre"
                                        class="block text-sm font-semibold text-white mb-2">Nombre Completo</label>
                                    <input id="create-nombre" name="nombre" type="text" placeholder="Ej: Juan Pérez"
                                        required
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- RUT -->
                                <div>
                                    <label for="create-rut" class="block text-sm font-semibold text-white mb-2">RUT
                                        (Opcional)</label>
                                    <input id="create-rut" name="rut" type="text" placeholder="Ej: 12.345.678-9"
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- Cargo -->
                                <div>
                                    <label for="create-cargo"
                                        class="block text-sm font-semibold text-white mb-2">Cargo</label>
                                    <input id="create-cargo" name="cargo" type="text"
                                        placeholder="Ej: Chofer de Reparto" required
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- Departamento -->
                                <div>
                                    <label for="create-departamento"
                                        class="block text-sm font-semibold text-white mb-2">Departamento</label>
                                    <input id="create-departamento" name="departamento" type="text"
                                        placeholder="Ej: Logística" required
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- Fecha Licencia -->
                                <div>
                                    <label for="create-fecha_licencia"
                                        class="block text-sm font-semibold text-white mb-2">Vencimiento Licencia</label>
                                    <input id="create-fecha_licencia" name="fecha_licencia" type="date" required
                                        class="w-full outline-none text-gray-400 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm [&::-webkit-calendar-picker-indicator]:invert" />
                                </div>

                                <!-- Fotografía -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-white mb-2">Fotografía
                                        (Opcional)</label>

                                    <!-- Preview -->
                                    <div class="mb-3" x-show="photoPreview" style="display: none;">
                                        <span
                                            class="block rounded-md w-full h-40 bg-cover bg-no-repeat bg-center mx-auto border border-gray-600"
                                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button type="button" x-on:click.prevent="$refs.photo.click()"
                                            :disabled="isCompressing"
                                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg border border-gray-600 transition-colors shadow-sm text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span x-show="!isCompressing">Seleccionar Imagen</span>
                                            <span x-show="isCompressing">Procesando...</span>
                                        </button>
                                        <span x-show="!photoPreview" class="text-xs text-gray-500">Ningún archivo
                                            seleccionado</span>
                                    </div>

                                    <input id="create-fotografia" name="fotografia" type="file" accept="image/*"
                                        class="hidden" x-ref="photo" x-on:change="
                                            const file = $refs.photo.files[0];
                                            if (file) {
                                                const reader = new FileReader();
                                                reader.onload = (e) => { photoPreview = e.target.result; };
                                                reader.readAsDataURL(file);
                                                
                                                compressImage(file).then(compressedFile => {
                                                    this.isCompressing = false;
                                                    const dataTransfer = new DataTransfer();
                                                    dataTransfer.items.add(compressedFile);
                                                    $refs.photo.files = dataTransfer.files;
                                                });
                                            }
                                        " />
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-700">
                                <button type="button" @click="openCreateModal = false"
                                    class="px-5 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-md border border-gray-600 transition-colors shadow-sm font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors shadow-md font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Guardar Conductor
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="openEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/75 transition-opacity"
                    @click="openEditModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-gray-800 rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-700">

                    <form :action="'/conductores/' + editingConductor.id" method="POST" enctype="multipart/form-data"
                        x-data="{
                            photoPreview: null,
                            isCompressing: false,
                            async compressImage(file) {
                                this.isCompressing = true;
                                return new Promise((resolve) => {
                                    const reader = new FileReader();
                                    reader.readAsDataURL(file);
                                    reader.onload = (event) => {
                                        const img = new Image();
                                        img.src = event.target.result;
                                        img.onload = () => {
                                            const canvas = document.createElement('canvas');
                                            const ctx = canvas.getContext('2d');
                                            const MAX_WIDTH = 1920;
                                            let width = img.width;
                                            let height = img.height;

                                            if (width > MAX_WIDTH) {
                                                height *= MAX_WIDTH / width;
                                                width = MAX_WIDTH;
                                            }

                                            canvas.width = width;
                                            canvas.height = height;
                                            ctx.drawImage(img, 0, 0, width, height);

                                            canvas.toBlob((blob) => {
                                                const compressedFile = new File([blob], file.name, {
                                                    type: 'image/jpeg',
                                                    lastModified: Date.now(),
                                                });
                                                resolve(compressedFile);
                                            }, 'image/jpeg', 0.8);
                                        };
                                    };
                                });
                            }
                        }">
                        @csrf
                        @method('PUT')

                        <div class="p-6 text-gray-100">
                            <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-3">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-100">Editar Conductor</h2>
                                    <p class="text-sm text-gray-400 mt-1">Modifique los campos necesarios para
                                        actualizar el perfil</p>
                                </div>
                                <button type="button" @click="openEditModal = false"
                                    class="text-gray-400 hover:text-gray-200">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nombre -->
                                <div>
                                    <label for="edit-nombre" class="block text-sm font-semibold text-white mb-2">Nombre
                                        Completo</label>
                                    <input id="edit-nombre" name="nombre" type="text" :value="editingConductor.nombre"
                                        required
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- RUT -->
                                <div>
                                    <label for="edit-rut" class="block text-sm font-semibold text-white mb-2">RUT
                                        (Opcional)</label>
                                    <input id="edit-rut" name="rut" type="text" :value="editingConductor.rut"
                                        placeholder="Ej: 12.345.678-9"
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- Cargo -->
                                <div>
                                    <label for="edit-cargo"
                                        class="block text-sm font-semibold text-white mb-2">Cargo</label>
                                    <input id="edit-cargo" name="cargo" type="text" :value="editingConductor.cargo"
                                        required
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- Departamento -->
                                <div>
                                    <label for="edit-departamento"
                                        class="block text-sm font-semibold text-white mb-2">Departamento</label>
                                    <input id="edit-departamento" name="departamento" type="text"
                                        :value="editingConductor.depto" required
                                        class="w-full outline-none text-white placeholder:text-gray-600 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" />
                                </div>

                                <!-- Fecha Licencia -->
                                <div>
                                    <label for="edit-fecha_licencia"
                                        class="block text-sm font-semibold text-white mb-2">Vencimiento Licencia</label>
                                    <input id="edit-fecha_licencia" name="fecha_licencia" type="date"
                                        :value="editingConductor.vencimiento" required
                                        class="w-full outline-none text-gray-400 border border-gray-600 bg-gray-900 transition-all text-sm py-2.5 px-3 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm [&::-webkit-calendar-picker-indicator]:invert" />
                                </div>

                                <!-- Fotografía -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-white mb-2">Cambiar Fotografía
                                        (Opcional)</label>

                                    <!-- Preview Box -->
                                    <div class="mb-3" x-show="photoPreview || editingConductor.has_foto"
                                        style="display: none;">
                                        <span
                                            class="block rounded-md w-full h-40 bg-cover bg-no-repeat bg-center mx-auto border border-gray-600"
                                            x-bind:style="'background-image: url(\'' + (photoPreview || editingConductor.foto) + '\');'">
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button type="button" x-on:click.prevent="$refs.photoEdit.click()"
                                            :disabled="isCompressing"
                                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg border border-gray-600 transition-colors shadow-sm text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span x-show="!isCompressing">Seleccionar Imagen</span>
                                            <span x-show="isCompressing">Procesando...</span>
                                        </button>

                                        <div x-show="!photoPreview && !editingConductor.has_foto"
                                            class="text-xs text-gray-500">Ningún archivo seleccionado</div>
                                        <div x-show="editingConductor.has_foto && !photoPreview"
                                            class="text-xs text-gray-400 italic">Se mantendrá la foto actual si no
                                            seleccionas otra</div>
                                    </div>

                                    <input id="edit-fotografia" name="fotografia" type="file" accept="image/*"
                                        class="hidden" x-ref="photoEdit" x-on:change="
                                            const file = $refs.photoEdit.files[0];
                                            if (file) {
                                                const reader = new FileReader();
                                                reader.onload = (e) => { photoPreview = e.target.result; };
                                                reader.readAsDataURL(file);

                                                compressImage(file).then(compressedFile => {
                                                    this.isCompressing = false;
                                                    const dataTransfer = new DataTransfer();
                                                    dataTransfer.items.add(compressedFile);
                                                    $refs.photoEdit.files = dataTransfer.files;
                                                });
                                            }
                                         " />
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-700">
                                <button type="button" @click="openEditModal = false"
                                    class="px-5 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-md border border-gray-600 transition-colors shadow-sm font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition-colors shadow-md font-semibold text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Actualizar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="openDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="openDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/75 transition-opacity"
                    @click="openDeleteModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="openDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-700">

                    <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-100" id="modal-title">
                                    Eliminar Conductor
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-400">
                                        ¿Estás seguro de que deseas enviar a la papelera a este conductor? Podrás
                                        restaurarlo después si es necesario.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form :action="deleteAction" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Eliminar
                            </button>
                        </form>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-gray-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="openDeleteModal = false">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>