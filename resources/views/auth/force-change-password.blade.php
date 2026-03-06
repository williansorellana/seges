<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Para continuar, necesitamos que actualices tu contraseña y completes la información de tu perfil.') }}
    </div>

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        <div class="space-y-4">
            <div x-data="{
                rut: '{{ old('rut', auth()->user()->rut) }}',
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
                <x-text-input id="rut" name="rut" type="text" class="mt-1 block w-full" x-model="rut"
                    @input="formatRut()" placeholder="12.345.678-9" maxlength="12" required />
                <p x-show="error" x-text="error" class="text-sm text-red-600 mt-1"></p>
                <x-input-error class="mt-2" :messages="$errors->get('rut')" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Teléfono')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', auth()->user()->phone)" placeholder="+56 9 1234 5678" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="address" :value="__('Dirección')" />
                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', auth()->user()->address)" placeholder="Av. Siempre Viva 742" required />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <div x-data="{ show: false }">
                <x-input-label for="password" :value="__('Nueva Contraseña')" />
                <div class="relative mt-1">
                    <x-text-input id="password" 
                        class="block w-full pr-10" 
                        x-bind:type="show ? 'text' : 'password'" 
                        name="password" 
                        required
                        autocomplete="new-password" />
                    
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.18-3.64m6.308-1.353a4.5 4.5 0 015.657 5.657m0 0l-5.657-5.657m0 0L3 3m3.343 3.343L3 3m18 18l-3.343-3.343" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div x-data="{ show: false }">
                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                <div class="relative mt-1">
                    <x-text-input id="password_confirmation" 
                        class="block w-full pr-10" 
                        x-bind:type="show ? 'text' : 'password'"
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password" />

                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.18-3.64m6.308-1.353a4.5 4.5 0 015.657 5.657m0 0l-5.657-5.657m0 0L3 3m3.343 3.343L3 3m18 18l-3.343-3.343" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Guardar y Continuar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>