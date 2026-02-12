<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Catálogo de Salas') }}
            </h2>
            
            <a href="{{ route('reservations.my_reservations') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                📅 Mis Reservas
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="calendarApp()" x-init="init()">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            

            @if($errors->any())
                <x-modal name="error-validation-modal" :show="true" focusable>
                    <div class="p-6 bg-gray-800 text-gray-100">
                        <div class="flex items-center mb-4 text-red-500">
                            <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h2 class="text-xl font-bold">Error al realizar la reserva</h2>
                        </div>
                        
                        <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-4 mb-6">
                            <ul class="list-disc list-inside text-red-200 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')" class="bg-gray-700 hover:bg-gray-600 border-gray-600 text-white px-6">
                                Entendido
                            </x-secondary-button>
                        </div>
                    </div>
                </x-modal>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($rooms as $room)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition duration-300 flex flex-col">
                        <div class="h-48 w-full bg-gray-700 relative overflow-hidden group">
                            @if($room->image_path)
                                <img src="{{ Storage::url($room->image_path) }}" alt="{{ $room->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-500">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $room->status === 'active' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $room->status === 'active' ? 'Disponible' : 'Mantenimiento' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $room->name }}</h3>
                                    <button @click="openViewModal({{ $room }})" class="ml-2 text-gray-400 hover:text-blue-400 transition" title="Ver detalles">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 bg-gray-700/30 px-2 py-1 rounded">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    {{ $room->capacity }}
                                </div>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 truncate">{{ $room->location ?? 'Ubicación no definida' }}</p>
                            
                            <div class="mt-auto grid grid-cols-2 gap-3">
                                <button @click="openAgendaModal({{ $room }})"
                                    class="flex items-center justify-center w-full px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition text-sm font-medium border border-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Agenda
                                </button>

                                <button @click="openReservationModal({{ $room }})"
                                    class="flex items-center justify-center w-full px-3 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition text-sm font-medium shadow-lg shadow-blue-900/20"
                                    {{ $room->status !== 'active' ? 'disabled' : '' }}>
                                    Reservar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <x-modal name="view-room-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100">
                <h2 class="text-xl font-bold text-gray-100 mb-6 border-b border-gray-700 pb-2">Detalle de la Sala</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex flex-col items-center justify-center bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <template x-if="viewingRoom.image_path">
                            <img :src="'/storage/' + viewingRoom.image_path" class="w-full h-64 object-cover rounded-md shadow-lg">
                        </template>
                        <template x-if="!viewingRoom.image_path">
                            <div class="w-full h-64 flex items-center justify-center bg-gray-800 text-gray-500 rounded-md">
                                <span class="text-sm">Sin imagen disponible</span>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-widest">Nombre</span>
                            <span class="text-2xl font-bold text-white tracking-wider" x-text="viewingRoom.name"></span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Capacidad</span>
                                <span class="text-lg text-gray-200" x-text="viewingRoom.capacity + ' Personas'"></span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-400 uppercase tracking-widest">Estado</span>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md" 
                                      :class="viewingRoom.status === 'active' ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200'" 
                                      x-text="viewingRoom.status === 'active' ? 'DISPONIBLE' : 'MANTENIMIENTO'">
                                </span>
                            </div>
                        </div>

                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-widest">Ubicación</span>
                            <span class="text-lg text-gray-200" x-text="viewingRoom.location || 'No especificada'"></span>
                        </div>

                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-widest">Descripción</span>
                            <p class="text-sm text-gray-300 mt-1 bg-gray-900/50 p-3 rounded border border-gray-700" x-text="viewingRoom.description || 'Sin descripción'"></p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button @click="$dispatch('close')" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600 transition">Cerrar</button>
                </div>
            </div>
        </x-modal>

        <x-modal name="agenda-modal" :show="false" focusable>
            
            <div class="md:p-8 p-5 dark:bg-gray-800 bg-white rounded-t">
                
                <div class="px-4 flex items-center justify-between">
                    <span tabindex="0" class="focus:outline-none text-base font-bold dark:text-gray-100 text-gray-800 capitalize" x-text="monthNames[month] + ' ' + year"></span>
                    <div class="flex items-center">
                        <button aria-label="calendar backward" @click="prevMonth()" class="focus:text-gray-400 hover:text-gray-400 text-gray-800 dark:text-gray-100 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <polyline points="15 6 9 12 15 18" />
                            </svg>
                        </button>
                        <button aria-label="calendar forward" @click="nextMonth()" class="focus:text-gray-400 hover:text-gray-400 ml-3 text-gray-800 dark:text-gray-100 p-1"> 
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <polyline points="9 6 15 12 9 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-12 overflow-x-auto">
                    <div class="w-full grid grid-cols-7 gap-y-6">
                        <template x-for="day in ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']">
                            <div class="flex justify-center">
                                <p class="text-base font-medium text-center text-gray-800 dark:text-gray-100" x-text="day"></p>
                            </div>
                        </template>

                        <template x-for="blank in blankDays">
                            <div></div>
                        </template>

                        <template x-for="date in no_of_days">
                            <div class="flex justify-center">
                                <div @click="selectDate(date)" 
                                     class="w-8 h-8 flex items-center justify-center rounded-full cursor-pointer transition-colors duration-200"
                                     :class="{
                                        'bg-indigo-700 text-white shadow-lg': isSelected(date),
                                        'text-gray-500 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700': !isSelected(date),
                                        'border border-red-500': hasEvents(date)
                                     }">
                                    <p class="text-base font-medium" x-text="date"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-700 pt-4" x-show="selectedDate">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">
                        Ocupación: <span x-text="selectedDate + ' de ' + monthNames[month]" class="text-white normal-case"></span>
                    </h4>
                    
                    <template x-if="dayEvents.length > 0">
                        <div class="space-y-2">
                            <template x-for="evt in dayEvents">
                                <div class="flex justify-between items-center bg-gray-900/50 p-2 rounded text-sm border border-gray-600">
                                    <span class="text-white" x-text="evt.time"></span>
                                    <span class="text-xs px-2 py-0.5 rounded bg-red-900 text-red-200">OCUPADO</span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="dayEvents.length === 0">
                        <div class="p-3 bg-green-900/20 border border-green-900 rounded text-center text-green-400 text-sm">
                            ¡Todo el día disponible!
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="$dispatch('close')" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-600 transition">Cerrar</button>
                </div>
            </div>
        </x-modal>

        <x-modal name="reservation-modal" :show="false" focusable>
            <div class="p-6 bg-gray-800 text-gray-100">
                <h2 class="text-lg font-bold mb-4">Reservar: <span x-text="selectedRoom.name" class="text-blue-400"></span></h2>
                
                <form method="POST" action="{{ route('reservations.store') }}">
                    @csrf
                    <input type="hidden" name="meeting_room_id" :value="selectedRoom.id">

                    <div class="grid gap-4">
                        <div>
                            <x-input-label class="text-gray-300">Inicio</x-input-label>
                            <input type="datetime-local" name="start_time" required class="w-full bg-gray-900 border-gray-700 rounded text-white">
                        </div>
                        <div>
                            <x-input-label class="text-gray-300">Fin</x-input-label>
                            <input type="datetime-local" name="end_time" required class="w-full bg-gray-900 border-gray-700 rounded text-white">
                        </div>
                        <div>
                            <x-input-label class="text-gray-300">Propósito</x-input-label>
                            <input type="text" name="purpose" required class="w-full bg-gray-900 border-gray-700 rounded text-white" placeholder="Ej: Reunión Cliente">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label class="text-gray-300">Asistentes</x-input-label>
                                <input type="number" name="attendees" min="1" required class="w-full bg-gray-900 border-gray-700 rounded text-white">
                            </div>
                            <div>
                                <x-input-label class="text-gray-300">Recursos</x-input-label>
                                <input type="text" name="resources" class="w-full bg-gray-900 border-gray-700 rounded text-white" placeholder="Opcional">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="$dispatch('close')" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500">Confirmar</button>
                    </div>
                </form>
            </div>
        </x-modal>

    </div>

    <script>
        function calendarApp() {
            return {
                month: '',
                year: '',
                no_of_days: [],
                blankDays: [],
                days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                
                selectedRoom: {},
                viewingRoom: {}, // Variable para el modal de detalles
                events: [],
                selectedDate: null,
                dayEvents: [],

                init() {
                    let today = new Date();
                    this.month = today.getMonth();
                    this.year = today.getFullYear();
                    this.getNoOfDays();
                },

                // Lógica nueva para ver detalles
                openViewModal(room) {
                    this.viewingRoom = room;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'view-room-modal' }));
                },

                async openAgendaModal(room) {
                    this.selectedRoom = room;
                    this.events = [];
                    this.selectedDate = null;
                    this.dayEvents = [];
                    
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'agenda-modal' }));

                    try {
                        let res = await fetch(`/rooms/${room.id}/availability`);
                        let data = await res.json();
                        
                        this.events = data.map(e => ({
                            date: e.day,
                            month: e.month,
                            year: e.year,
                            time: e.start_time + ' - ' + e.end_time
                        }));
                    } catch (err) { console.error(err); }
                },

                openReservationModal(room) {
                    this.selectedRoom = room;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reservation-modal' }));
                },

                hasEvents(date) {
                    return this.events.some(e => 
                        e.date === date && 
                        e.month === this.month && 
                        e.year === this.year
                    );
                },

                selectDate(date) {
                    this.selectedDate = date;
                    this.dayEvents = this.events.filter(e => 
                        e.date === date && 
                        e.month === this.month && 
                        e.year === this.year
                    );
                },

                isSelected(date) {
                    return this.selectedDate === date;
                },

                isToday(date) {
                    const today = new Date();
                    return this.year === today.getFullYear() && 
                           this.month === today.getMonth() && 
                           date === today.getDate();
                },

                getNoOfDays() {
                    let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    let dayOfWeek = new Date(this.year, this.month, 1).getDay(); 
                    
                    let blankDaysArray = [];
                    let adjust = (dayOfWeek === 0) ? 6 : dayOfWeek - 1; 

                    for (var i = 1; i <= adjust; i++) {
                        blankDaysArray.push(i);
                    }

                    let daysArray = [];
                    for (var i = 1; i <= daysInMonth; i++) {
                        daysArray.push(i);
                    }

                    this.blankDays = blankDaysArray;
                    this.no_of_days = daysArray;
                },

                prevMonth() {
                    if (this.month == 0) {
                        this.month = 11;
                        this.year--;
                    } else {
                        this.month--;
                    }
                    this.selectedDate = null;
                    this.getNoOfDays();
                },

                nextMonth() {
                    if (this.month == 11) {
                        this.month = 0;
                        this.year++;
                    } else {
                        this.month++;
                    }
                    this.selectedDate = null;
                    this.getNoOfDays();
                }
            }
        }
    </script>
</x-app-layout>