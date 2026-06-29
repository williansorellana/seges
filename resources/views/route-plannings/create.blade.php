<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nueva Planificación de Ruta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-100 dark:border-gray-700">
                
                <div class="p-8 bg-gradient-to-br from-orange-50 to-white dark:from-gray-800 dark:to-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="bg-orange-500 p-2 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Formulario de Solicitud</h3>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Completa los detalles de tu viaje para solicitar fondos o alimentación antes de rendir.</p>
                </div>

                <!-- Import Flatpickr -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
                <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

                <style>
                    /* Custom Flatpickr Overrides for Seges */
                    .flatpickr-calendar.dark {
                        background: #2b2b2b;
                        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
                        border: 1px solid #444;
                        border-radius: 12px;
                    }
                    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
                        background: #f97316;
                        border-color: #f97316;
                    }
                </style>


                <form action="{{ route('route-plannings.store') }}" method="POST" class="p-8" x-data="{ requiresFunds: false, requiresAmipass: false }">
                    @csrf
                    
                    @php
                        $now = now();

                        $thisWeekWednesdayLimit = now()
                            ->startOfWeek()
                            ->addDays(2)
                            ->setTime(13, 0);

                        if ($now->lessThanOrEqualTo($thisWeekWednesdayLimit)) {
                            $fundsDate = now()
                                ->startOfWeek()
                                ->addDays(4);
                        } else {
                            $fundsDate = now()
                                ->startOfWeek()
                                ->addDays(11);
                        }
                    @endphp

                    <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                        <strong>Importante:</strong>
                        Las solicitudes ingresadas antes del miércoles a las 13:00 hrs tendrán fondos disponibles el viernes de la misma semana.
                        Las solicitudes ingresadas después de ese horario quedarán para el viernes de la semana siguiente.
                        <br>
                        <span class="font-semibold">
                            Según la fecha actual, la disponibilidad estimada sería: {{ $fundsDate->format('d/m/Y') }}.
                        </span>
                    </div>
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-lg p-4">
                            <ul class="list-disc pl-5 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- 1. Detalles Generales -->
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">1. Detalles del Viaje</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        
                        <!-- Tipo de Viaje -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Actividad <span class="text-red-500">*</span></label>
                            <div class="flex space-x-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="trip_type" value="terreno" class="form-radio text-orange-600 focus:ring-orange-500 border-gray-300" required>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Trabajo en Terreno</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="trip_type" value="reunion" class="form-radio text-orange-600 focus:ring-orange-500 border-gray-300" required>
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Reunión de Negocios</span>
                                </label>
                            </div>
                        </div>

                        <!-- Fechas con Flatpickr -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fechas del Viaje <span class="text-red-500">*</span></label>
                            
                            <!-- Hidden inputs for backend -->
                            <input type="hidden" name="start_date" id="start_date" required>
                            <input type="hidden" name="end_date" id="end_date" required>

                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full md:w-1/2">
                                <input type="text" id="dateRange" required placeholder="Seleccionar rango de fechas..." class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm">
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Haz clic para abrir el calendario interactivo. Selecciona inicio y fin.</p>
                        </div>

                        <!-- Destino: Región y Ciudad -->
                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="region" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Región <span class="text-red-500">*</span></label>
                                <select name="region" id="region" x-model="searchRegion" @change="searchCity = ''" class="w-full bg-[#1e293b] border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-100" required>
                                    <option value="">Seleccione una región</option>
                                    <template x-for="regionObj in dataset" :key="regionObj.region">
                                        <option :value="regionObj.region" x-text="regionObj.region"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label for="destination" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destino (Comuna) <span class="text-red-500">*</span></label>
                                <select name="destination" id="destination" x-model="searchCity" class="w-full bg-[#1e293b] border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-slate-100" required>
                                    <option value="">Seleccione una comuna</option>
                                    <template x-for="city in availableCities" :key="city">
                                        <option :value="city" x-text="city"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="col-span-1 md:col-span-2 mt-6" x-data="{ emails: [''] }">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correos para Notificación de Anexo</label>
                                <template x-for="(email, index) in emails" :key="index">
                                    <div class="flex gap-2 mb-2">
                                        <input type="email" name="notification_emails[]" x-model="emails[index]" placeholder="ejemplo@dimak.cl" class="flex-1 bg-[#1e293b] border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100">
                                        <button type="button" @click="emails.splice(index, 1)" class="px-3 bg-red-600/20 text-red-500 rounded-lg">x</button>
                                    </div>
                                </template>
                                <button type="button" @click="emails.push('')" class="text-xs text-blue-500 font-bold hover:underline">+ Agregar otro correo</button>
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="motive" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo del viaje <span class="text-red-500">*</span></label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <input type="text" name="motive" id="motive" required placeholder="Ej: Visita a cliente X" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm">
                            </div>
                        </div>

                        <!-- Acompañantes -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="companions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Acompañantes</label>
                            <textarea name="companions" id="companions" rows="2" class="w-full bg-[#1e293b] border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors" placeholder="Nombres de las personas que viajan con usted (Opcional)"></textarea>
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-6" x-data="{ emails: [''] }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correos para Notificación de Anexo</label>
                            <template x-for="(email, index) in emails" :key="index">
                                <div class="flex gap-2 mb-2">
                                    <input type="email" name="notification_emails[]" x-model="emails[index]" placeholder="ejemplo@dimak.cl" class="flex-1 bg-[#1e293b] border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100">
                                    <button type="button" @click="emails.splice(index, 1)" class="px-3 bg-red-600/20 text-red-500 rounded-lg font-bold">X</button>
                                </div>
                            </template>
                            <button type="button" @click="emails.push('')" class="text-xs text-blue-500 font-bold hover:underline">+ Agregar otro correo</button>
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-6" x-data="{ emails: [''] }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correos para Notificación de Anexo</label>
                            <template x-for="(email, index) in emails" :key="index">
                                <div class="flex gap-2 mb-2">
                                    <input type="email" name="notification_emails[]" x-model="emails[index]" placeholder="ejemplo@dimak.cl" class="flex-1 bg-[#1e293b] border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100">
                                    <button type="button" @click="emails.splice(index, 1)" class="px-3 bg-red-600/20 text-red-500 rounded-lg font-bold">X</button>
                                </div>
                            </template>
                            <button type="button" @click="emails.push('')" class="text-xs text-blue-500 font-bold hover:underline">+ Agregar otro correo</button>
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-6" x-data="{ emails: [''] }">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correos para Notificación de Anexo</label>
                            <template x-for="(email, index) in emails" :key="index">
                                <div class="flex gap-2 mb-2">
                                    <input type="email" name="notification_emails[]" x-model="emails[index]" placeholder="ejemplo@dimak.cl" class="flex-1 bg-[#1e293b] border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100">
                                    <button type="button" @click="emails.splice(index, 1)" class="px-3 bg-red-600/20 text-red-500 rounded-lg">x</button>
                                </div>
                            </template>
                            <button type="button" @click="emails.push('')" class="text-xs text-blue-500 font-bold hover:underline">+ Agregar otro correo</button>
                        </div>
                    </div>

                    <!-- 2. Solicitudes Financieras -->
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">2. Fondos y Viáticos</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        
                        <!-- Fondos por Rendir -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl border border-gray-200 dark:border-gray-600 transition-all">
                            <label class="flex items-center cursor-pointer mb-3">
                                <div class="relative">
                                    <input type="checkbox" name="requires_funds" value="1" class="sr-only" x-model="requiresFunds">
                                    <div class="block bg-gray-300 dark:bg-gray-600 w-10 h-6 rounded-full transition" :class="{'bg-orange-500': requiresFunds}"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform" :class="{'translate-x-4': requiresFunds}"></div>
                                </div>
                                <div class="ml-3 font-medium text-gray-800 dark:text-gray-200">
                                    Solicitar Fondos por Rendir
                                </div>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 ml-12">Adelanto de dinero para peajes, combustible, alojamiento, etc.</p>
                            
                            <div x-show="requiresFunds" x-transition.opacity class="ml-12" style="display: none;">
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                    <span class="text-slate-500 text-sm mr-2">$</span>
                                    <input type="number" name="requested_funds" id="requested_funds" min="1" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm" placeholder="Ej: 50000" x-bind:required="requiresFunds">
                                </div>
                            </div>
                        </div>

                        <!-- Amipass -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl border border-gray-200 dark:border-gray-600 transition-all">
                            <label class="flex items-center cursor-pointer mb-3">
                                <div class="relative">
                                    <input type="checkbox" name="requires_amipass" value="1" class="sr-only" x-model="requiresAmipass">
                                    <div class="block bg-gray-300 dark:bg-gray-600 w-10 h-6 rounded-full transition" :class="{'bg-green-500': requiresAmipass}"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform" :class="{'translate-x-4': requiresAmipass}"></div>
                                </div>
                                <div class="ml-3 font-medium text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                    Solicitar Tarjeta Amipass
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">Alimentación</span>
                                </div>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 ml-12">Recarga diaria para almuerzo/comidas durante el viaje.</p>
                            
                            <div x-show="requiresAmipass" x-transition.opacity class="ml-12 space-y-4" style="display: none;">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="amipass_start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Hora de salida
                                        </label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                            <input
                                                type="time"
                                                name="amipass_start_time"
                                                id="amipass_start_time"
                                                class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none"
                                                x-bind:required="requiresAmipass"
                                            >
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Hora en que comienza el desplazamiento el primer día.
                                        </p>
                                    </div>

                                    <div>
                                        <label for="amipass_end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Hora de regreso
                                        </label>
                                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                                            <input
                                                type="time"
                                                name="amipass_end_time"
                                                id="amipass_end_time"
                                                class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none"
                                                x-bind:required="requiresAmipass"
                                            >
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Hora estimada en que termina el viaje el último día.
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/40 rounded-lg p-3">
                                    <p class="text-xs text-green-800 dark:text-green-300 font-semibold">
                                        El monto Amipass se calculará automáticamente según las fechas del viaje, la hora de salida y la hora de regreso registradas.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end mt-8 border-t border-gray-200 dark:border-gray-800 pt-6">
                        <a href="{{ route('route-plannings.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-slate-600 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-700 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:ring-offset-[#1e293b] mr-4">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-md shadow-blue-500/20 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-[#1e293b] transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Enviar Solicitud
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script>
        // Initialize Flatpickr
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "es",
                monthSelectorType: "static", // Removes the ugly long native dropdown
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        // Format dates to YYYY-MM-DD for the backend
                        const start = selectedDates[0].toLocaleDateString('en-CA'); // en-CA format is YYYY-MM-DD
                        const end = selectedDates[1].toLocaleDateString('en-CA');
                        
                        document.getElementById('start_date').value = start;
                        document.getElementById('end_date').value = end;
                    } else if (selectedDates.length === 1) {
                        const start = selectedDates[0].toLocaleDateString('en-CA');
                        document.getElementById('start_date').value = start;
                        document.getElementById('end_date').value = start;
                    } else {
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                    }
                }
            });
        });

        function locationAutocomplete() {
            return {
                searchRegion: '',
                searchCity: '',
                openRegion: false,
                openCity: false,
                dataset: [
                    { "region": "Arica y Parinacota", "comunas": ["Arica", "Camarones", "Putre", "General Lagos"] },
                    { "region": "Tarapacá", "comunas": ["Iquique", "Alto Hospicio", "Pozo Almonte", "Camiña", "Colchane", "Huara", "Pica"] },
                    { "region": "Antofagasta", "comunas": ["Antofagasta", "Mejillones", "Sierra Gorda", "Taltal", "Calama", "Ollagüe", "San Pedro de Atacama", "Tocopilla", "María Elena"] },
                    { "region": "Atacama", "comunas": ["Copiapó", "Caldera", "Tierra Amarilla", "Chañaral", "Diego de Almagro", "Vallenar", "Alto del Carmen", "Freirina", "Huasco"] },
                    { "region": "Coquimbo", "comunas": ["La Serena", "Coquimbo", "Andacollo", "La Higuera", "Paiguano", "Vicuña", "Illapel", "Canela", "Los Vilos", "Salamanca", "Ovalle", "Combarbalá", "Monte Patria", "Punitaqui", "Río Hurtado"] },
                    { "region": "Valparaíso", "comunas": ["Valparaíso", "Casablanca", "Concón", "Juan Fernández", "Puchuncaví", "Quintero", "Viña del Mar", "Isla de Pascua", "Los Andes", "Calle Larga", "Rinconada", "San Esteban", "La Ligua", "Cabildo", "Papudo", "Petorca", "Zapallar", "Quillota", "Calera", "Hijuelas", "La Cruz", "Nogales", "San Antonio", "Algarrobo", "Cartagena", "El Quisco", "El Tabo", "Santo Domingo", "San Felipe", "Catemu", "Llaillay", "Panquehue", "Putaendo", "Santa María", "Quilpué", "Limache", "Olmué", "Villa Alemana"] },
                    { "region": "Región del Libertador Gral. Bernardo O’Higgins", "comunas": ["Rancagua", "Codegua", "Coinco", "Coltauco", "Doñihue", "Graneros", "Las Cabras", "Machalí", "Malloa", "Mostazal", "Olivar", "Peumo", "Pichidegua", "Quinta de Tilcoco", "Rengo", "Requínoa", "San Vicente", "Pichilemu", "La Estrella", "Litueche", "Marchihue", "Navidad", "Paredones", "San Fernando", "Chépica", "Chimbarongo", "Lolol", "Nancagua", "Palmilla", "Peralillo", "Placilla", "Pumanque", "Santa Cruz"] },
                    { "region": "Región del Maule", "comunas": ["Talca", "Constitución", "Curepto", "Empedrado", "Maule", "Pelarco", "Pencahue", "Río Claro", "San Clemente", "San Rafael", "Cauquenes", "Chanco", "Pelluhue", "Curicó", "Hualañé", "Licantén", "Molina", "Rauco", "Romeral", "Sagrada Familia", "Teno", "Vichuquén", "Linares", "Colbún", "Longaví", "Parral", "Retiro", "San Javier", "Villa Alegre", "Yerbas Buenas"] },
                    { "region": "Región de Ñuble", "comunas": ["Cobquecura", "Coelemu", "Ninhue", "Portezuelo", "Quirihue", "Ránquil", "Treguaco", "Bulnes", "Chillán Viejo", "Chillán", "El Carmen", "Pemuco", "Pinto", "Quillón", "San Ignacio", "Yungay", "Coihueco", "Ñiquén", "San Carlos", "San Fabián", "San Nicolás"] },
                    { "region": "Región del Biobío", "comunas": ["Concepción", "Coronel", "Chiguayante", "Florida", "Hualqui", "Lota", "Penco", "San Pedro de la Paz", "Santa Juana", "Talcahuano", "Tomé", "Hualpén", "Lebu", "Arauco", "Cañete", "Contulmo", "Curanilahue", "Los Álamos", "Tirúa", "Los Ángeles", "Antuco", "Cabrero", "Laja", "Mulchén", "Nacimiento", "Negrete", "Quilaco", "Quilleco", "San Rosendo", "Santa Bárbara", "Tucapel", "Yumbel", "Alto Biobío"] },
                    { "region": "Región de la Araucanía", "comunas": ["Temuco", "Carahue", "Cunco", "Curarrehue", "Freire", "Galvarino", "Gorbea", "Lautaro", "Loncoche", "Melipeuco", "Nueva Imperial", "Padre las Casas", "Perquenco", "Pitrufquén", "Pucón", "Saavedra", "Teodoro Schmidt", "Toltén", "Vilcún", "Villarrica", "Cholchol", "Angol", "Collipulli", "Curacautín", "Ercilla", "Lonquimay", "Los Sauces", "Lumaco", "Purén", "Renaico", "Traiguén", "Victoria"] },
                    { "region": "Región de Los Ríos", "comunas": ["Valdivia", "Corral", "Lanco", "Los Lagos", "Máfil", "Mariquina", "Paillaco", "Panguipulli", "La Unión", "Futrono", "Lago Ranco", "Río Bueno"] },
                    { "region": "Región de Los Lagos", "comunas": ["Puerto Montt", "Calbuco", "Cochamó", "Fresia", "Frutillar", "Los Muermos", "Llanquihue", "Maullín", "Puerto Varas", "Castro", "Ancud", "Chonchi", "Curaco de Vélez", "Dalcahue", "Puqueldón", "Queilén", "Quellón", "Quemchi", "Quinchao", "Osorno", "Puerto Octay", "Purranque", "Puyehue", "Entre Lagos", "Río Negro", "San Juan de la Costa", "San Pablo", "Chaitén", "Futaleufú", "Hualaihué", "Palena"] },
                    { "region": "Región Aisén del Gral. Carlos Ibáñez del Campo", "comunas": ["Coihaique", "Lago Verde", "Aisén", "Cisnes", "Guaitecas", "Cochrane", "O’Higgins", "Tortel", "Chile Chico", "Río Ibáñez"] },
                    { "region": "Región de Magallanes y de la Antártica Chilena", "comunas": ["Punta Arenas", "Laguna Blanca", "Río Verde", "San Gregorio", "Cabo de Hornos (Ex Navarino)", "Antártica", "Porvenir", "Primavera", "Timaukel", "Natales", "Torres del Paine"] },
                    { "region": "Región Metropolitana de Santiago", "comunas": ["Cerrillos", "Cerro Navia", "Conchalí", "El Bosque", "Estación Central", "Huechuraba", "Independencia", "La Cisterna", "La Florida", "La Granja", "La Pintana", "La Reina", "Las Condes", "Lo Barnechea", "Lo Espejo", "Lo Prado", "Macul", "Maipú", "Ñuñoa", "Pedro Aguirre Cerda", "Peñalolén", "Providencia", "Pudahuel", "Quilicura", "Quinta Normal", "Recoleta", "Renca", "Santiago", "San Joaquín", "San Miguel", "San Ramón", "Vitacura", "Puente Alto", "Pirque", "San José de Maipo", "Colina", "Lampa", "Tiltil", "San Bernardo", "Buin", "Calera de Tango", "Paine", "Melipilla", "Alhué", "Curacaví", "María Pinto", "San Pedro", "Talagante", "El Monte", "Isla de Maipo", "Padre Hurtado", "Peñaflor"] }
                ],

                get availableCities() {
                    let regionData = this.dataset.find(r => r.region === this.searchRegion);
                    return regionData ? regionData.comunas : [];
                }
                get filteredRegions() {
                    if (this.searchRegion === '') {
                        return this.dataset.map(item => item.region);
                    }
                    return this.dataset
                        .map(item => item.region)
                        .filter(region => region.toLowerCase().includes(this.searchRegion.toLowerCase()));
                },
                get filteredCities() {
                    let validRegion = this.dataset.find(item => item.region === this.searchRegion);
                    let possibleCities = validRegion ? validRegion.comunas : this.dataset.flatMap(item => item.comunas);
                    
                    if (this.searchCity === '') {
                        // If no city typed, show all cities of the selected region (or empty if no region)
                        return validRegion ? possibleCities : [];
                    }
                    
                    return possibleCities
                        .filter(city => city.toLowerCase().includes(this.searchCity.toLowerCase()));
                },
                selectRegion(region) {
                    this.searchRegion = region;
                    this.openRegion = false;
                    // Optional: clear city if the new region doesn't contain the current city
                    let regionData = this.dataset.find(r => r.region === region);
                    if (regionData && !regionData.comunas.includes(this.searchCity)) {
                        this.searchCity = '';
                    }
                },
                selectCity(city) {
                    this.searchCity = city;
                    this.openCity = false;
                    
                    // Auto-fill region if not filled or invalid
                    if (!this.dataset.find(r => r.region === this.searchRegion)) {
                        let foundRegion = this.dataset.find(r => r.comunas.includes(city));
                        if (foundRegion) {
                            this.searchRegion = foundRegion.region;
                        }
                    }
                }
            }
        }
    </script>
</x-app-layout>
