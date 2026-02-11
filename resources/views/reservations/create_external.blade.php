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
                    <form action="{{ route('reservations.store_external') }}" method="POST" id="reservationForm">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">
                                ¿Para quién es la reserva? <span class="text-gray-500 dark:text-gray-400 font-normal text-xs ml-2">(Opcional)</span>
                            </label>
                            <input type="text" name="external_name" 
                                class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 placeholder-gray-400 dark:placeholder-gray-500" 
                                placeholder="Ej: Cliente, Visita...">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">Sala</label>
                                <select name="meeting_room_id" class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200">
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">
                                            {{ $room->name }} (Cap: {{ $room->capacity }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">N° Personas</label>
                                <input type="number" name="attendees" min="1" value="1"
                                    class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200">
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 p-5 rounded-lg border border-gray-200 dark:border-gray-700 mb-6 shadow-sm">
                            <h4 class="text-gray-800 dark:text-gray-200 font-bold mb-3 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Horario de la Reserva
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-gray-600 dark:text-gray-400 text-xs uppercase font-bold mb-1">Inicio</label>
                                    <input type="datetime-local" name="start_time"
                                        class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200" required>
                                </div>
                                <div>
                                    <label class="block text-gray-600 dark:text-gray-400 text-xs uppercase font-bold mb-1">Término</label>
                                    <input type="datetime-local" name="end_time"
                                        class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">Motivo / Propósito</label>
                            <input type="text" name="purpose" 
                                class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500" 
                                placeholder="Ej: Reunión trimestral de ventas" required>
                        </div>

                        <div class="mb-8">
                            <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2 text-sm">Recursos Adicionales <span class="text-gray-500 dark:text-gray-400 font-normal">(Opcional)</span></label>
                            <textarea name="resources" rows="2" 
                                class="w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500" 
                                placeholder="Ej: Proyector, Servicio de café..."></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('reservations.catalog') }}" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-bold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                    confirmButtonColor: '#4f46e5',
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
                    confirmButtonColor: '#4f46e5'
                });
            });
        </script>
    @endif
</x-app-layout>