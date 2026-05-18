<section>
    <header class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-800 pb-8">
        <div>
            <h2 class="text-xl font-black text-white flex items-center gap-3 tracking-tight uppercase">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/20 shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                {{ __('Seguridad de la Cuenta') }}
            </h2>
            <p class="mt-2 text-[11px] text-slate-500 font-black uppercase tracking-widest">
                {{ __("Protección y gestión de credenciales") }}
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('put')

        <div x-data="{ show: false }" class="group">
            <label for="current_password" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Contraseña Actual') }}</label>
            <div class="relative flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                <input id="current_password" name="current_password" x-bind:type="show ? 'text' : 'password'" 
                    class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold pr-10" 
                    autocomplete="current-password" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-blue-400 transition-colors focus:outline-none cursor-pointer">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.18-3.64m6.308-1.353a4.5 4.5 0 015.657 5.657m0 0l-5.657-5.657m0 0L3 3m3.343 3.343L3 3m18 18l-3.343-3.343" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }" class="group">
            <label for="password" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Nueva Contraseña') }}</label>
            <div class="relative flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                <input id="password" name="password" x-bind:type="show ? 'text' : 'password'" 
                    class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold pr-10" 
                    autocomplete="new-password" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-blue-400 transition-colors focus:outline-none cursor-pointer">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.18-3.64m6.308-1.353a4.5 4.5 0 015.657 5.657m0 0l-5.657-5.657m0 0L3 3m3.343 3.343L3 3m18 18l-3.343-3.343" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }" class="group">
            <label for="password_confirmation" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-blue-400 transition-colors">{{ __('Confirmar Contraseña') }}</label>
            <div class="relative flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-blue-500 transition-all">
                <input id="password_confirmation" name="password_confirmation" x-bind:type="show ? 'text' : 'password'" 
                    class="w-full bg-transparent border-none outline-none text-white placeholder-slate-600 text-sm font-bold pr-10" 
                    autocomplete="new-password" />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-blue-400 transition-colors focus:outline-none cursor-pointer">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.18-3.64m6.308-1.353a4.5 4.5 0 015.657 5.657m0 0l-5.657-5.657m0 0L3 3m3.343 3.343L3 3m18 18l-3.343-3.343" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-6 pt-6 border-t border-slate-800">
            <button type="submit" class="px-8 py-3.5 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-indigo-600/20 hover:bg-indigo-500 hover:-translate-y-1 hover:shadow-indigo-600/40 transition-all cursor-pointer">
                {{ __('Actualizar Contraseña') }}
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)"
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    <span class="text-[10px] text-emerald-500 font-black uppercase tracking-widest">{{ __('Cambiado') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>