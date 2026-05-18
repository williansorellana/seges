<section class="space-y-6">
    <header class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-rose-900/30 pb-8">
        <div>
            <h2 class="text-xl font-black text-white flex items-center gap-3 tracking-tight uppercase">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 border border-rose-500/20 shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </div>
                {{ __('Eliminar Cuenta') }}
            </h2>
            <p class="mt-2 text-[11px] text-slate-500 font-black uppercase tracking-widest">
                {{ __("Acción crítica e irreversible") }}
            </p>
        </div>
    </header>

    <div class="p-6 bg-rose-500/5 rounded-[2rem] border border-rose-500/10 shadow-inner">
        <p class="text-[11px] text-slate-400 font-bold mb-6 leading-relaxed">
            {{ __('Una vez que su cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Antes de proceder, descargue cualquier información que desee conservar.') }}
        </p>

        <button x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-8 py-4 bg-rose-600 hover:bg-rose-500 text-white font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-rose-600/20 hover:shadow-rose-600/40 transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-3 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" /></svg>
            {{ __('Eliminar Cuenta Definitivamente') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-10 bg-slate-900 border border-slate-800 rounded-[2.5rem] overflow-hidden relative">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-rose-500/10 rounded-full blur-[80px] pointer-events-none"></div>
            
            <h2 class="text-xl font-black text-white mb-4 tracking-tight uppercase">
                {{ __('¿Confirmar Eliminación?') }}
            </h2>

            <p class="text-xs text-slate-400 mb-8 leading-relaxed font-bold">
                {{ __('Esta acción es irreversible. Por favor, ingrese su contraseña para confirmar que desea eliminar permanentemente su cuenta y todos sus datos asociados.') }}
            </p>

            <div class="space-y-6">
                <div class="group">
                    <label for="password_deletion" class="block text-[10px] font-black text-slate-500 mb-2 uppercase tracking-widest group-focus-within:text-rose-500 transition-colors">{{ __('Contraseña de Confirmación') }}</label>
                    <div class="flex items-center border border-slate-800 rounded-2xl bg-slate-950 px-4 py-3.5 focus-within:border-rose-500 transition-all">
                        <input id="password_deletion" name="password" type="password" 
                            class="w-full bg-transparent border-none outline-none text-white placeholder-slate-700 text-sm font-bold"
                            placeholder="{{ __('Ingrese su contraseña') }}" />
                    </div>
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>
            </div>

            <div class="mt-10 flex flex-col sm:flex-row justify-end gap-4 border-t border-slate-800 pt-8">
                <button type="button" x-on:click="$dispatch('close')" 
                    class="px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all cursor-pointer">
                    {{ __('Cancelar') }}
                </button>

                <button type="submit" 
                    class="px-8 py-3.5 bg-rose-600 hover:bg-rose-500 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl shadow-rose-600/20 hover:shadow-rose-600/40 transition-all hover:-translate-y-1 cursor-pointer">
                    {{ __('Confirmar Eliminación') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>