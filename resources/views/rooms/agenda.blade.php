<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Agenda de Salas') }}
            </h2>
            <a href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm font-bold hover:bg-gray-500 transition shadow-sm">
                ← Volver al Panel
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

            // Datos
            cancelUrl: '',
            data: {},       // Datos de la reserva actual
            roomData: {},   // Datos de la sala
            userData: {},   // Datos del usuario

            // Funciones Helpers
            openRes(reservationData) {
                this.data = reservationData;
                this.modalRes = true;
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
                    <a href="{{ route('rooms.agenda', ['month' => $prevDate->month, 'year' => $prevDate->year]) }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></a>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white capitalize min-w-[200px] text-center">
                        {{ $currentDate->locale('es')->monthName }} <span class="text-gray-400 font-light">{{ $year }}</span>
                    </h2>
                    <a href="{{ route('rooms.agenda', ['month' => $nextDate->month, 'year' => $nextDate->year]) }}" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
                </div>
                
                <div x-data="{ open: false, currentYear: {{ $year }} }" class="relative">
                    <button @click="open = !open" class="flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Ir a fecha</span>
                    </button>
                    <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-3 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">
                        <form method="GET" action="{{ route('rooms.agenda') }}" class="p-4">
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
                @if($reservations->isEmpty())
                    <div class="flex flex-col items-center justify-center h-96 text-gray-400 dark:text-gray-500">
                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-lg font-medium">No hay reservas aprobadas.</p>
                    </div>
                @else
                    <div class="p-6 md:p-8">
                        @foreach($reservations as $dateString => $dayReservations)
                            @php $carbonDate = \Carbon\Carbon::parse($dateString); @endphp
                            <div class="mb-10 last:mb-0 relative">
                                <div class="flex items-center mb-6">
                                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 capitalize mr-4 date-line relative">
                                        {{ $carbonDate->locale('es')->translatedFormat('l j') }}
                                    </span>
                                </div>
                                <div class="space-y-4 pl-2 md:pl-4">
                                    @foreach($dayReservations as $reservation)
                                        @php 
                                            // == ZONA SEGURA: PROTECCIÓN CONTRA ELIMINADOS ==
                                            $isFinished = $reservation->end_time->isPast(); 
                                            $cancelUrl = route('room-reservations.cancel_admin', $reservation->id);
                                            
                                            $jsData = [
                                                'start' => $reservation->start_time->format('H:i'),
                                                'end' => $reservation->end_time->format('H:i'),
                                                'full_start' => $reservation->start_time->format('d/m/Y H:i'),
                                                'full_end' => $reservation->end_time->format('d/m/Y H:i'),
                                                'purpose' => $reservation->purpose,
                                                'resources' => $reservation->resources ?? 'Ninguno',
                                                'attendees' => $reservation->attendees ?? 0,
                                                'is_finished' => $isFinished,
                                                'room_data' => [
                                                    // Usamos '?->' para evitar error si la sala fue borrada
                                                    'name' => $reservation->meetingRoom?->name ?? 'Sala Eliminada',
                                                    'capacity' => $reservation->meetingRoom?->capacity ?? 0,
                                                    'location' => $reservation->meetingRoom?->location ?? 'N/A',
                                                    'description' => $reservation->meetingRoom?->description ?? '',
                                                    'status' => $reservation->meetingRoom?->status ?? 'inactive',
                                                    'image_url' => ($reservation->meetingRoom?->image_path) ? Storage::url($reservation->meetingRoom->image_path) : null,
                                                ],
                                                'user_data' => [
                                                    // Usamos '?->' para evitar error si el usuario fue borrado
                                                    'name' => $reservation->user?->name ?? 'Usuario Desconocido',
                                                    'email' => $reservation->user?->email ?? 'sin-email@dimak.cl',
                                                    'photo_url' => ($reservation->user?->profile_photo_path) ? Storage::url($reservation->user->profile_photo_path) : null,
                                                    'initials' => substr($reservation->user?->name ?? 'X', 0, 1),
                                                    'rut' => $reservation->user?->rut ?? 'N/A',
                                                    'phone' => $reservation->user?->phone ?? 'N/A',
                                                    'address' => $reservation->user?->address ?? 'N/A',
                                                    'role' => ucfirst($reservation->user?->role ?? 'Invitado'),
                                                    'status' => $reservation->user?->status ?? 'Inactivo',
                                                    'verified' => ($reservation->user?->email_verified_at) ? 'Verificado' : 'Pendiente',
                                                    'created_at' => optional($reservation->user?->created_at)->format('d/m/Y') ?? '-',
                                                ]
                                            ];
                                        @endphp

                                        <div class="flex items-center group hover:bg-gray-50 dark:hover:bg-gray-700/30 p-3 -ml-3 rounded-lg transition-colors border-l-4 {{ $isFinished ? 'opacity-70 bg-gray-50 dark:bg-gray-800/50' : '' }}"
                                             style="border-color: {{ $isFinished ? '#9ca3af' : ['#60a5fa', '#f87171', '#fbbf24', '#34d399', '#a78bfa', '#f472b6'][$reservation->meeting_room_id % 6] }};">
                                            
                                            <div class="w-24 flex-shrink-0 pt-0.5">
                                                <div class="text-sm font-bold {{ $isFinished ? 'text-gray-500 line-through' : 'text-gray-800 dark:text-gray-200' }}">{{ $reservation->start_time->format('H:i') }}</div>
                                                <div class="text-xs text-gray-400 dark:text-gray-500">{{ $reservation->end_time->format('H:i') }}</div>
                                            </div>

                                            <div class="flex-1 min-w-0 pr-4">
                                                <div class="flex justify-between items-center">
                                                    <h4 class="text-base font-bold text-gray-900 dark:text-white leading-tight truncate">
                                                        {{ $reservation->meetingRoom?->name ?? 'Sala Eliminada' }}
                                                    </h4>
                                                    @if($isFinished)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Finalizada</span>@endif
                                                </div>
                                                <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="font-medium mr-1">Por:</span> {{ $reservation->user?->name ?? 'Usuario Desconocido' }}
                                                </div>
                                            </div>

                                            <div class="flex space-x-2">
                                                <button type="button" 
                                                        @click.stop="openRes({{ json_encode($jsData) }})"
                                                        class="p-2 rounded-full text-green-700 bg-green-200 hover:bg-green-300 dark:bg-green-800 dark:text-green-100 dark:hover:bg-green-700 transition shadow-sm" 
                                                        title="Ver detalles">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>

                                                @if(!$isFinished)
                                                    <button type="button" 
                                                            @click.stop="openCancel('{{ $cancelUrl }}')"
                                                            class="p-2 rounded-full text-red-700 bg-red-200 hover:bg-red-300 dark:bg-red-900/80 dark:text-red-100 dark:hover:bg-red-800 transition shadow-sm" 
                                                            title="Cancelar Reserva">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
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

    </div>
</x-app-layout>