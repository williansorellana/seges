<section x-data="{ 
    showImageModal: false, 
    modalImageUrl: '', 
    openModal(url) { 
        if(url) {
            this.modalImageUrl = url; 
            this.showImageModal = true; 
        }
    } 
}" @view-image.window="openModal($event.detail)" class="relative">
    
    <header class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-800 pb-8">
        <div>
            <h2 class="text-xl font-black text-white flex items-center gap-3 tracking-tight uppercase">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                {{ __('Información del Perfil') }}
            </h2>
            <p class="mt-2 text-[11px] text-slate-500 font-black uppercase tracking-widest">
                {{ __("Gestión centralizada de identidad y documentos") }}
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-12" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Columna Izquierda: Fotografías y Documentos -->
            <div class="lg:col-span-4 space-y-8">
                
                <!-- Profile Photo Card -->
                <div class="bg-slate-950/40 p-6 rounded-[2rem] border border-slate-800 shadow-inner relative group/photo">
                    <label class="block text-[10px] font-black text-slate-500 mb-6 uppercase tracking-[0.2em]">{{ __('Foto de Perfil') }}</label>
                    
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
                                        const MAX_WIDTH = 1024;
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
                    }">
                        <div class="flex flex-col items-center gap-6">
                            <!-- Current Photo Display -->
                            <div x-show="! photoPreview" class="relative">
                                <div class="absolute -inset-1 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-full blur opacity-20 group-hover/photo:opacity-40 transition-opacity"></div>
                                @if ($user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}"
                                        @click="$dispatch('view-image', '{{ asset('storage/' . $user->profile_photo_path) }}')"
                                        class="relative rounded-full h-32 w-32 object-cover border-4 border-slate-900 shadow-2xl cursor-pointer hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="relative rounded-full h-32 w-32 bg-slate-900 border-4 border-slate-800 flex items-center justify-center text-blue-500 font-black text-4xl shadow-inner">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Preview Display -->
                            <div x-show="photoPreview" style="display: none;" class="relative">
                                <div class="absolute -inset-1 bg-gradient-to-tr from-emerald-500 to-teal-500 rounded-full blur opacity-40"></div>
                                <span class="relative block rounded-full w-32 h-32 bg-cover bg-no-repeat bg-center border-4 border-slate-900 shadow-2xl"
                                    x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                </span>
                            </div>

                            <button type="button" x-on:click.prevent="$refs.photo.click()" x-bind:disabled="isCompressing" class="px-6 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-blue-600/20 hover:bg-blue-500 hover:-translate-y-0.5 transition-all cursor-pointer disabled:opacity-50">
                                <span x-show="!isCompressing">{{ __('Cambiar Avatar') }}</span>
                                <span x-show="isCompressing" class="flex items-center gap-2">
                                    <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    {{ __('Procesando') }}
                                </span>
                            </button>
                        </div>

                        <input type="file" id="photo" class="hidden" x-ref="photo" name="photo" accept="image/*"
                            x-on:change="
                                const file = $refs.photo.files[0];
                                if (file) {
                                    photoName = file.name;
                                    compressImage(file).then(compressedFile => {
                                        const dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(compressedFile);
                                        $refs.photo.files = dataTransfer.files;
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL(compressedFile);
                                    });
                                }
                            " />
                        <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                    </div>
                </div>

                <!-- License Document Card -->
                <div class="bg-slate-950/40 p-6 rounded-[2rem] border border-slate-800 shadow-inner relative group/license">
                    <label class="block text-[10px] font-black text-slate-500 mb-6 uppercase tracking-[0.2em]">{{ __('Licencia de Conducir') }}</label>
                    <div x-data="{
                        licensePreview: null,
                        processLicense(folder) {
                            const file = folder.files[0];
                            if (!file) return;
                            const reader = new FileReader();
                            reader.onload = (e) => { this.licensePreview = e.target.result; };
                            reader.readAsDataURL(file);
                        }
                    }">
                        <div class="space-y-6">
                            <!-- Image Display -->
                            <div class="relative overflow-hidden rounded-2xl border-2 border-slate-800 bg-slate-900 aspect-[16/10] flex items-center justify-center">
                                <div x-show="!licensePreview" class="w-full h-full">
                                    @if ($user->license_photo_path)
                                        <img src="{{ asset('storage/' . $user->license_photo_path) }}" alt="Licencia"
                                            @click="$dispatch('view-image', '{{ asset('storage/' . $user->license_photo_path) }}')"
                                            class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-500 opacity-80 hover:opacity-100">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center gap-3">
                                            <svg class="w-8 h-8 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            <span class="text-[10px] text-slate-600 font-black uppercase tracking-widest">Sin Documento</span>
                                        </div>
                                    @endif
                                </div>
                                <div x-show="licensePreview" style="display: none;" class="w-full h-full">
                                    <img :src="licensePreview" @click="$dispatch('view-image', licensePreview)" class="w-full h-full object-cover cursor-pointer">
                                </div>
                                <!-- Overlay Button -->
                                <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover/license:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" x-on:click.prevent="$refs.license.click()" class="p-3 bg-white text-slate-900 rounded-full shadow-xl hover:scale-110 transition-transform cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="group">
                                <label for="license_expires_at" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Vencimiento Licencia') }}</label>
                                <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-900 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                    <input id="license_expires_at" name="license_expires_at" type="text" class="w-full bg-transparent border-none outline-none text-white text-sm font-bold cursor-pointer" value="{{ old('license_expires_at', optional($user->license_expires_at)->format('Y-m-d')) }}" required>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('license_expires_at')" />
                            </div>
                        </div>

                        <input type="file" id="license_photo" class="hidden" x-ref="license" name="license_photo" accept="image/*" x-on:change="processLicense($refs.license)" />
                        <x-input-error class="mt-2" :messages="$errors->get('license_photo')" />
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Formulario de Datos -->
            <div class="lg:col-span-8 space-y-12">
                
                <!-- Sección 1: Datos Identitarios -->
                <div class="space-y-6">
                    <h3 class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="w-8 h-[1px] bg-indigo-500/30"></span>
                        {{ __('Datos Personales') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="name" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Nombres') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="name" name="name" type="text" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" value="{{ old('name', $user->name) }}" required autofocus autocomplete="given-name">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="group">
                            <label for="last_name" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Apellidos') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="last_name" name="last_name" type="text" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" value="{{ old('last_name', $user->last_name) }}" required autocomplete="family-name">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

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
                        }" class="group">
                            <label for="rut" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('RUT') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="rut" name="rut" type="text" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-bold font-mono" x-model="rut" @input="formatRut()" placeholder="12.345.678-9" maxlength="12">
                            </div>
                            <p x-show="error" x-text="error" class="text-[10px] text-rose-500 mt-1 font-bold uppercase"></p>
                            <x-input-error class="mt-2" :messages="$errors->get('rut')" />
                        </div>

                        <div class="group">
                            <label for="email" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Email Corporativo') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="email" name="email" type="email" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                <div class="mt-3 px-4 py-2 bg-amber-500/5 rounded-xl border border-amber-500/20">
                                    <p class="text-[10px] text-amber-500 font-black uppercase flex items-center justify-between">
                                        {{ __('Correo no verificado') }}
                                        <button form="send-verification" class="text-blue-400 hover:text-blue-300 underline cursor-pointer">
                                            {{ __('Re-enviar') }}
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-1 text-[9px] text-emerald-400 font-bold italic">
                                            {{ __('Enlace enviado al correo.') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Contacto y Ubicación -->
                <div class="space-y-6">
                    <h3 class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="w-8 h-[1px] bg-indigo-500/30"></span>
                        {{ __('Contacto y Ubicación') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="phone" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Teléfono') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="phone" name="phone" type="text" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-bold" value="{{ old('phone', $user->phone) }}" placeholder="+56 9 1234 5678">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                        </div>

                        <div class="group">
                            <label for="address" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Dirección de Residencia') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="address" name="address" type="text" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-bold" value="{{ old('address', $user->address) }}" placeholder="Ej: Av. Principal 123, Santiago">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Cargo y Departamento -->
                <div class="space-y-6">
                    <h3 class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.2em] flex items-center gap-3">
                        <span class="w-8 h-[1px] bg-indigo-500/30"></span>
                        {{ __('Información Laboral') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="cargo" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Cargo / Puesto') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="cargo" name="cargo" type="text" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-bold" value="{{ old('cargo', $user->cargo) }}" placeholder="Ej: Analista Senior">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('cargo')" />
                        </div>

                        <div class="group">
                            <label for="departamento" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Departamento') }}</label>
                            <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                                <input id="departamento" name="departamento" type="text" class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-bold" value="{{ old('departamento', $user->departamento) }}" placeholder="Ej: Finanzas">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('departamento')" />
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="pt-10 flex items-center justify-end gap-6 border-t border-slate-800">
                    @if (session('status') === 'profile-updated')
                        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                            class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            <span class="text-[10px] text-emerald-500 font-black uppercase tracking-widest">{{ __('Guardado correctamente') }}</span>
                        </div>
                    @endif
                    
                    <button type="submit" class="group px-10 py-4 bg-blue-600 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-600/20 hover:bg-blue-500 hover:-translate-y-1 hover:shadow-blue-600/40 transition-all flex items-center gap-3 cursor-pointer">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        {{ __('Actualizar Perfil') }}
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Image Modal Viewer -->
    <div x-show="showImageModal" 
        style="display: none;"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/95 backdrop-blur-md p-6"
        @click.away="showImageModal = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        
        <div class="relative max-w-5xl w-full flex justify-center group">
            <img :src="modalImageUrl" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-[0_0_100px_rgba(59,130,246,0.15)] border border-slate-800">
            
            <button @click="showImageModal = false" class="absolute -top-14 right-0 text-slate-400 hover:text-white transition-colors cursor-pointer bg-slate-900/50 p-2 rounded-full border border-slate-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Initialize Flatpickr -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof flatpickr !== 'undefined' && document.getElementById('license_expires_at')) {
                flatpickr("#license_expires_at", {
                    locale: "es",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M, Y",
                    theme: "dark",
                    disableMobile: true
                });
            }
        });
    </script>
</section>