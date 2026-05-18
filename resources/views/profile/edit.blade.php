<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-black text-2xl text-white leading-tight tracking-tight">
                {{ __('Configuración de Perfil') }}
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-md border border-blue-500/20">Mi Cuenta</span>
                <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                <span class="text-xs text-slate-400 font-bold uppercase tracking-tighter">{{ Auth::user()->email }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-[#0f172a] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Import Flatpickr -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
            <style>
                .flatpickr-calendar.dark {
                    background: #0f172a;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.5);
                    border: 1px solid #1e293b;
                    border-radius: 12px;
                }
                .flatpickr-day.selected { background: #3b82f6 !important; border-color: #3b82f6 !important; }
            </style>

            <!-- Información del Perfil - Premium Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative p-8 sm:p-10">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-600/5 rounded-full blur-[80px] pointer-events-none"></div>
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Cambiar Contraseña -->
                <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative p-8 sm:p-10">
                    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-600/5 rounded-full blur-[80px] pointer-events-none"></div>
                    @include('profile.partials.update-password-form')
                </div>

                <!-- Eliminar Cuenta -->
                <div class="bg-slate-900 border border-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden relative p-8 sm:p-10">
                    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-rose-600/5 rounded-full blur-[80px] pointer-events-none"></div>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>