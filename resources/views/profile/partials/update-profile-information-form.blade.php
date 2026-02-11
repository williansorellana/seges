<section x-data="{ 
    showImageModal: false, 
    modalImageUrl: '', 
    openModal(url) { 
        if(url) {
            this.modalImageUrl = url; 
            this.showImageModal = true; 
        }
    } 
}" @view-image.window="openModal($event.detail)">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Información del Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Actualice la información de su perfil y su dirección de correo electrónico.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div>
            <x-input-label for="photo" :value="__('Foto de Perfil')" />
            <div x-data="{ 
                photoName: null, 
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
                                const MAX_WIDTH = 1024; // Smaller max width for profile photos
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
                                    this.isCompressing = false;
                                    resolve(compressedFile);
                                }, 'image/jpeg', 0.8);
                            };
                        };
                    });
                }
            }" class="col-span-6 sm:col-span-4">
                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    @if ($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}"
                            @click="$dispatch('view-image', '{{ asset('storage/' . $user->profile_photo_path) }}')"
                            class="rounded-full h-20 w-20 object-cover cursor-pointer hover:opacity-75 transition">
                    @else
                        <div
                            class="rounded-full h-20 w-20 bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <div class="flex items-center mt-2">
                    <x-secondary-button type="button" x-on:click.prevent="$refs.photo.click()" x-bind:disabled="isCompressing">
                        <span x-show="!isCompressing">{{ __('Seleccionar Nueva Foto') }}</span>
                        <span x-show="isCompressing">{{ __('Procesando...') }}</span>
                    </x-secondary-button>
                    
                    <span x-show="isCompressing" class="ml-2 text-sm text-gray-500">Optimizando imagen...</span>
                </div>

                <input type="file" id="photo" class="hidden" x-ref="photo" name="photo" accept="image/*"
                    x-on:change="
                                    const file = $refs.photo.files[0];
                                    if (file) {
                                        photoName = file.name;
                                        // Preview inmediato (opcional, o esperar al comprimido)
                                        // const reader = new FileReader();
                                        // reader.onload = (e) => { photoPreview = e.target.result; };
                                        // reader.readAsDataURL(file);

                                        compressImage(file).then(compressedFile => {
                                            const dataTransfer = new DataTransfer();
                                            dataTransfer.items.add(compressedFile);
                                            $refs.photo.files = dataTransfer.files;
                                            
                                            // Actualizar preview con la imagen comprimida
                                            const reader = new FileReader();
                                            reader.onload = (e) => { photoPreview = e.target.result; };
                                            reader.readAsDataURL(compressedFile);
                                        });
                                    }
                            " />

                <x-input-error class="mt-2" :messages="$errors->get('photo')" />
            </div>
        </div>

        <!-- License Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Licencia de Conducir') }}</h3>

            <script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>

            <div x-data="{
                licensePreview: null,
                isScanning: false,
                scanMessage: '',
                scanProgress: 0,
                showErrorModal: false,
                errorMessage: '',

                processLicense(folder) {
                    const file = folder.files[0];
                    if (!file) return;

                    // 1. Mostrar preview temporal inmediatamente
                    const reader = new FileReader();
                    reader.onload = (e) => { this.licensePreview = e.target.result; };
                    reader.readAsDataURL(file);

                    // 2. Comprimir y luego escanear
                    this.compressImage(file).then(compressedFile => {
                        // Reemplazar archivo en input (hack)
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedFile);
                        folder.files = dataTransfer.files;

                        // Escanear
                        this.scanLicense(compressedFile, folder);
                    });
                },

                scanLicense(file, inputElement) {
                    this.isScanning = true;
                    this.scanMessage = 'Iniciando escáner OCR...';
                    this.scanProgress = 0;

                    Tesseract.recognize(
                        file,
                        'eng',
                        {
                            logger: m => {
                                if (m.status === 'recognizing text') {
                                    this.scanProgress = Math.round(m.progress * 100);
                                    this.scanMessage = 'Analizando imagen: ' + this.scanProgress + '%';
                                } else {
                                    this.scanMessage = m.status;
                                }
                            }
                        }
                    ).then(({ data: { text } }) => {
                        console.log('Texto OCR:', text);
                        
                        // 1. Validar RUT
                        const rutValid = this.validateRutMatch(text);
                        
                        if (!rutValid) {
                            this.isScanning = false;
                            this.scanMessage = '❌ Error: El RUT de la licencia no coincide con su perfil.';
                            this.errorMessage = 'El RUT detectado en la licencia no coincide con el RUT registrado en su perfil. Por favor, suba su propia licencia para continuar.';
                            this.showErrorModal = true;
                            
                            // Limpiar input y preview
                            this.licensePreview = null;
                            inputElement.value = ''; // Limpiar el input file real
                            
                            return;
                        }

                        // 2. Si el RUT coincide, extraer fecha
                        this.extractDate(text);
                        this.isScanning = false;
                    }).catch(err => {
                        console.error(err);
                        this.isScanning = false;
                        this.scanMessage = 'Error al escanear.';
                    });
                },

                validateRutMatch(text) {
                    // Obtener RUT del usuario (desde backend) y limpiarlo
                    const userRutRaw = '{{ $user->rut }}';
                    if (!userRutRaw) return true; 
                    
                    const userRutClean = userRutRaw.replace(/[^0-9kK]/g, '').toUpperCase();
                    const textClean = text.replace(/[^0-9kK]/g, '').toUpperCase();
                    
                    if (textClean.includes(userRutClean)) {
                        return true;
                    }

                    const userRutBody = userRutClean.slice(0, -1);
                    return textClean.includes(userRutBody);
                },

                async compressImage(file) {
                    this.scanMessage = 'Optimizando imagen...';

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
                },

                extractDate(text) {
                    const datePatterns = [
                        /\b(\d{2})[-/](\d{2})[-/](\d{4})\b/g, // DD/MM/YYYY
                        /\b(\d{4})[-/](\d{2})[-/](\d{2})\b/g  // YYYY-MM-DD
                    ];

                    let foundDates = [];

                    datePatterns.forEach(pattern => {
                        let match;
                        while ((match = pattern.exec(text)) !== null) {
                            if (match[3].length === 4) { // DD/MM/YYYY
                                foundDates.push(new Date(match[3] + '-' + match[2] + '-' + match[1]));
                            } else { // YYYY-MM-DD
                                foundDates.push(new Date(match[0]));
                            }
                        }
                    });

                    if (foundDates.length > 0) {
                        foundDates = foundDates.filter(d => !isNaN(d.getTime()));
                        if (foundDates.length > 0) {
                            foundDates.sort((a, b) => b - a);
                            const bestDate = foundDates[0];
                            const formatted = bestDate.toISOString().split('T')[0];
                            document.getElementById('license_expires_at').value = formatted;
                            this.scanMessage = '✅ Licencia validada. Vence: ' + bestDate.toLocaleDateString();
                        } else {
                            this.scanMessage = '✅ Licencia validada (Fecha no detectada).';
                        }
                    } else {
                         this.scanMessage = '✅ Licencia validada (Fecha no detectada).';
                    }
                }
            }">

                <!-- Error Modal -->
                <div x-show="showErrorModal" style="display: none;" 
                    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-sm"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-sm w-full mx-4 relative">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full mb-4">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-center text-gray-900 dark:text-gray-100 mb-2">Error de Validación</h3>
                        <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6" x-text="errorMessage"></p>
                        <button @click="showErrorModal = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                            Entendido
                        </button>
                    </div>
                </div>

                <!-- License Photo Input -->
                <div class="mb-4" id="license-section">
                    <x-input-label for="license_photo" :value="__('Foto de la Licencia')" />

                    <!-- Preview Area -->
                    <div class="mt-2 mb-4" x-show="!licensePreview">
                        @if ($user->license_photo_path)
                            <img src="{{ asset('storage/' . $user->license_photo_path) }}" alt="Licencia"
                                @click="$dispatch('view-image', '{{ asset('storage/' . $user->license_photo_path) }}')"
                                class="h-40 w-auto object-cover rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:opacity-75 transition">
                        @else
                            <div class="h-40 w-full max-w-sm flex items-center justify-center bg-gray-100 dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-md">
                                <span class="text-gray-400 text-sm">Sin foto de licencia</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-2 mb-4" x-show="licensePreview" style="display: none;">
                         <img :src="licensePreview" @click="$dispatch('view-image', licensePreview)" class="h-40 w-auto object-cover rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:opacity-75 transition">
                    </div>

                    <div class="flex items-center gap-4">
                        <x-secondary-button type="button" x-on:click.prevent="$refs.license.click()">
                            {{ __('Subir Foto Licencia') }}
                        </x-secondary-button>
                        
                        <!-- Scanning Status -->
                        <div x-show="isScanning" class="flex items-center text-sm text-blue-500">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="scanMessage"></span>
                        </div>
                        <div x-show="!isScanning && scanMessage" class="text-sm text-green-500" x-text="scanMessage"></div>
                    </div>

                    <input type="file" id="license_photo" class="hidden" x-ref="license" name="license_photo"
                        accept="image/*"
                        x-on:change="processLicense($refs.license)" />
                    <x-input-error class="mt-2" :messages="$errors->get('license_photo')" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        La imagen será escaneada automáticamente para detectar la fecha de vencimiento.
                    </p>
                </div>

                <!-- Expiration Date -->
                <div>
                    <x-input-label for="license_expires_at" :value="__('Fecha de Vencimiento')" />
                    <x-text-input id="license_expires_at" name="license_expires_at" type="date" class="mt-1 block w-full bg-gray-50 dark:bg-gray-900" 
                        :value="old('license_expires_at', optional($user->license_expires_at)->format('Y-m-d'))" />
                    <x-input-error class="mt-2" :messages="$errors->get('license_expires_at')" />
                </div>
            </div>

        </div>

        <!-- Información Personal Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Información Personal') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nombres')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required
                        autofocus autocomplete="given-name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <!-- Last Name -->
                <div>
                    <x-input-label for="last_name" :value="__('Apellidos')" />
                    <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required
                        autocomplete="family-name" />
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>

                <!-- RUT -->
                <div x-data="{
                    rut: '{{ old('rut', $user->rut) }}',
                    error: '',
                    formatRut() {
                        let value = this.rut.replace(/[^0-9kK]/g, '').toUpperCase();
                        if (value.length > 1) {
                            const dv = value.slice(-1);
                            let body = value.slice(0, -1);
                            body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            this.rut = body + '-' + dv;
                        } else {
                            this.rut = value;
                        }
                        this.validateRut();
                    },
                    validateRut() {
                        let value = this.rut.replace(/[^0-9kK]/g, '').toUpperCase();
                        if (value.length < 8) {
                            this.error = '';
                            return;
                        }
                        const body = value.slice(0, -1);
                        const dv = value.slice(-1);
                        let suma = 0;
                        let multiplo = 2;
                        for (let i = body.length - 1; i >= 0; i--) {
                            suma += multiplo * body.charAt(i);
                            multiplo = (multiplo + 1) % 8 || 2;
                        }
                        const calculado = 11 - (suma % 11);
                        const dvCalculado = calculado === 11 ? '0' : (calculado === 10 ? 'K' : calculado.toString());
                        
                        if (dv !== dvCalculado) {
                            this.error = 'RUT inválido';
                            document.getElementById('rut').setCustomValidity('RUT inválido');
                        } else {
                            this.error = '';
                            document.getElementById('rut').setCustomValidity('');
                        }
                    }
                }">
                    <x-input-label for="rut" :value="__('RUT')" />
                    <x-text-input id="rut" name="rut" type="text" class="mt-1 block w-full" 
                        x-model="rut" 
                        @input="formatRut()" 
                        placeholder="12.345.678-9" 
                        maxlength="12" />
                    <p x-show="error" x-text="error" class="text-sm text-red-600 mt-1"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('rut')" />
                </div>

                <!-- Email -->
                <div class="md:col-span-2">
                    <x-input-label for="email" :value="__('Correo Electrónico')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div>
                            <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                {{ __('Su dirección de correo electrónico no está verificada.') }}

                                <button form="send-verification"
                                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    {{ __('Haga clic aquí para re-enviar el correo de verificación.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                    {{ __('Se ha enviado un nuevo enlace de verificación a su dirección de correo.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información de Contacto Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Información de Contacto') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div>
                    <x-input-label for="phone" :value="__('Teléfono')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)"
                        placeholder="+56 9 1234 5678" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <!-- Address (full width) -->
                <div class="md:col-span-2">
                    <x-input-label for="address" :value="__('Dirección')" />
                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)"
                        placeholder="Av. Siempre Viva 742" />
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
        </div>

        <!-- Información Laboral Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Información Laboral') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cargo -->
                <div>
                    <x-input-label for="cargo" :value="__('Cargo')" />
                    <x-text-input id="cargo" name="cargo" type="text" class="mt-1 block w-full" :value="old('cargo', $user->cargo)"
                        placeholder="Ej: Gerente de Operaciones" />
                    <x-input-error class="mt-2" :messages="$errors->get('cargo')" />
                </div>

                <!-- Departamento -->
                <div>
                    <x-input-label for="departamento" :value="__('Departamento')" />
                    <x-text-input id="departamento" name="departamento" type="text" class="mt-1 block w-full" :value="old('departamento', $user->departamento)"
                        placeholder="Ej: Logística" />
                    <x-input-error class="mt-2" :messages="$errors->get('departamento')" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
    <!-- Image Modal -->
    <div x-show="showImageModal" 
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        
        <div @click.away="showImageModal = false" class="relative max-w-4xl w-full max-h-full flex justify-center">
            <img :src="modalImageUrl" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-xl">
            
            <button @click="showImageModal = false" class="absolute -top-10 right-0 text-white hover:text-gray-300 focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</section>