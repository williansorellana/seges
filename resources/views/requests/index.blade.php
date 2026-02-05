<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mis Reservas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100"
                    x-data="{ returnUrl: '', fuelRequestId: '', fuelVehicleId: '', fuelType: '' }">




                    <div x-init="
                        const highlightId = '{{ request('highlight_id') }}';
                        if (highlightId) {
                            const el = document.getElementById('request-' + highlightId);
                            if (el) {
                                setTimeout(() => {
                                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    el.classList.add('ring-2', 'ring-blue-500', 'dark:ring-blue-400');
                                    setTimeout(() => el.classList.remove('ring-2', 'ring-blue-500', 'dark:ring-blue-400'), 5000);
                                }, 500);
                            }
                        }
                    "></div>

                    @if($requests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Vehículo</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Desde</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Hasta</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Estado</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($requests as $request)
                                        <tr id="request-{{ $request->id }}" 
                                            class="{{ request('highlight_id') == $request->id ? 'bg-blue-50 dark:bg-blue-900/30' : '' }} transition-colors duration-1000">
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                @if($request->vehicle)
                                                    {{ $request->vehicle->brand }} {{ $request->vehicle->model }}
                                                    <span
                                                        class="text-xs text-gray-500 block">({{ $request->vehicle->plate }})</span>
                                                @else
                                                    <span class="text-red-500 italic">Vehículo Eliminado</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $request->start_date->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $request->end_date->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'approved' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                        'completed' => 'bg-gray-100 text-gray-800',
                                                    ];
                                                    $statusLabel = [
                                                        'pending' => 'Pendiente',
                                                        'approved' => 'Aprobado',
                                                        'rejected' => 'Rechazado',
                                                        'completed' => 'Finalizado',
                                                    ];
                                                @endphp
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$request->status] }}">
                                                    {{ $statusLabel[$request->status] }}
                                                </span>
                                                @if($request->status === 'rejected' && $request->rejection_reason)
                                                    <div class="mt-2 text-xs text-red-500 font-medium bg-red-50 dark:bg-red-900/20 p-1.5 rounded border border-red-200 dark:border-red-800">
                                                        <strong>Motivo:</strong> {{ $request->rejection_reason }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($request->status === 'approved')
                                                    <div class="flex flex-col space-y-2 text-right">
                                                        @if($request->vehicle)
                                                            <button
                                                                @click="fuelRequestId = '{{ $request->id }}'; fuelVehicleId = '{{ $request->vehicle_id }}'; fuelType = '{{ $request->vehicle->fuel_type }}'; $dispatch('open-modal', 'fuel-load-modal')"
                                                                class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition w-full justify-center">
                                                                <span class="mr-2">⛽</span> Cargar Combustible
                                                            </button>
                                                            <button
                                                                @click="returnUrl = '{{ route('requests.complete', $request->id) }}'; $dispatch('open-modal', 'confirm-return-modal')"
                                                                class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-25 transition w-full justify-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                                Devolver / Finalizar
                                                            </button>
                                                        @else
                                                            <span class="text-xs text-red-500 font-bold uppercase">Vehículo No
                                                                Disponible</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-gray-500 dark:text-gray-400">No tienes reservas registradas.</p>
                    @endif

                    <!-- Modal de Confirmación -->
                    <x-modal name="confirm-return-modal" :show="false" focusable>
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
                            <h3
                                class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 dark:border-gray-700">
                                Check-in de Devolución
                            </h3>
                            <p class="mb-4 text-gray-600 dark:text-gray-400 text-sm">per complete los detalles del
                                estado del vehículo para finalizar el viaje.</p>

                            <form :action="returnUrl" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <!-- Kilometraje -->
                                    <div class="col-span-2">
                                        <x-input-label for="mileage" :value="__('Kilometraje de Devolución')" />
                                        <x-text-input id="mileage" type="text" name="return_mileage" required
                                            class="block mt-1 w-full" placeholder="Ingrese kilometraje actual"
                                            x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" />
                                        <x-input-error :messages="$errors->get('return_mileage')" class="mt-2" />
                                    </div>

                                    <!-- Nivel de Combustible -->
                                    <div>
                                        <x-input-label for="fuel_level" :value="__('Nivel de Combustible')" />
                                        <select id="fuel_level" name="fuel_level" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="" disabled selected>Seleccione...</option>
                                            <option value="1/4">1/4 de Estanque</option>
                                            <option value="1/2">1/2 Estanque</option>
                                            <option value="3/4">3/4 Estanque</option>
                                            <option value="full">Estanque Lleno</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('fuel_level')" class="mt-2" />
                                    </div>

                                    <!-- Limpieza -->
                                    <div>
                                        <x-input-label for="cleanliness" :value="__('Limpieza')" />
                                        <select id="cleanliness" name="cleanliness" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="clean">🟢 Limpio</option>
                                            <option value="dirty">🟡 Sucio (Normal)</option>
                                            <option value="very_dirty">🔴 Muy Sucio</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('cleanliness')" class="mt-2" />
                                    </div>

                                    <!-- Neumáticos Delanteros -->
                                    <div>
                                        <x-input-label for="tire_front" :value="__('Neumáticos Delanteros')" />
                                        <select id="tire_front" name="tire_status_front" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="good">🟢 Bueno</option>
                                            <option value="fair">🟡 Regular</option>
                                            <option value="poor">🔴 Malo/Daño</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('tire_status_front')" class="mt-2" />
                                    </div>

                                    <!-- Neumáticos Traseros -->
                                    <div>
                                        <x-input-label for="tire_rear" :value="__('Neumáticos Traseros')" />
                                        <select id="tire_rear" name="tire_status_rear" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="good">🟢 Bueno</option>
                                            <option value="fair">🟡 Regular</option>
                                            <option value="poor">🔴 Malo/Daño</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('tire_status_rear')" class="mt-2" />
                                    </div>

                                    <!-- Daños Carrocería -->
                                    <div class="col-span-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="body_damage_reported" value="1"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span
                                                class="ml-2 text-gray-700 dark:text-gray-300">{{ __('Reportar nuevos daños en carrocería') }}</span>
                                        </label>
                                    </div>

                                    <!-- Comentarios -->
                                    <div class="col-span-2">
                                        <x-input-label for="comments" :value="__('Comentarios Adicionales')" />
                                        <textarea id="comments" name="comments" rows="2"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        <x-input-error :messages="$errors->get('comments')" class="mt-2" />
                                    </div>

                                    <!-- Fotos (Con Compresión Múltiple) -->
                                    <div class="col-span-2" x-data="{
                                        files: [],
                                        isProcessing: false,
                                        previews: [],
                                        async compressImage(file) {
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
                                                            resolve({ file: compressedFile, preview: event.target.result });
                                                        }, 'image/jpeg', 0.8);
                                                    };
                                                };
                                            });
                                        },
                                        async handleFiles(event) {
                                            this.isProcessing = true;
                                            const selectedFiles = Array.from(event.target.files);

                                            if (selectedFiles.length === 0) {
                                                this.isProcessing = false;
                                                return;
                                            }

                                            try {
                                                const results = await Promise.all(selectedFiles.map(file => this.compressImage(file)));
                                                
                                                // Agregar nuevos archivos a los existentes (Acumulativo)
                                                results.forEach(result => {
                                                    // Evitar duplicados por nombre si se desea, o permitir todo. Aquí permitimos todo.
                                                    this.files.push(result.file);
                                                    this.previews.push(result.preview);
                                                });
                                                
                                                this.updateInputFiles();
                                                
                                                // Limpiar el input para permitir seleccionar los mismos archivos de nuevo si se desea
                                                event.target.value = ''; 
                                            } catch (error) {
                                                console.error('Error comprimiendo imágenes', error);
                                            } finally {
                                                this.isProcessing = false;
                                            }
                                        },
                                        removeFile(index) {
                                            this.files.splice(index, 1);
                                            this.previews.splice(index, 1);
                                            this.updateInputFiles();
                                        },
                                        updateInputFiles() {
                                            const dataTransfer = new DataTransfer();
                                            this.files.forEach(file => {
                                                dataTransfer.items.add(file);
                                            });
                                            this.$refs.photosInput.files = dataTransfer.files;
                                        }
                                    }">
                                        <x-input-label for="photos" :value="__('Fotos (Opcional - Máx 5)')" />
                                        
                                        <!-- Previews Grid -->
                                        <div class="grid grid-cols-5 gap-2 mt-2 mb-2" x-show="previews.length > 0">
                                            <template x-for="(preview, index) in previews" :key="index">
                                                <div class="relative aspect-square group">
                                                    <img :src="preview" class="w-full h-full object-cover rounded-md border border-gray-300 dark:border-gray-600">
                                                    <!-- Botón Eliminar -->
                                                    <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-600/90 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-700 focus:opacity-100">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Processing Status -->
                                        <div x-show="isProcessing" class="flex items-center text-sm text-blue-500 mb-2">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Optimizando imágenes...
                                        </div>

                                        <input type="file" id="photos" name="photos[]" multiple accept="image/*"
                                            x-ref="photosInput"
                                            @change="handleFiles($event)"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300" />

                                        <div class="mt-2 text-xs text-gray-500" x-show="files.length > 0 && !isProcessing">
                                            <span x-text="files.length + ' imágenes listas para subir'"></span>
                                            <span class="text-green-500 font-bold ml-1">✓ Optimizadas</span>
                                        </div>
                                        <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                                        <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-4 border-t pt-4 dark:border-gray-700">
                                    <button type="button" @click="$dispatch('close')"
                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 font-bold">
                                        Confirmar Entrega
                                    </button>
                                </div>
                            </form>
                        </div>
                    </x-modal>

                    <!-- Modal Carga Combustible -->
                    <x-modal name="fuel-load-modal" :show="false" focusable>
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
                            <h3
                                class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 dark:border-gray-700">
                                Registrar Carga de Combustible ⛽
                            </h3>

                            <div class="mb-4 p-4 rounded-md border-2 text-center" :class="{
                                    'bg-yellow-900/30 border-yellow-500 text-yellow-500': fuelType === 'diesel',
                                    'bg-green-900/30 border-green-500 text-green-500': fuelType === 'gasoline',
                                    'bg-gray-700 border-gray-500 text-gray-300': !fuelType || (fuelType !== 'diesel' && fuelType !== 'gasoline')
                                }">
                                <span class="block text-xs uppercase tracking-widest font-bold text-gray-400">Tipo de
                                    Combustible Requerido</span>
                                <span class="text-2xl font-black uppercase"
                                    x-text="fuelType === 'diesel' ? 'PETRÓLEO (DIESEL)' : (fuelType === 'gasoline' ? 'BENCINA (GASOLINA)' : 'CONSULTAR MANUAL')"></span>
                                <span class="block text-xs mt-1 text-white font-bold" x-show="fuelType">⚠️ Verifique
                                    antes de
                                    cargar</span>
                            </div>

                            <form action="{{ route('fuel-loads.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="vehicle_request_id" :value="fuelRequestId">
                                <input type="hidden" name="vehicle_id" :value="fuelVehicleId">
                                <input type="hidden" name="date" value="{{ now()->format('Y-m-d H:i') }}"> {{-- Default
                                to now --}}

                                <div class="space-y-4">
                                    <!-- Fecha (Editable) -->
                                    <div>
                                        <x-input-label for="fuel_date" :value="__('Fecha y Hora')" />
                                        <x-text-input id="fuel_date" type="datetime-local" name="date" required
                                            class="block mt-1 w-full" value="{{ now()->format('Y-m-d\TH:i') }}" />
                                    </div>

                                    <!-- Kilometraje -->
                                    <div>
                                        <x-input-label for="fuel_mileage" :value="__('Kilometraje Actual (Odometer)')" />
                                        <x-text-input id="fuel_mileage" type="number" name="mileage" required
                                            class="block mt-1 w-full" placeholder="Ej: 45200" />
                                        <span class="text-xs text-gray-500">Debe ser mayor o igual al último
                                            registrado.</span>
                                    </div>

                                    <!-- Litros y Precio -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="fuel_liters" :value="__('Litros')" />
                                            <x-text-input id="fuel_liters" type="number" step="0.01" name="liters"
                                                required class="block mt-1 w-full" placeholder="Ej: 40.5" x-data="{}"
                                                @input="$refs.total.innerText = '$' + Math.round(($el.value || 0) * (document.getElementById('fuel_price').value || 0))" />
                                        </div>
                                        <div>
                                            <x-input-label for="fuel_price" :value="__('Precio por Litro')" />
                                            <x-text-input id="fuel_price" type="number" name="price_per_liter" required
                                                class="block mt-1 w-full" placeholder="Ej: 1350"
                                                @input="$refs.total.innerText = '$' + Math.round(($el.value || 0) * (document.getElementById('fuel_liters').value || 0))" />
                                        </div>
                                    </div>

                                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-center">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Costo Total
                                            Estimado:</span>
                                        <div class="text-2xl font-bold text-green-600" x-ref="total">$0</div>
                                    </div>

                                    <!-- Foto Boleta (Con Compresión) -->
                                    <div x-data="{
                                        preview: null,
                                        isProcessing: false,
                                        async compressImage(file) {
                                            this.isProcessing = true;
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
                                        <x-input-label for="fuel_photo" :value="__('Foto Boleta / Recibo')" />
                                        
                                        <!-- Preview -->
                                        <div class="mt-2 mb-2" x-show="preview" style="display: none;">
                                            <img :src="preview" class="max-h-40 rounded-md border border-gray-300 dark:border-gray-600">
                                            <div class="text-xs text-green-500 font-bold mt-1" x-show="!isProcessing && preview">✓ Imagen optimizada para carga rápida</div>
                                        </div>

                                        <!-- Processing State -->
                                        <div x-show="isProcessing" class="flex items-center text-sm text-blue-500 mb-2">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Optimizando imagen...
                                        </div>

                                        <input type="file" id="fuel_photo" name="receipt_photo" accept="image/*" x-ref="fuelPhotoInput"
                                            @change="
                                                const file = $event.target.files[0];
                                                if (file) {
                                                    // Preview inmediato
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { preview = e.target.result; };
                                                    reader.readAsDataURL(file);

                                                    // Comprimir
                                                    compressImage(file).then(compressedFile => {
                                                        const dataTransfer = new DataTransfer();
                                                        dataTransfer.items.add(compressedFile);
                                                        $refs.fuelPhotoInput.files = dataTransfer.files;
                                                        isProcessing = false;
                                                    });
                                                }
                                            "
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-gray-700 dark:file:text-gray-300" />
                                    </div>

                                    <!-- Nro Factura -->
                                    <div>
                                        <x-input-label for="invoice_number" :value="__('Nº Boleta/Factura (Opcional)')" />
                                        <x-text-input id="invoice_number" type="text" name="invoice_number"
                                            class="block mt-1 w-full" />
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-4 border-t pt-4 mt-6 dark:border-gray-700">
                                    <button type="button" @click="$dispatch('close')"
                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 font-bold">Registrar
                                        Carga</button>
                                </div>
                            </form>
                        </div>
                    </x-modal>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>