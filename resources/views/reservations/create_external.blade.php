<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Reservas') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">Reserva Manual</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Agendar reserva de sala a nombre de terceros.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-600 p-2 rounded-full shadow-sm border border-gray-200 dark:border-gray-500">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <div class="p-6 md:p-8 bg-white dark:bg-gray-800">
                    <form action="{{ route('reservations.store_external') }}" method="POST" id="reservationForm"
                        x-data="{
                            selectedRoom: '',
                            roomLabel: '-- Selecciona una sala --',
                            openRoom: false,
                            rooms: {{ $rooms->map(fn($r) => ['id' => $r->id, 'label' => $r->name . ' (Cap: ' . $r->capacity . ')'])->toJson() }}
                        }">
                        @csrf

                        {{-- ¿Para quién? --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">
                                ¿Para quién es la reserva? <span class="text-gray-500 dark:text-gray-400 font-normal text-xs ml-2">(Opcional)</span>
                            </label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <input type="text" name="external_name"
                                    class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none"
                                    placeholder="Ej: Cliente, Visita...">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Sala (styled dropdown) --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">Sala</label>
                                <div class="relative">
                                    <input type="hidden" name="meeting_room_id" x-model="selectedRoom">
                                    <button type="button"
                                        @click="openRoom = !openRoom"
                                        @click.away="openRoom = false"
                                        class="w-full flex items-center justify-between border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 hover:border-slate-600 transition-colors text-left focus:outline-none focus:border-blue-500">
                                        <span x-text="roomLabel" class="text-slate-100 text-sm"></span>
                                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200"
                                            :class="openRoom ? 'rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <ul x-show="openRoom"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute z-50 w-full mt-1 bg-[#1e293b] shadow-lg max-h-60 rounded-lg py-1 text-sm ring-1 ring-slate-700 overflow-auto"
                                        style="display:none;">
                                        <template x-for="room in rooms" :key="room.id">
                                            <li @click="selectedRoom = room.id; roomLabel = room.label; openRoom = false"
                                                class="text-gray-200 cursor-pointer select-none py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors"
                                                :class="selectedRoom == room.id ? 'bg-blue-600/20 text-blue-400' : ''">
                                                <span x-text="room.label"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            {{-- N° Personas --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">N° Personas</label>
                                <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                    <input type="number" name="attendees" min="1" value="1"
                                        class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Horario --}}
                        <div class="bg-gray-50 dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-700 mb-6 shadow-sm">
                            <h4 class="text-gray-800 dark:text-gray-200 font-bold mb-3 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Horario de la Reserva
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-gray-600 dark:text-gray-400 text-xs uppercase font-bold mb-1">Inicio</label>
                                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                        <input type="datetime-local" name="start_time"
                                            class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-gray-600 dark:text-gray-400 text-xs uppercase font-bold mb-1">Término</label>
                                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                        <input type="datetime-local" name="end_time"
                                            class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Número de contacto --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">
                                Número de contacto <span class="text-red-400">*</span>
                            </label>

                            <div class="flex w-full">
                                <div class="flex items-center gap-2 px-4 bg-gray-800 border border-gray-700 rounded-lg text-gray-300">
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
                                    class="w-full ml-2 px-4 bg-[#1e293b] border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-0"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 9)"
                                >
                            </div>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Ingresa los 9 dígitos del número celular.
                            </p>
                        </div>

                        {{-- Propósito --}}
                        <div class="mb-6">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">Motivo / Propósito</label>
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <input type="text" name="purpose"
                                    class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none"
                                    placeholder="Ej: Reunión trimestral de ventas" required>
                            </div>
                        </div>

                        {{-- Recursos --}}
                        <div class="mb-8">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">Recursos Adicionales <span class="text-gray-500 dark:text-gray-400 font-normal">(Opcional)</span></label>
                            <div class="flex items-start border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <textarea name="resources" rows="2"
                                    class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none resize-none"
                                    placeholder="Ej: Proyector, Servicio de café..."></textarea>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('reservations.catalog') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-rose-600 text-white text-xs font-semibold rounded-lg hover:bg-rose-500 shadow-md shadow-rose-500/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-5 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-md shadow-blue-500/20 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-[#1e293b] transition-all hover:-translate-y-0.5 cursor-pointer">
                                Confirmar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('error_modal'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Atención',
                    text: "{{ session('error_modal') }}",
                    confirmButtonColor: '#2563eb',
                    background: '#1f2937',
                    color: '#fff',
                    confirmButtonText: 'Entendido'
                });
            });
        </script>
    @endif
    
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    background: '#1f2937',
                    color: '#fff',
                    timer: 2000
                });
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let msgs = "";
                @foreach ($errors->all() as $error)
                    msgs += "{{ $error }}\n";
                @endforeach
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan datos',
                    text: msgs,
                    background: '#1f2937',
                    color: '#fff',
                    confirmButtonColor: '#2563eb'
                });
            });
        </script>
    @endif
</x-app-layout>