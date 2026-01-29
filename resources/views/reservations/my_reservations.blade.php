<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mis Reservas de Salas') }}
            </h2>
            <a href="{{ route('reservations.catalog') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 text-sm font-medium">
                ← Volver al Catálogo
            </a>
        </div>
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="py-12" x-data="{ 
        openRoomModal: false, 
        selectedRoom: { name: '', location: '', capacity: '', description: '', image: null },

        openCancelModal: false,
        cancelUrl: '',

        confirmCancel(url) {
            this.cancelUrl = url;
            this.openCancelModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if($reservations->isEmpty())
                        <div class="text-center py-10">
                            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-xl text-gray-400 font-semibold">No tienes reservas registradas.</p>
                            <a href="{{ route('reservations.catalog') }}" class="mt-4 inline-block text-blue-400 hover:text-blue-300 underline">Ir a reservar una sala</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead class="bg-gray-800 text-gray-300">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Sala</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha y Hora</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Propósito</th>
                                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">Estado</th>
                                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700 bg-gray-900 text-gray-300">
                                    @foreach($reservations as $res)
                                        <tr class="hover:bg-gray-800 transition">
                                            <td class="px-5 py-4 whitespace-nowrap">
                                                @if($res->meetingRoom)
                                                    <button type="button" 
                                                            @click.stop="selectedRoom = {
                                                                name: '{{ addslashes($res->meetingRoom->name) }}',
                                                                location: '{{ addslashes($res->meetingRoom->location ?? 'No especificada') }}',
                                                                capacity: '{{ $res->meetingRoom->capacity }}',
                                                                description: '{{ addslashes($res->meetingRoom->description ?? 'Sin descripción') }}',
                                                                image: '{{ $res->meetingRoom->image_path ? Storage::url($res->meetingRoom->image_path) : null }}'
                                                            }; openRoomModal = true;" 
                                                            class="text-left group focus:outline-none block w-full">
                                                        <div class="text-sm font-bold text-blue-400 group-hover:text-blue-300 group-hover:underline transition">
                                                            {{ $res->meetingRoom->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 group-hover:text-gray-400">
                                                            {{Str::limit($res->meetingRoom->location ?? '', 20) }}
                                                        </div>
                                                    </button>
                                                @else
                                                    <span class="text-sm font-bold text-red-400 italic">Sala eliminada</span>
                                                @endif
                                            </td>

                                            <td class="px-5 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-200 capitalize">
                                                    {{ \Carbon\Carbon::parse($res->start_time)->locale('es')->translatedFormat('l d \d\e F, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-400 font-mono mt-1">
                                                    {{ \Carbon\Carbon::parse($res->start_time)->format('H:i') }} - 
                                                    {{ \Carbon\Carbon::parse($res->end_time)->format('H:i') }}
                                                </div>
                                            </td>

                                            <td class="px-5 py-4">
                                                <div class="text-sm italic">"{{ $res->purpose }}"</div>
                                                <div class="flex gap-2 mt-1">
                                                    <span class="text-xs bg-gray-700 px-2 py-0.5 rounded border border-gray-600">👥 {{ $res->attendees }}</span>
                                                    @if($res->resources)
                                                        <span class="text-xs bg-indigo-900/50 text-indigo-300 px-2 py-0.5 rounded border border-indigo-800" title="{{ $res->resources }}">🛠️ Recursos</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-5 py-4 text-center">
                                                @php
                                                    // Calculamos si ya pasó
                                                    $isPast = \Carbon\Carbon::parse($res->end_time)->isPast();
                                                    
                                                    $colors = [
                                                        'pending' => 'bg-yellow-900 text-yellow-200 border-yellow-700',
                                                        'approved' => 'bg-green-900 text-green-200 border-green-700',
                                                        'rejected' => 'bg-red-900 text-red-200 border-red-700',
                                                        'cancelled' => 'bg-gray-700 text-gray-400 border-gray-600',
                                                    ];
                                                    
                                                    $labels = [
                                                        'pending' => 'PENDIENTE',
                                                        'approved' => 'APROBADA',
                                                        'rejected' => 'RECHAZADA',
                                                        'cancelled' => 'CANCELADA',
                                                    ];
                                                    
                                                    // Lógica para elegir el color final
                                                    $badgeColor = $colors[$res->status] ?? 'bg-gray-700';
                                                    $badgeLabel = $labels[$res->status] ?? strtoupper($res->status);
                                                    
                                                    // SOBRESCRIBIR SI ES PASADO Y ESTABA APROBADA
                                                    if ($isPast && $res->status === 'approved') {
                                                        $badgeColor = 'bg-blue-900 text-blue-200 border-blue-700';
                                                        $badgeLabel = 'FINALIZADA';
                                                    }
                                                @endphp
                                                
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $badgeColor }}">
                                                    {{ $badgeLabel }}
                                                </span>
                                            </td>

                                            <td class="px-5 py-4 text-right">
                                                @if($res->status === 'pending' || ($res->status === 'approved' && !$isPast))
                                                    <button type="button" 
                                                            @click="confirmCancel('{{ route('reservations.cancel', $res->id) }}')"
                                                            class="text-red-400 hover:text-red-300 text-sm font-medium hover:underline transition flex items-center justify-end ml-auto">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Cancelar
                                                    </button>
                                                @else
                                                    <span class="text-gray-600 text-xs">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <template x-teleport="body">
            <div x-show="openRoomModal" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="openRoomModal" 
                         x-transition.opacity
                         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                         @click="openRoomModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div x-show="openRoomModal" 
                         x-transition.scale
                         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200 dark:border-gray-700 relative z-50">
                        
                        <div class="bg-gray-800 px-4 py-3 sm:px-6 border-b border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-white">Detalles de la Sala</h3>
                            <button @click="openRoomModal = false" class="text-gray-400 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>

                        <div class="px-4 py-5 sm:p-6 text-gray-100">
                            <div class="mb-4 flex justify-center">
                                <template x-if="selectedRoom.image"><img :src="selectedRoom.image" class="w-full h-48 object-cover rounded-lg border border-gray-600 shadow-md"></template>
                                <template x-if="!selectedRoom.image"><div class="w-full h-48 bg-gray-700 rounded-lg flex flex-col items-center justify-center text-gray-500 border border-gray-600"><svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg><span>Sin imagen disponible</span></div></template>
                            </div>
                            <div class="space-y-4">
                                <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nombre</label><p class="text-lg font-bold text-white" x-text="selectedRoom.name"></p></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Capacidad</label><p class="font-medium text-gray-200" x-text="selectedRoom.capacity + ' Personas'"></p></div>
                                    <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Ubicación</label><p class="font-medium text-gray-200" x-text="selectedRoom.location"></p></div>
                                </div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Descripción</label><p class="mt-1 p-3 bg-gray-700/50 rounded-lg text-sm text-gray-300 italic border-l-2 border-blue-500" x-text="selectedRoom.description"></p></div>
                            </div>
                        </div>

                        <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" @click="openRoomModal = false">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="openCancelModal" x-cloak class="fixed inset-0 z-[9999] overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div x-show="openCancelModal" 
                         x-transition.opacity 
                         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                         @click="openCancelModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div x-show="openCancelModal" 
                         x-transition.scale
                         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border-2 border-red-500 relative z-50">
                        
                        <div class="bg-red-600 px-4 py-3 sm:px-6 flex items-center">
                            <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <h3 class="text-lg font-bold text-white">Cancelar Reserva</h3>
                        </div>

                        <div class="px-6 py-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <p class="text-base text-gray-600 dark:text-gray-300">
                                ¿Estás completamente seguro de que deseas cancelar esta reserva? Esta acción liberará la sala para otros usuarios y no se puede deshacer.
                            </p>
                        </div>

                        <div class="bg-gray-5 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <form :action="cancelUrl" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Sí, Cancelar Reserva
                                </button>
                            </form>
                            <button type="button" @click="openCancelModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                No, Volver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>