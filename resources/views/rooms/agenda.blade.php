<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                @if (isset($selectedRoom) && $selectedRoom)
                    Agenda de {{ $selectedRoom->name }}
                @else
                    Agenda de Salas
                @endif
            </h2>                
            <a href="{{ route('reservations.catalog') }}" 
                class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm font-bold hover:bg-gray-500 transition shadow-sm">
                ← Volver al Catálogo de Salas
            </a>
        </div>
    </x-slot>

    <style>
        .date-line::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 100%;
            width: 60px;
            height: 1px;
            background-color: #e2e8f0;
            margin-left: 1rem;
            transform: translateY(-50%);
        }
        .dark .date-line::after { background-color: #374151; }
        @media (min-width: 768px) { .date-line::after { width: 200px; } }
        [x-cloak] { display: none !important; }
    </style>

    <div class="py-12" x-data="{ 
            // Control de Modales
            modalRes: false, 
            modalRoom: false, 
            modalUser: false, 
            modalCancel: false,
            modalCreate: false,
            modalDay: false,
            fullDay: false,
            guests: [],
            addGuest() {
                this.guests.push({
                    name: '',
                    email: ''
                });
            },
            removeGuest(index) {
                this.guests.splice(index, 1);
            },
            startTime: '',
            endTime: '',

            applyFullDay() {
                if          (this.fullDay && this.startTime) {
                    const date = this.startTime.substring(0, 10);
                    this.startTime = date + 'T09:00';
                    this.endTime = date + 'T18:00';
                }
            },          

            // Datos
            cancelUrl: '',
            data: {},       // Datos de la reserva actual
            roomData: {},   // Datos de la sala
            userData: {},   // Datos del usuario
            dayReservations: [],
            selectedDay: '',

            // Funciones Helpers
            openRes(reservationData) {
                this.data = reservationData;
                this.modalRes = true;
            },
            openDay(reservations, date) {
                this.dayReservations = reservations;
                this.selectedDay = date;
                this.modalDay = true;
            },
            openCancel(url) {
                this.cancelUrl = url;
                this.modalCancel = true;
            },
            openRoomFromRes() {
                this.roomData = this.data.room_data;
                this.modalRes = false;
                setTimeout(() => { this.modalRoom = true; }, 100);
            },
            openUserFromRes() {
                this.userData = this.data.user_data;
                this.modalRes = false;
                setTimeout(() => { this.modalUser = true; }, 100);
            },
            backToRes() {
                this.modalRoom = false;
                this.modalUser = false;
                setTimeout(() => { this.modalRes = true; }, 100);
            }
         }">
        
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-900/40 border border-red-700 text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-900/40 border border-green-700 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-900/40 border border-red-700 text-red-200">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif          

            @if (isset($selectedRoom) && $selectedRoom && Auth::user()->role !== 'viewer')
                <div class="mb-6 flex justify-end">
                    <button 
                        type="button"
                        @click="modalCreate = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-bold hover:bg-blue-500 transition shadow-sm">
                        + Reservar esta sala
                    </button>
                </div>
            @endif
            
            @php
                $currentDate = \Carbon\Carbon::create($year, $month, 1);
                $prevDate = $currentDate->copy()->subMonth();
                $nextDate = $currentDate->copy()->addMonth();
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-8 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div>
                    <a href="{{ route('rooms.agenda') }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm">
                        Hoy
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('rooms.agenda', array_filter([
                        'month' => $prevDate->month, 
                        'year' => $prevDate->year,
                        'room_id' => $selectedRoomId ?? null,
                        ])) }}" 
                        class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" 
                            stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </a>

                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white capitalize min-w-[200px] text-center">
                        {{ $currentDate->locale('es')->monthName }} <span class="text-gray-400 font-light">{{ $year }}</span>
                    </h2>
                    <a href="{{ route('rooms.agenda', array_filter([
                        'month' => $nextDate->month, 
                        'year' => $nextDate->year,
                        'room_id' => $selectedRoomId ?? null,
                        ])) }}" 
                        class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
                
                <div x-data="{ open: false, currentYear: {{ $year }} }" class="relative">
                    <button @click="open = !open" class="flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Ir a fecha</span>
                    </button>
                    <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-3 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">
                        <form method="GET" action="{{ route('rooms.agenda') }}" class="p-4">

                            @if(isset($selectedRoomId) && $selectedRoomId)
                                <input type="hidden" name="room_id" value="{{ $selectedRoomId }}">
                            @endif

                            <div class="flex justify-between items-center mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                                <button type="button" @click="currentYear--" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-indigo-600 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400 select-none" x-text="currentYear"></span>
                                <input type="hidden" name="year" :value="currentYear">
                                <button type="button" @click="currentYear++" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400 hover:text-indigo-600 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(range(1, 12) as $m)
                                    @php $mesNombre = ucfirst(\Carbon\Carbon::create(null, $m, 1)->locale('es')->isoFormat('MMM')); @endphp
                                    <button type="submit" name="month" value="{{ $m }}" class="px-2 py-2 text-sm rounded-md transition-all duration-200 border {{ ($m == $month) ? 'bg-indigo-600 text-white border-indigo-600 shadow-md transform scale-105' : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:border-indigo-300 hover:text-indigo-600' }}">{{ $mesNombre }}</button>
                                @endforeach
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden min-h-[400px]">
                @php
                    $startOfMonth = $currentDate->copy()->startOfMonth();
                    $endOfMonth = $currentDate->copy()->endOfMonth();
                    $daysInMonth = $currentDate->daysInMonth;
                    $firstDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 lunes - 7 domingo
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-700">
                        <div class="flex flex-wrap gap-3 text-xs font-bold">
                            <span class="px-3 py-1 rounded bg-green-900/50 text-green-300 border border-green-700">
                                Disponible
                            </span>
                            <span class="px-3 py-1 rounded bg-yellow-900/50 text-yellow-300 border border-yellow-700">
                                Ocupación parcial
                            </span>
                            <span class="px-3 py-1 rounded bg-red-900/50 text-red-300 border border-red-700">
                                Jornada completa ocupada
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-7 text-center text-xs font-bold uppercase text-gray-400 border-b border-gray-700">
                        <div class="py-3">Lun</div>
                        <div class="py-3">Mar</div>
                        <div class="py-3">Mié</div>
                        <div class="py-3">Jue</div>
                        <div class="py-3">Vie</div>
                        <div class="py-3">Sáb</div>
                        <div class="py-3">Dom</div>
                    </div>

                    <div class="grid grid-cols-7">
                        {{-- Espacios vacíos antes del día 1 --}}
                        @for($i = 1; $i < $firstDayOfWeek; $i++)
                            <div class="min-h-[120px] border-r border-b border-gray-700 bg-gray-900/20"></div>
                        @endfor

                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $date = \Carbon\Carbon::create($year, $month, $day);
                                $dateKey = $date->format('Y-m-d');
                                $dayReservations = $reservations->get($dateKey, collect());

                                $hasReservations = $dayReservations->count() > 0;

                                $isFullDay = $dayReservations->contains(function ($reservation) use ($date) {
                                    $start = $reservation->start_time;
                                    $end = $reservation->end_time;

                                    return $start->format('H:i') <= '09:00'
                                     && $end->format('H:i') >= '18:00';
                                });

                                $boxClass = 'bg-green-900/20 border-green-800/40 hover:bg-green-900/40';
                                $statusText = 'Disponible';
                                $statusClass = 'text-green-300';

                                if ($isFullDay) {
                                    $boxClass = 'bg-red-900/30 border-red-800/60';
                                    $statusText = 'Ocupada';
                                    $statusClass = 'text-red-300';
                                } elseif ($hasReservations) {
                                    $boxClass = 'bg-yellow-900/30 border-yellow-800/60 hover:bg-yellow-900/40';
                                    $statusText = 'Parcial';
                                    $statusClass = 'text-yellow-300';
                                }
                            @endphp

                            <div 
                                class="min-h-[120px] p-3 border-r border-b border-gray-700 {{ $boxClass }} transition relative"
                                @if($hasReservations)
                                    @click="openDay(
                                        @js($dayReservations->map(function($reservation) {
                                            return [
                                                'start' => $reservation->start_time->format('H:i'),
                                                'end' => $reservation->end_time->format('H:i'),
                                                'purpose' => $reservation->purpose,
                                                'user' => trim(($reservation->user->name ?? '') . ' ' . ($reservation->user->last_name ?? '')),
                                                'room' => $reservation->meetingRoom->name ?? 'Sala eliminada'
                                            ];
                                        })->values()),
                                        @js($date->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY'))
                                    )"
                                    class="cursor-pointer"
                                @endif
                            >
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-lg font-bold text-white">
                                    {{ $day }}
                                </span>

                                <span class="text-[10px] font-bold uppercase {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                                @if($hasReservations)
                                    <div class="space-y-1 mb-3">
                                        @foreach($dayReservations->take(2) as $reservation)
                                            <div class="text-[11px] bg-gray-900/60 rounded px-2 py-1 text-gray-200 leading-tight">
                                                <div>
                                                    {{ $reservation->start_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}
                                                </div>

                                                <div class="text-[10px] text-blue-300 font-semibold truncate">
                                                    {{ $reservation->meetingRoom->name ?? 'Sala eliminada' }}
                                                </div>
                                            </div>
                                        @endforeach

                                        @if($dayReservations->count() > 2)
                                            <div class="text-[11px] text-gray-400">
                                + {{ $dayReservations->count() - 2 }} más
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-[11px] text-gray-400 mb-3">
                                        Sin reservas
                                    </div>
                                @endif

                                @if(!$isFullDay && isset($selectedRoom) && $selectedRoom && Auth::user()->role !== 'viewer')
                                    <button
                                        type="button"
                                        @click.stop="
                                            startTime = '{{ $dateKey }}T09:00';
                                            endTime = '{{ $dateKey }}T18:00';
                                            fullDay = false;
                                            modalCreate = true;
                                        "
                                        class="w-full mt-auto px-2 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded text-xs font-bold">
                                        Reservar
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        disabled
                                        class="w-full mt-auto px-2 py-1 bg-gray-700 text-gray-400 rounded text-xs font-bold cursor-not-allowed">
                                        No disponible
                                    </button>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>              
            </div>
        </div>

        <template x-teleport="body">
            <div x-show="modalRes" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="modalRes" 
                        x-transition.opacity 
                        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                        @click="modalRes = false">
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    
                    <div x-show="modalRes" x-transition.scale class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700 relative z-50">
                        
                        <div class="bg-green-800 px-4 py-3 border-b border-emerald-900 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-white">Detalles de Reserva</h3>
                            <span x-show="data.is_finished" class="px-2 py-1 bg-gray-800 text-white text-xs rounded uppercase font-bold tracking-wider">Finalizada</span>
                            <span x-show="!data.is_finished" class="px-2 py-1 bg-green-800 text-white text-xs rounded uppercase font-bold tracking-wider">En Curso</span>
                        </div>

                        <div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 space-y-5">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Sala</label>
                                <button @click="openRoomFromRes()" class="group flex items-center text-lg font-bold text-green-600 dark:text-green-400 hover:text-green-800 transition w-full text-left mt-1">
                                    <span x-text="data.room_data?.name"></span>
                                    <svg class="w-4 h-4 ml-2 opacity-50 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Inicio</label><p class="font-medium" x-text="data.full_start"></p></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Término</label><p class="font-medium" x-text="data.full_end"></p></div>
                            </div>
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Solicitante</label>
                                <button @click="openUserFromRes()" class="flex items-center mt-1 group p-2 -ml-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition w-full text-left">
                                    <div class="h-8 w-8 rounded-full bg-green-100 text-green-700 dark:bg-gray-600 dark:text-gray-200 flex items-center justify-center text-xs font-bold mr-3">
                                        <span x-text="data.user_data?.initials"></span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-800 dark:text-gray-200 group-hover:text-green-600 dark:group-hover:text-green-400 transition" x-text="data.user_data?.name"></p>
                                        <p class="text-xs text-gray-500" x-text="data.user_data?.email"></p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Asistentes</label><div class="flex items-center mt-1 text-gray-800 dark:text-gray-200"><svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span class="font-medium" x-text="data.attendees + ' Personas'"></span></div></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Recursos</label><div class="flex items-center mt-1 text-gray-800 dark:text-gray-200"><svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg><span class="font-medium" x-text="data.resources"></span></div></div>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Propósito</label>
                                <p class="mt-1 bg-gray-50 dark:bg-gray-900 p-3 rounded text-sm italic border-l-2 border-green-300" x-text="data.purpose"></p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="modalRes = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="modalCancel" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="modalCancel" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                        @click="modalCancel = false">
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    
                    <div x-show="modalCancel" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border-2 border-red-500 relative z-50">
                        
                        <form method="POST" :action="cancelUrl">
                            @csrf @method('PUT')
                            <div class="bg-red-600 px-4 py-3 sm:px-6 flex items-center">
                                <svg class="w-6 h-6 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <h3 class="text-lg font-bold text-white">Cancelar Reserva</h3>
                            </div>
                            <div class="px-6 py-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">¿Seguro que deseas cancelar esta reserva? Se enviará una notificación al usuario.</p>
                                <label for="reason" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Motivo (Obligatorio):</label>
                                <textarea id="reason" name="reason" rows="3" required class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Ej: Mantención urgente..."></textarea>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Confirmar</button>
                                <button type="button" @click="modalCancel = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Volver</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="modalRoom" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="modalRoom" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="backToRes()"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="modalRoom" x-transition.scale class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700 relative z-50">
                        <div class="p-6 bg-gray-800 text-gray-100">
                            <h2 class="text-xl font-bold text-gray-100 mb-6 border-b border-gray-700 pb-2">Detalle de la Sala</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="flex flex-col items-center justify-center bg-gray-900 rounded-lg p-4 border border-gray-700">
                                    <template x-if="roomData.image_url"><img :src="roomData.image_url" class="w-full h-64 object-cover rounded-md shadow-lg"></template>
                                    <template x-if="!roomData.image_url"><div class="w-full h-64 flex items-center justify-center bg-gray-800 text-gray-500 rounded-md"><span class="text-sm">Sin imagen</span></div></template>
                                </div>
                                <div class="space-y-4">
                                    <div><span class="block text-xs text-gray-400 uppercase tracking-widest">Nombre</span><span class="text-2xl font-bold text-white tracking-wider" x-text="roomData.name"></span></div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div><span class="block text-xs text-gray-400 uppercase tracking-widest">Capacidad</span><span class="text-lg text-gray-200" x-text="roomData.capacity + ' Personas'"></span></div>
                                        <div><span class="block text-xs text-gray-400 uppercase tracking-widest">Estado</span><span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-md" :class="roomData.status === 'active' ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200'" x-text="roomData.status === 'active' ? 'DISPONIBLE' : 'MANTENIMIENTO'"></span></div>
                                    </div>
                                    <div><span class="block text-xs text-gray-400 uppercase tracking-widest">Ubicación</span><span class="text-lg text-gray-200" x-text="roomData.location || 'No especificada'"></span></div>
                                    <div><span class="block text-xs text-gray-400 uppercase tracking-widest">Descripción</span><p class="text-sm text-gray-300 mt-1" x-text="roomData.description || 'Sin descripción'"></p></div>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <x-secondary-button @click="backToRes()" class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">Volver</x-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="modalUser" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="modalUser" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="backToRes()"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="modalUser" x-transition.scale class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 dark:border-gray-700 relative z-50">
                        <div class="p-6 bg-gray-800 text-gray-100">
                            <h2 class="text-xl font-bold text-gray-100 mb-6">Detalle de Usuario</h2>
                            <div class="bg-gray-900 rounded-lg p-6 border border-gray-700 shadow-md">
                                <div class="flex items-center space-x-6 mb-6 pb-6 border-b border-gray-800">
                                    <div class="flex-shrink-0">
                                        <template x-if="userData.photo_url"><img :src="userData.photo_url" class="h-24 w-24 rounded-full object-cover border-4 border-indigo-600"></template>
                                        <template x-if="!userData.photo_url"><div class="h-24 w-24 rounded-full bg-indigo-900 flex items-center justify-center text-white font-bold text-3xl border-4 border-indigo-600"><span x-text="userData.initials"></span></div></template>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-white" x-text="userData.name"></h3>
                                        <p class="text-gray-400 text-sm mb-2" x-text="userData.email"></p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200" x-text="userData.role"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                                    <div><span class="block text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">RUT:</span><span class="text-sm font-medium text-white" x-text="userData.rut"></span></div>
                                    <div><span class="block text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Teléfono:</span><span class="text-sm font-medium text-white" x-text="userData.phone"></span></div>
                                    <div><span class="block text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Dirección:</span><span class="text-sm font-medium text-white" x-text="userData.address"></span></div>
                                    <div><span class="block text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Estado:</span><span class="text-sm font-medium text-white" x-text="userData.status"></span></div>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <x-secondary-button @click="backToRes()" class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">Volver</x-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <!-- Modal: Reservas del día -->
        <template x-teleport="body">
            <div 
                x-show="modalDay" 
                x-cloak 
                class="fixed inset-0 z-[9999] overflow-y-auto"
                role="dialog"
                aria-modal="true"
            >
                <div class="flex items-center justify-center min-h-screen px-4 py-8">

                    <!-- Fondo -->
                    <div 
                        x-show="modalDay"
                        x-transition.opacity
                        class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"
                        @click="modalDay = false"
                    ></div>

                    <!-- Modal -->
                    <div
                        x-show="modalDay"
                        x-transition.scale
                        class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50"
                    >

                        <!-- Encabezado -->
                        <div class="px-6 py-4 bg-gray-800 dark:bg-gray-900 border-b border-gray-700 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    Reservas del día
                                </h3>

                                <p 
                                    class="text-sm text-gray-400 capitalize mt-1"
                                    x-text="selectedDay"
                                ></p>
                            </div>

                            <button
                                type="button"
                                @click="modalDay = false"
                                class="text-gray-400 hover:text-white transition"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path 
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <!-- Reservas -->
                        <div class="p-6 max-h-[65vh] overflow-y-auto">

                            <template x-if="dayReservations.length > 0">
                                <div class="space-y-3">

                                    <template x-for="(reservation, index) in dayReservations" :key="index">

                                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4">

                                            <!-- Horario -->
                                            <div class="flex items-center gap-2 mb-3">

                                                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        />
                                                    </svg>
                                                </div>

                                                <div>
                                                    <p 
                                                        class="text-sm font-bold text-gray-900 dark:text-white"
                                                        x-text="reservation.start + ' - ' + reservation.end"
                                                    ></p>

                                                    <p 
                                                        class="text-xs text-gray-500 dark:text-gray-400"
                                                        x-text="reservation.room"
                                                    ></p>
                                                </div>

                                            </div>

                                            <!-- Asunto -->
                                            <div class="mb-3">
                                                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                    Asunto
                                                </span>

                                                <p 
                                                    class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200"
                                                    x-text="reservation.purpose || 'Sin asunto'"
                                                ></p>
                                            </div>

                                            <!-- Reservado por -->
                                            <div class="flex items-center gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">

                                                <div class="w-7 h-7 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                        />
                                                    </svg>
                                                </div>

                                                <div>
                                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Reservado por
                                                    </span>

                                                    <p 
                                                        class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                                        x-text="reservation.user"
                                                    ></p>
                                                </div>

                                            </div>

                                        </div>

                                    </template>

                                </div>
                            </template>

                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-end border-t border-gray-200 dark:border-gray-700">

                            <button
                                type="button"
                                @click="modalDay = false"
                                class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-md text-sm font-bold transition"
                            >
                                Cerrar
                            </button>

                        </div>

                    </div>

                </div>
            </div>
        </template>
        @if(isset($selectedRoom) && $selectedRoom)
        <template x-teleport="body">
            <div x-show="modalCreate" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/70" @click="modalCreate = false"></div>

                    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md border border-gray-700 overflow-hidden">
                        <form method="POST" action="{{ route('reservations.store') }}">
                            @csrf

                            <input type="hidden" name="meeting_room_id" value="{{ $selectedRoom->id }}">

                            <div class="px-6 py-4 border-b border-gray-700">
                                <h3 class="text-lg font-bold text-gray-100">
                            Reservar {{ $selectedRoom->name }}
                                </h3>
                            </div>

                            <div class="p-5 space-y-3">
                                <label class="flex items-center gap-2 text-gray-200">
                                    <input type="checkbox" x-model="fullDay" @change="applyFullDay()" class="rounded border-gray-600">
                                    Jornada completa
                                </label>

                                <div>
                                    <label class="block text-sm font-bold text-gray-300 mb-1">Inicio</label>
                                    <input 
                                        type="datetime-local" 
                                        name="start_time"
                                        x-model="startTime"
                                        @change="applyFullDay()"
                                        :readonly="fullDay" 
                                        required
                                        class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-300 mb-1">Término</label>
                                    <input 
                                        type="datetime-local" 
                                        name="end_time"
                                        x-model="endTime"
                                        :readonly="fullDay"
                                        required
                                        class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-300 mb-1">Propósito</label>
                                    <input 
                                        type="text" 
                                        name="purpose" 
                                        required
                                        placeholder="Ej: Reunión de equipo"
                                        class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-300 mb-1">Asistentes</label>
                                    <input 
                                        type="number" 
                                        name="attendees" 
                                        min="1" 
                                        value="1"
                                        required
                                        class="w-full bg-gray-900 border-gray-700 rounded text-white">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-300 mb-1">
                                        Número de contacto
                                    </label>

                                    <div class="flex w-full">
                                        <div class="flex items-center gap-2 px-4 bg-gray-800 border border-gray-700 rounded-l text-gray-300">
                                            <span class="text-sm font-semibold">CL</span>
                                            <span class="text-sm">+56</span>
                                        </div>

                                        <input
                                            type="tel"
                                            name="cellphone"
                                            inputmode="numeric"
                                            maxlength="9"
                                            minlength="9"
                                            pattern="9[0-9]{8}"
                                            placeholder="912345678"
                                            required
                                            class="w-full ml-2 px-4 bg-gray-900 border-gray-700 rounded-lg text-white"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0, 9)"
                                        >
                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Ingresa los 9 dígitos del número celular.
                                    </p>
                                </div>

                                <div class="bg-gray-900/70 border border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-300">
                                                Invitados a la reunión
                                            </label>
                                            <p class="text-xs text-gray-500">
                                                Opcional. Se les notificará por correo cuando la reserva sea aprobada.
                                            </p>
                                        </div>

                                        <button type="button"
                                            @click="addGuest()"
                                            class="px-3 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-lg transition">
                                            + Agregar invitado
                                        </button>
                                    </div>

                                    <div x-show="guests.length === 0" class="text-sm text-gray-500 text-center py-4">
                                        No hay invitados agregados.
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(guest, index) in guests" :key="index">
                                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-gray-800 border border-gray-700 rounded-lg p-3">
                                                <div class="md:col-span-5">
                                                    <label class="block text-xs font-semibold text-gray-400 mb-1">
                                                        Nombre
                                                    </label>
                                                    <input type="text"
                                                        :name="'guests[' + index + '][name]'"
                                                        x-model="guest.name"
                                                        placeholder="Ej: Juan Pérez"
                                                        class="w-full bg-gray-900 border-gray-700 rounded text-white text-sm">
                                                </div>

                                                <div class="md:col-span-6">
                                                    <label class="block text-xs font-semibold text-gray-400 mb-1">
                                                        Correo
                                                    </label>
                                                    <input type="email"
                                                        :name="'guests[' + index + '][email]'"
                                                        x-model="guest.email"
                                                        placeholder="correo@empresa.cl"
                                                        class="w-full bg-gray-900 border-gray-700 rounded text-white text-sm">
                                                </div>

                                                <div class="md:col-span-1 flex justify-end">
                                                    <button type="button"
                                                        @click="removeGuest(index)"
                                                        class="px-3 py-2 bg-red-600 hover:bg-red-500 text-white text-xs font-bold rounded-lg">
                                                        X
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-300 mb-1">Recursos</label>
                                    <textarea 
                                        name="resources" 
                                        rows="2"
                                        placeholder="Opcional"
                                        class="w-full bg-gray-900 border-gray-700 rounded text-white"></textarea>
                                </div>
                            </div>

                            <div class="px-5 py-4 flex justify-end gap-3 border-t border-gray-700">
                                <button 
                                    type="button" 
                                    @click="modalCreate = false"
                                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500">
                                    Cancelar
                                </button>

                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                                    Confirmar reserva
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
        @endif      
    </div>
</x-app-layout>