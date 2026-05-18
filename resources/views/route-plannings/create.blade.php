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
                    /* Uiverse Input Design (breezy-wolverine-23) - Adapted to Blue-Slate */
                    .search-label {
                      display: flex;
                      align-items: center;
                      box-sizing: border-box;
                      position: relative;
                      border: 1px solid #334155; /* slate-700 */
                      border-radius: 8px; /* Matching the more squared look in screenshot */
                      overflow: hidden;
                      background: #1e293b; /* slate-800 */
                      padding: 9px;
                      cursor: text;
                    }
                    html:not(.dark) .search-label {
                      background: #f8fafc; /* slate-50 */
                      border-color: #cbd5e1; /* slate-300 */
                    }
                    html:not(.dark) .search-label:focus-within {
                      background: #ffffff;
                      border-color: #3b82f6; /* blue-500 */
                    }
                    html:not(.dark) .search-label input {
                      color: #0f172a; /* slate-900 */
                    }
                    .search-label:hover { border-color: #475569; /* slate-600 */ }
                    .search-label:focus-within { background: #0f172a; /* slate-900 */ border-color: #3b82f6; /* blue focus ring */ }
                    .search-label input { outline: none; width: 100%; border: none; background: none; color: #f1f5f9; /* slate-100 */ }
                    .search-label input::placeholder { color: #64748b; /* slate-500 */ }
                    .search-label input:focus+.slash-icon, .search-label input:valid+.slash-icon { display: none; }
                    .search-label input:valid~.search-icon { display: block; }
                    .search-label input:valid { width: calc(100% - 22px); transform: translateX(20px); }
                    .search-label svg, .slash-icon { position: absolute; color: #64748b; /* slate-500 */ }
                    .search-icon { display: none; width: 16px; height: auto; left: 12px; }
                    .slash-icon { 
                        right: 7px; 
                        border: 1px solid #334155; 
                        background: linear-gradient(-225deg, #1e293b, #334155); 
                        border-radius: 4px; 
                        text-align: center; 
                        box-shadow: inset 0 -2px 0 0 #0f172a, inset 0 0 1px 1px #475569, 0 1px 2px 1px rgba(0, 0, 0, 0.4); 
                        cursor: pointer; 
                        font-size: 12px; 
                        width: 18px; 
                        padding-bottom: 2px;
                    }
                    .slash-icon:active { 
                        box-shadow: inset 0 1px 0 0 #0f172a, inset 0 0 1px 1px #475569, 0 1px 2px 0 rgba(0, 0, 0, 0.4); 
                        text-shadow: 0 1px 0 #64748b; 
                        color: transparent; 
                    }
                    
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

                            <label class="search-label w-full md:w-1/2">
                                <input type="text" id="dateRange" required placeholder="Seleccionar rango de fechas...">
                                <kbd class="slash-icon">/</kbd>
                                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 56.966 56.966"><path d="M55.146 51.887 41.588 37.786A22.926 22.926 0 0 0 46.984 23c0-12.682-10.318-23-23-23s-23 10.318-23 23 10.318 23 23 23c4.761 0 9.298-1.436 13.177-4.162l13.661 14.208c.571.593 1.339.92 2.162.92.779 0 1.518-.297 2.079-.837a3.004 3.004 0 0 0 .083-4.242zM23.984 6c9.374 0 17 7.626 17 17s-7.626 17-17 17-17-7.626-17-17 7.626-17 17-17z" fill="currentColor"></path></svg>
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Haz clic para abrir el calendario interactivo. Selecciona inicio y fin.</p>
                        </div>

                        <!-- Destino: Región y Ciudad -->
                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" x-data="locationAutocomplete()">
                            
                            <!-- Región -->
                            <div class="relative">
                                <label for="region" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Región <span class="text-red-500">*</span></label>
                                <label class="search-label w-full">
                                    <input type="text" name="region" id="region" x-model="searchRegion" @input="openRegion = true" @focus="openRegion = true" @click.away="openRegion = false" @keydown.escape="openRegion = false" required autocomplete="off" placeholder="Ej: Región del Biobío">
                                    <kbd class="slash-icon">/</kbd>
                                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 56.966 56.966"><path d="M55.146 51.887 41.588 37.786A22.926 22.926 0 0 0 46.984 23c0-12.682-10.318-23-23-23s-23 10.318-23 23 10.318 23 23 23c4.761 0 9.298-1.436 13.177-4.162l13.661 14.208c.571.593 1.339.92 2.162.92.779 0 1.518-.297 2.079-.837a3.004 3.004 0 0 0 .083-4.242zM23.984 6c9.374 0 17 7.626 17 17s-7.626 17-17 17-17-7.626-17-17 7.626-17 17-17z" fill="currentColor"></path></svg>
                                </label>
                                
                                <ul x-show="openRegion && filteredRegions.length > 0" x-transition class="absolute z-50 w-full mt-1 bg-white dark:bg-[#1e293b] shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm border border-gray-100 dark:border-slate-700" style="display: none;">
                                    <template x-for="region in filteredRegions" :key="region">
                                        <li @click="selectRegion(region)" class="text-gray-900 dark:text-gray-200 cursor-pointer select-none relative py-2 px-4 hover:bg-slate-100 dark:hover:bg-blue-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                            <span x-text="region" class="block truncate"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Ciudad -->
                            <div class="relative">
                                <label for="destination" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Destino (Ciudad/Comuna) <span class="text-red-500">*</span></label>
                                <label class="search-label w-full">
                                    <input type="text" name="destination" id="destination" x-model="searchCity" @input="openCity = true" @focus="openCity = true" @click.away="openCity = false" @keydown.escape="openCity = false" required autocomplete="off" placeholder="Ej: Concepción">
                                    <kbd class="slash-icon">/</kbd>
                                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 56.966 56.966"><path d="M55.146 51.887 41.588 37.786A22.926 22.926 0 0 0 46.984 23c0-12.682-10.318-23-23-23s-23 10.318-23 23 10.318 23 23 23c4.761 0 9.298-1.436 13.177-4.162l13.661 14.208c.571.593 1.339.92 2.162.92.779 0 1.518-.297 2.079-.837a3.004 3.004 0 0 0 .083-4.242zM23.984 6c9.374 0 17 7.626 17 17s-7.626 17-17 17-17-7.626-17-17 7.626-17 17-17z" fill="currentColor"></path></svg>
                                </label>
                                
                                <ul x-show="openCity && filteredCities.length > 0" x-transition class="absolute z-50 w-full mt-1 bg-white dark:bg-[#1e293b] shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm border border-gray-100 dark:border-slate-700" style="display: none;">
                                    <template x-for="city in filteredCities" :key="city">
                                        <li @click="selectCity(city)" class="text-gray-900 dark:text-gray-200 cursor-pointer select-none relative py-2 px-4 hover:bg-slate-100 dark:hover:bg-blue-600 hover:text-blue-600 dark:hover:text-white transition-colors">
                                            <span x-text="city" class="block truncate"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="motive" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo del viaje <span class="text-red-500">*</span></label>
                            <label class="search-label w-full">
                                <input type="text" name="motive" id="motive" required placeholder="Ej: Visita a cliente X">
                                <kbd class="slash-icon">/</kbd>
                                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 56.966 56.966"><path d="M55.146 51.887 41.588 37.786A22.926 22.926 0 0 0 46.984 23c0-12.682-10.318-23-23-23s-23 10.318-23 23 10.318 23 23 23c4.761 0 9.298-1.436 13.177-4.162l13.661 14.208c.571.593 1.339.92 2.162.92.779 0 1.518-.297 2.079-.837a3.004 3.004 0 0 0 .083-4.242zM23.984 6c9.374 0 17 7.626 17 17s-7.626 17-17 17-17-7.626-17-17 7.626-17 17-17z" fill="currentColor"></path></svg>
                            </label>
                        </div>

                        <!-- Acompañantes -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="companions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Acompañantes
                                <span class="text-xs text-gray-400 font-normal ml-1">(Opcional)</span>
                            </label>
                            <div class="search-label w-full" style="align-items: flex-start; min-height: 70px;">
                                <textarea name="companions" id="companions" rows="2" placeholder="Ej: Juan Pérez, María González, Carlos López..." class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm resize-y" style="background: none; color: #f1f5f9;"></textarea>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Escribe los nombres de las personas que te acompañarán, separados por coma.</p>
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
                                <label for="requested_funds" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto Solicitado ($)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="requested_funds" id="requested_funds" min="0" class="pl-8 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej: 50000" x-bind:required="requiresFunds">
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
                            
                            <div x-show="requiresAmipass" x-transition.opacity class="ml-12" style="display: none;">
                                <label for="amipass_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cantidad de Días</label>
                                <input type="number" name="amipass_days" id="amipass_days" min="1" max="30" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" placeholder="Ej: 3" x-bind:required="requiresAmipass">
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
