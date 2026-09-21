<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Panel de Vehículos') }}
        </h2>
    </x-slot>

    <div
        class="py-12"
        x-data="{
            openViewModal: false,
            viewingVehicle: null,

            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),

            openVehicleModal(vehicle) {
                this.viewingVehicle = vehicle;

                this.currentMonth = new Date().getMonth();
                this.currentYear = new Date().getFullYear();

                this.openViewModal = true;
            },

            previousMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
            },

            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
            },

            monthName() {
                return new Intl.DateTimeFormat('es-CL', {
                    month: 'long',
                    year: 'numeric'
                }).format(
                    new Date(this.currentYear, this.currentMonth, 1)
                );
            },

            formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            },

            calendarWeeks() {
                const firstDay = new Date(
                    this.currentYear,
                    this.currentMonth,
                    1
                );

                const lastDay = new Date(
                    this.currentYear,
                    this.currentMonth + 1,
                    0
                );

                let startDay = firstDay.getDay();

                // Lunes = 0 ... Domingo = 6
                startDay = startDay === 0 ? 6 : startDay - 1;

                const calendarStart = new Date(
                    this.currentYear,
                    this.currentMonth,
                    1 - startDay
                );

                const weeks = [];

                for (let week = 0; week < 6; week++) {

                    const days = [];

                    for (let day = 0; day < 7; day++) {

                        const date = new Date(calendarStart);

                        date.setDate(
                            calendarStart.getDate() +
                            (week * 7) +
                            day
                        );

                        days.push({
                            date: this.formatDate(date),
                            day: date.getDate(),
                            currentMonth:
                                date.getMonth() === this.currentMonth
                        });
                    }

                    weeks.push(days);
                }

                // Quitamos la sexta semana si está completamente fuera
                const lastWeek = weeks[weeks.length - 1];

                if (lastWeek.every(day => !day.currentMonth)) {
                    weeks.pop();
                }

                return weeks;
            },

            reservationSegments(week) {

                if (!this.viewingVehicle?.reservations) {
                    return [];
                }

                const weekStart = week[0].date;
                const weekEnd = week[6].date;

                const reservations = this.viewingVehicle.reservations
                    .filter(reservation => {
                        return reservation.start_date <= weekEnd &&
                            reservation.end_date >= weekStart;
                    })
                    .map(reservation => {

                        let startIndex = week.findIndex(
                            day => day.date >= reservation.start_date
                        );

                        let endIndex = -1;

                        for (let i = week.length - 1; i >= 0; i--) {
                            if (week[i].date <= reservation.end_date) {
                                endIndex = i;
                                break;
                            }
                        }

                        if (startIndex === -1) {
                            startIndex = 0;
                        }

                        if (endIndex === -1) {
                            endIndex = 6;
                        }

                        return {
                            ...reservation,
                            startColumn: startIndex + 1,
                            endColumn: endIndex + 1
                        };
                    })
                    .sort((a, b) => {
                        if (a.startColumn !== b.startColumn) {
                            return a.startColumn - b.startColumn;
                        }

                        return a.endColumn - b.endColumn;
                    });


                // Líneas ocupadas.
                // Cada posición representa hasta qué columna
                // está ocupada esa línea.
                const laneEnds = [];


                return reservations.map(reservation => {

                    let lane = 0;

                    // Buscar la primera línea donde la reserva
                    // no se solape con otra.
                    while (
                        laneEnds[lane] !== undefined &&
                        reservation.startColumn <= laneEnds[lane]
                    ) {
                        lane++;
                    }

                    // Esta línea queda ocupada hasta esta columna.
                    laneEnds[lane] = reservation.endColumn;

                    return {
                        ...reservation,
                        lane: lane
                    };

                });
            },
            weekHeight(week) {
                const reservations = this.reservationSegments(week);
                if (!reservations.length) {
                    return 88;
                }
                const maxLane = Math.max(
                    ...reservations.map(reservation => reservation.lane)
                );
                // 30px para la fecha +
                // 28px por cada línea de reserva +
                // margen inferior.
                return Math.max(
                    88,
                    30 + ((maxLane + 1) * 28) + 10
                );
            },
        }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 mb-12 border border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 divide-x-0 md:divide-x divide-gray-700">
                    <div class="text-center px-4">
                        @if(Auth::user()->role !== 'supervisor')
                            <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Disponibles</span>
                                <span class="block text-4xl font-black text-emerald-500 transition-transform duration-200">{{ $countDisponible }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'available']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Disponibles</span>
                                <span class="block text-4xl font-black text-emerald-500 group-hover:scale-110 transition-transform duration-200">{{ $countDisponible }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role !== 'supervisor')
                             <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Reservados</span>
                                <span class="block text-4xl font-black text-blue-500 transition-transform duration-200">{{ $countAsignado }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'occupied']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Reservados</span>
                                <span class="block text-4xl font-black text-blue-500 group-hover:scale-110 transition-transform duration-200">{{ $countAsignado }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role !== 'supervisor')
                             <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Fuera de Servicio</span>
                                <span class="block text-4xl font-black text-red-500 transition-transform duration-200">{{ $countFueraDeServicio }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'out_of_service']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Fuera de Servicio</span>
                                <span class="block text-4xl font-black text-red-500 group-hover:scale-110 transition-transform duration-200">{{ $countFueraDeServicio }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="text-center px-4 border-l border-gray-700">
                        @if(Auth::user()->role !== 'supervisor')
                            <div class="block group rounded-lg p-2 transition duration-200 cursor-default">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Mantenimiento</span>
                                <span class="block text-4xl font-black text-amber-500 transition-transform duration-200">{{ $countMantenimiento }}</span>
                            </div>
                        @else
                            <a href="{{ route('vehicles.index', ['status' => 'maintenance']) }}" class="block group hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg p-2 transition duration-200">
                                <span class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 group-hover:text-gray-700 dark:group-hover:text-gray-300">Mantenimiento</span>
                                <span class="block text-4xl font-black text-amber-500 group-hover:scale-110 transition-transform duration-200">{{ $countMantenimiento }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($vehicles as $vehicle)
                    @php 
                        // USAMOS EL ESTADO DIRECTO DE LA BD PARA EVITAR ERRORES
                        $status = $vehicle->status; 
                    @endphp
                    <div
                        @click="openVehicleModal({
                            id: {{ $vehicle->id }},
                            plate: @js($vehicle->plate),

                            reservations: @js(
                                $vehicle->reservations->map(function ($reservation) {
                                    return [
                                        'id' => $reservation->id,
                                        'start_date' => $reservation->start_date?->format('Y-m-d'),
                                        'end_date' => $reservation->end_date?->format('Y-m-d'),
                                        'start_time' => $reservation->start_date?->format('H:i'),
                                        'end_time' => $reservation->end_date?->format('H:i'),
                                        'status' => $reservation->status,
                                        'user' => $reservation->user
                                            ? trim($reservation->user->name . ' ' . $reservation->user->last_name)
                                            : 'Sin usuario',
                                        'conductor' => $reservation->conductor_name ?? 'Sin conductor',
                                    ];
                                })->values()
                            )
                        })"
                        class="bg-gray-800 border border-gray-700 rounded-3xl overflow-hidden
                            hover:ring-2 hover:ring-indigo-500 transition-all duration-300
                            group shadow-2xl cursor-pointer"
                    >
                        <div class="relative h-48 bg-gray-900 overflow-hidden">
                            @if($vehicle->image_path)
                                <img src="{{ Storage::url($vehicle->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-700 font-black text-2xl uppercase italic">Sin Foto</div>
                            @endif
                            
                            <div class="absolute top-4 left-4 flex flex-col items-start gap-1">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black tracking-widest border
                                    {{ $status === 'available' ? 'text-green-400 bg-green-900/30 border-green-500/50' : '' }}
                                    {{ $status === 'out_of_service' ? 'text-red-400 bg-red-900/30 border-red-500/50' : '' }}
                                    {{ $status === 'maintenance' ? 'text-yellow-400 bg-yellow-900/30 border-yellow-500/50' : '' }}
                                    {{ $status === 'occupied' ? 'text-blue-400 bg-blue-900/30 border-blue-500/50' : '' }}">
                                    
                                    @switch($status)
                                        @case('available') DISPONIBLE @break
                                        @case('out_of_service') FUERA DE SERVICIO @break
                                        @case('maintenance') MANTENCIÓN @break
                                        @case('occupied') RESERVADO @break
                                        @default {{ strtoupper($status) }}
                                    @endswitch
                                </span>
                                
                                @if($status === 'occupied' && $vehicle->active_reservation)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold tracking-wider text-blue-200 bg-blue-900/80 border border-blue-500/30 backdrop-blur-sm">
                                        {{ Str::limit($vehicle->active_reservation->user->name, 15) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-2xl font-black text-white tracking-tighter uppercase">{{ $vehicle->plate }}</h3>
                                <span class="text-[10px] font-bold text-gray-500 bg-gray-900 px-2 py-1 rounded border border-gray-700">{{ $vehicle->year }}</span>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                            
                            <div class="flex items-center justify-between pt-4 border-t border-gray-700/50">
                                <div>
                                    <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Kilometraje</span>
                                    <span class="text-sm font-mono text-gray-100">{{ number_format($vehicle->mileage, 0, '', '.') }} KM</span>
                                </div>
                                <div class="text-right mr-4">
                                     <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Eficiencia</span>
                                    @if($vehicle->average_efficiency)
                                        <span class="text-sm font-bold text-blue-400">{{ number_format($vehicle->average_efficiency, 1, ',', '.') }} km/L</span>
                                    @else
                                        <span class="text-xs text-gray-600 italic">--</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2 mt-2 border-t border-gray-700/30">
                                <div>
                                    <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Costo/Km</span>
                                    @if($vehicle->cost_per_km)
                                        <span class="text-xs font-bold text-gray-300">${{ number_format($vehicle->cost_per_km, 0, '', '.') }}</span>
                                    @else
                                        <span class="text-[10px] text-gray-600 italic">--</span>
                                    @endif
                                </div>
                                <div class="text-right mr-4">
                                    <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest">Mantención en</span>
                                    @php $dist = $vehicle->maintenance_remaining_km; @endphp
                                    @if($dist !== null)
                                        <span class="text-xs font-bold {{ $dist < 0 ? 'text-red-500' : ($dist < 1000 ? 'text-yellow-500' : 'text-emerald-400') }}">
                                            {{ number_format($dist, 0, '', '.') }} km
                                        </span>
                                    @else
                                        <span class="text-[10px] text-gray-600 italic">No Data</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-gray-900/40 rounded-3xl border-2 border-dashed border-gray-800 flex flex-col items-center">
                        <p class="text-gray-500 text-xs uppercase tracking-[0.2em]">No hay vehículos activos para mostrar</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Modal Calendario de Reservas -->
        <div
            x-show="openViewModal"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display: none;"
        >
            <!-- Fondo -->
            <div
                class="fixed inset-0 bg-black/70 backdrop-blur-sm"
                @click="openViewModal = false"
            ></div>
            <!-- Modal -->
            <div
                class="relative z-50 bg-gray-800 border border-gray-700
                    rounded-2xl shadow-2xl w-full max-w-3xl
                    overflow-hidden"
                @click.stop
            >
                <!-- Encabezado -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-white">
                            Calendario de Reservas
                        </h2>

                        <p
                            class="text-sm text-indigo-400 font-bold mt-1"
                            x-text="viewingVehicle?.plate"
                        ></p>
                    </div>
                    <button
                        @click="openViewModal = false"
                        class="p-2 rounded-lg text-gray-400
                            hover:text-white hover:bg-gray-700 transition"
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
                <!-- Calendario -->
                <div class="p-4">
                    <!-- Navegación -->
                    <div class="flex items-center justify-between mb-6">

                        <button
                            @click="previousMonth()"
                            class="p-2 rounded-lg bg-gray-700
                                hover:bg-gray-600 text-white transition"
                        >
                            <svg class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </button>
                        <h3
                            class="text-lg font-bold text-white capitalize"
                            x-text="monthName()"
                        ></h3>
                        <button
                            @click="nextMonth()"
                            class="p-2 rounded-lg bg-gray-700
                                hover:bg-gray-600 text-white transition"
                        >
                            <svg class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>
                    <!-- Encabezado de días -->
                    <div class="grid grid-cols-7 mb-2">
                        <template
                            x-for="day in ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']"
                            :key="day"
                        >
                            <div
                                class="text-center text-[10px] font-black
                                    uppercase tracking-widest text-gray-500 py-2"
                                x-text="day"
                            ></div>
                        </template>
                    </div>
                    <!-- Semanas -->
                    <div class="space-y-2">
                        <template
                            x-for="week in calendarWeeks()"
                            :key="week[0].date"
                        >
                            <div
                                class="relative grid grid-cols-7
                                    rounded-xl overflow-visible"
                                :style="`min-height: ${weekHeight(week)}px`"
                            >
                                <!-- Días -->
                                <template
                                    x-for="day in week"
                                    :key="day.date"
                                >
                                    <div
                                        class="border rounded-lg min-h-[88px]
                                        p-1.5 transition h-full"
                                        :class="{
                                            'bg-gray-900 border-gray-700':
                                                day.currentMonth,

                                            'bg-gray-900/40 border-gray-800':
                                                !day.currentMonth
                                        }"
                                    >
                                        <span
                                            class="text-sm font-bold"
                                            :class="{
                                                'text-gray-300': day.currentMonth,
                                                'text-gray-600': !day.currentMonth
                                            }"
                                            x-text="day.day"
                                        ></span>
                                    </div>
                                </template>
                                <!-- Reservas -->
                                <template
                                    x-for="reservation in reservationSegments(week)"
                                    :key="reservation.id"
                                >
                                    <div
                                        class="absolute z-20
                                            rounded-md
                                            bg-blue-600
                                            border border-blue-400
                                            shadow-md
                                            px-2 py-1
                                            overflow-hidden
                                            cursor-pointer
                                            hover:bg-blue-500
                                            transition"
                                        :style="`
                                            left: calc(
                                                ${((reservation.startColumn - 1) * (100 / 7))}%
                                                + 3px
                                            );
                                            width: calc(
                                                ${(reservation.endColumn - reservation.startColumn + 1) * (100 / 7)}%
                                                - 6px
                                            );
                                            top: ${30 + (reservation.lane * 28)}px;
                                        `"
                                    >
                                        <div
                                            class="text-[9px] font-bold
                                                text-white truncate leading-tight"
                                            x-text="reservation.user"
                                        ></div>
                                        <div
                                            class="text-[8px] text-blue-100
                                                font-medium leading-tight"
                                            x-text="
                                                reservation.start_time +
                                                ' → ' +
                                                reservation.end_time
                                            "
                                        ></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <!-- Leyenda -->
                    <div class="mt-5 flex items-center gap-2 text-xs text-gray-500">
                        <span
                            class="w-3 h-3 rounded bg-blue-600
                                border border-blue-400"
                        ></span>
                        Reserva
                    </div>
                </div>
                <!-- Footer -->
                <div class="flex justify-end px-6 py-4 border-t border-gray-700">
                    <button
                        @click="openViewModal = false"
                        class="px-6 py-2 bg-gray-700 hover:bg-gray-600
                            text-white font-bold rounded-lg
                            transition uppercase text-xs tracking-widest"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>