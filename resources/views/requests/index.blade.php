<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mis Reservas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100"
                    x-data="{ returnUrl: '', fuelRequestId: '', fuelVehicleId: '', fuelType: '', deliveryRequestId: '', startTripUrl: '' }">




                    <div x-init="
                        const highlightId = '{{ request('highlight_id') }}';
                        if (highlightId) {
                            const el = document.getElementById('request-' + highlightId);
                            if (el) {
                                setTimeout(() => {
                                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    el.classList.add('ring-2', 'ring-blue-500', 'dark:ring-blue-400');
                                    setTimeout(() => el.classList.remove('ring-2', 'ring-blue-500', 'dark:ring-blue-400'), 5000);
                                }, 500);
                            }
                        }
                    "></div>

                    {{-- Panel de Estadísticas --}}
                    @php
                        $totalRequests = $requests->count();
                        $activeRequests = $requests->where('status', 'approved')->whereBetween('start_date', [now()->subDays(30), now()->addDays(30)])->count();
                        $pendingRequests = $requests->where('status', 'pending')->count();
                        $completedRequests = $requests->where('status', 'completed')->count();
                        $rejectedRequests = $requests->where('status', 'rejected')->count();
                    @endphp

                    @php
                        $currentTab = request('tab', 'all');
                    @endphp

                    <div class="mb-6 flex flex-wrap justify-center gap-3">
                        {{-- Total --}}
                        <a href="{{ route('requests.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}"
                           class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-gray-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ $currentTab === 'all' ? 'ring-2 ring-gray-400 bg-gray-700' : '' }}">
                            <div class="text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase truncate">Total</div>
                            <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $totalRequests }}</div>
                        </a>

                        {{-- Activas --}}
                        <a href="{{ route('requests.index', array_merge(request()->except('tab'), ['tab' => 'approved'])) }}"
                           class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-green-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ $currentTab === 'approved' ? 'ring-2 ring-green-400 bg-gray-700' : '' }}">
                            <div class="text-green-600 dark:text-green-400 text-[10px] font-bold uppercase truncate">Activas</div>
                            <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $activeRequests }}</div>
                        </a>

                        {{-- Pendientes --}}
                        <a href="{{ route('requests.index', array_merge(request()->except('tab'), ['tab' => 'pending'])) }}"
                           class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-yellow-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ $currentTab === 'pending' ? 'ring-2 ring-yellow-400 bg-gray-700' : '' }}">
                            <div class="text-yellow-600 dark:text-yellow-400 text-[10px] font-bold uppercase truncate">Pendientes</div>
                            <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $pendingRequests }}</div>
                        </a>

                        {{-- Completadas --}}
                        <a href="{{ route('requests.index', array_merge(request()->except('tab'), ['tab' => 'completed'])) }}"
                           class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-gray-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ $currentTab === 'completed' ? 'ring-2 ring-gray-400 bg-gray-700' : '' }}">
                            <div class="text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase truncate">Completadas</div>
                            <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $completedRequests }}</div>
                        </a>

                        {{-- Rechazadas --}}
                        <a href="{{ route('requests.index', array_merge(request()->except('tab'), ['tab' => 'rejected'])) }}"
                           class="flex-1 min-w-[140px] bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border-l-4 border-red-500 hover:shadow-md hover:scale-105 transition-all cursor-pointer {{ $currentTab === 'rejected' ? 'ring-2 ring-red-400 bg-gray-700' : '' }}">
                            <div class="text-red-600 dark:text-red-400 text-[10px] font-bold uppercase truncate">Rechazadas</div>
                            <div class="text-xl font-bold text-gray-800 dark:text-white">{{ $rejectedRequests }}</div>
                        </a>
                    </div>



                    {{-- Barra de Búsqueda y Filtros --}}
                    <div class="mb-6 space-y-4">
                        {{-- Búsqueda --}}
                        <form method="GET" action="{{ route('requests.index') }}" class="flex gap-3">
                            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                            @if(request('start_date'))
                                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            @endif
                            @if(request('end_date'))
                                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            @endif
                            
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Buscar por vehículo (marca, modelo o patente)..." 
                                       class="block w-full pl-10 pr-10 py-2 border border-gray-600 rounded-lg bg-gray-700 text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @if(request('search'))
                                    <a href="{{ route('requests.index', request()->except('search')) }}" 
                                       class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg transition-colors">
                                Buscar
                            </button>
                        </form>

                        {{-- Filtros de Fecha --}}
                        <form method="GET" action="{{ route('requests.index') }}" class="flex flex-wrap gap-3 items-end">
                            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-sm font-medium text-gray-300 mb-1">Desde</label>
                                <input type="date" 
                                       name="start_date" 
                                       value="{{ request('start_date') }}"
                                       class="block w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-sm font-medium text-gray-300 mb-1">Hasta</label>
                                <input type="date" 
                                       name="end_date" 
                                       value="{{ request('end_date') }}"
                                       class="block w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-medium rounded-lg transition-colors">
                                Aplicar Filtros
                            </button>
                            
                            @if(request('start_date') || request('end_date'))
                                <a href="{{ route('requests.index', request()->except(['start_date', 'end_date'])) }}" 
                                   class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white font-medium rounded-lg transition-colors">
                                    Limpiar Fechas
                                </a>
                            @endif
                        </form>
                    </div>

                    @if($requests->count() > 0)
                        {{-- Grid de Tarjetas --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($requests as $request)
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-900/50 text-yellow-300 border border-yellow-700',
                                        'approved' => 'bg-green-900/50 text-green-300 border border-green-700',
                                        'rejected' => 'bg-red-900/50 text-red-300 border border-red-700',
                                        'in_trip' => 'bg-blue-900/50 text-blue-300 border border-blue-700',
                                        'completed' => 'bg-gray-900/50 text-gray-300 border border-gray-700',
                                    ];
                                    $statusLabel = [
                                        'pending' => 'Pendiente',
                                        'approved' => 'Aprobado',
                                        'rejected' => 'Rechazado',
                                        'in_trip' => 'En Viaje',
                                        'completed' => 'Finalizado',
                                    ];
                                    
                                    // Calcular progreso para reservas activas
                                    $showProgress = $request->status === 'approved' && now()->between($request->start_date, $request->end_date);
                                    if ($showProgress) {
                                        $totalDuration = $request->start_date->diffInHours($request->end_date);
                                        $elapsed = $request->start_date->diffInHours(now());
                                        $percentage = $totalDuration > 0 ? min(100, ($elapsed / $totalDuration) * 100) : 0;
                                        $remaining = $request->end_date->diffForHumans(null, true);
                                    }

                                    // Override para Término Anticipado
                                    if ($request->status === 'completed' && $request->early_termination_reason) {
                                        $statusLabel['completed'] = 'Fin. Anticipado';
                                        $statusColors['completed'] = 'bg-orange-900/50 text-orange-300 border border-orange-700';
                                    }
                                @endphp

                                <div id="request-{{ $request->id }}" 
                                     class="bg-gray-800 rounded-lg overflow-hidden shadow-lg border border-gray-700 hover:border-gray-600 hover:shadow-xl transition-all duration-300 {{ request('highlight_id') == $request->id ? 'ring-2 ring-blue-500 border-blue-500' : '' }}">
                                    
                                    {{-- Cabecera con Foto --}}
                                    <div class="relative h-48 bg-gray-900 overflow-hidden">
                                        @if($request->vehicle && $request->vehicle->image_path)
                                            <img src="{{ Storage::url($request->vehicle->image_path) }}" 
                                                 alt="{{ $request->vehicle->brand }} {{ $request->vehicle->model }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            {{-- Icono de vehículo por defecto --}}
                                            <div class="w-full h-full flex items-center justify-center text-gray-600">
                                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        {{-- Badge de Estado --}}
                                        <div class="absolute top-3 right-3">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$request->status] }} shadow-lg">
                                                {{ $statusLabel[$request->status] }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Cuerpo de la Tarjeta --}}
                                    <div class="p-4">
                                        {{-- Información del Vehículo --}}
                                        <div class="mb-3">
                                            @if($request->vehicle)
                                                <h3 class="text-lg font-bold text-gray-100">
                                                    {{ $request->vehicle->brand }} {{ $request->vehicle->model }}
                                                </h3>
                                                <div class="flex items-center text-sm text-gray-400 mt-1">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ $request->vehicle->plate }}
                                                </div>
                                            @else
                                                <h3 class="text-lg font-bold text-red-400 italic">Vehículo Eliminado</h3>
                                            @endif
                                        </div>

                                        {{-- Fechas --}}
                                        <div class="space-y-2 mb-3">
                                            <div class="flex items-start text-sm">
                                                <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <div class="flex-1">
                                                    <div class="text-gray-100 font-medium">{{ $request->start_date->format('d/m/Y H:i') }}</div>
                                                    <div class="text-xs text-gray-400">{{ $request->start_date->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-start text-sm">
                                                <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <div class="flex-1">
                                                    <div class="text-gray-100 font-medium">{{ $request->end_date->format('d/m/Y H:i') }}</div>
                                                    <div class="text-xs text-gray-400">{{ $request->end_date->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Duración --}}
                                        @php
                                            $duration = floor($request->start_date->diffInDays($request->end_date));
                                            $hours = $request->start_date->diffInHours($request->end_date) % 24;
                                        @endphp
                                        <div class="flex items-center text-sm text-blue-400 mb-3">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-medium">
                                                {{ $duration }} día{{ $duration != 1 ? 's' : '' }}{{ $hours > 0 ? ", {$hours}h" : '' }}
                                            </span>
                                        </div>

                                        {{-- Barra de Progreso (solo para reservas activas en curso) --}}
                                        @if($showProgress)
                                            <div class="mb-3">
                                                <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                                                    <span>Progreso</span>
                                                    <span>{{ number_format($percentage, 0) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Quedan {{ $remaining }}
                                                </p>
                                            </div>
                                        @endif

                                        {{-- Motivo de Término Anticipado --}}
                                        @if($request->status === 'completed' && $request->early_termination_reason)
                                            <div class="mb-3 p-3 bg-orange-900/30 rounded-lg border border-orange-800/50">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs font-bold text-orange-400 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                        Término Anticipado
                                                    </span>
                                                    <button @click="$dispatch('open-modal', 'early-term-reason-{{ $request->id }}')" 
                                                            class="text-xs bg-orange-600 hover:bg-orange-500 text-white font-bold px-3 py-1.5 rounded transition-colors shadow-sm"
                                                            style="background-color: #ea580c; color: white;">
                                                        Ver Motivo
                                                    </button>
                                                </div>
                                            </div>

                                            <template x-teleport="body">
                                                <x-modal name="early-term-reason-{{ $request->id }}" :show="false" focusable maxWidth="sm">
                                                    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
                                                        <div class="flex items-center gap-3 mb-4">
                                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                                </svg>
                                                            </div>
                                                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                                Motivo de Término Anticipado
                                                            </h3>
                                                        </div>
                                                        
                                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-6 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                                            {{ $request->early_termination_reason }}
                                                        </p>

                                                        <div class="flex justify-end">
                                                            <x-secondary-button x-on:click="$dispatch('close')">
                                                                {{ __('Cerrar') }}
                                                            </x-secondary-button>
                                                        </div>
                                                    </div>
                                                </x-modal>
                                            </template>
                                        @endif

                                        {{-- Motivo de Rechazo --}}
                                        @if($request->status === 'rejected' && $request->rejection_reason)
                                            <div class="mb-3 text-xs text-red-400 bg-red-950/40 p-3 rounded-lg border border-red-800/50">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <div class="font-semibold text-red-300 mb-1">Motivo del rechazo:</div>
                                                        <div class="text-red-200">{{ $request->rejection_reason }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Footer con Acciones --}}
                                    @if($request->status === 'approved' || $request->status === 'in_trip')
                                        <div class="px-4 py-3 bg-gray-900/50 border-t border-gray-700">
                                            @if($request->vehicle)
                                                <div class="flex flex-col gap-2">
                                                    @if($request->status === 'approved')
                                                            <button
                                                                @click="$dispatch('set-delivery-photos', { requestId: '{{ $request->id }}', photos: {{ json_encode($request->delivery_photos ?? []) }}, comment: '{{ $request->delivery_comment ?? '' }}' }); $dispatch('open-modal', 'upload-delivery-photos-modal')"
                                                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition mb-2">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                {{ !empty($request->delivery_photos) ? 'Ver/Editar Fotos Recepción' : 'Subir Fotos Recepción' }}
                                                            </button>

                                                            <button 
                                                                @click="startTripUrl = '{{ route('requests.start-trip', $request->id) }}'; $dispatch('open-modal', 'start-trip-modal')"
                                                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                                🚀 Comenzar Viaje
                                                            </button>
                                                        @else
                                                            <button
                                                                @click="fuelRequestId = '{{ $request->id }}'; fuelVehicleId = '{{ $request->vehicle_id }}'; fuelType = '{{ $request->vehicle->fuel_type }}'; $dispatch('open-modal', 'fuel-load-modal')"
                                                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition mb-2">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                                </svg>
                                                                Cargar Combustible
                                                            </button>
                                                            <button
                                                                @click="returnUrl = '{{ route('requests.complete', $request->id) }}'; $dispatch('open-modal', 'confirm-return-modal')"
                                                                class="w-full inline-flex items-center justify-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-25 transition">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                                Devolver / Finalizar
                                                            </button>
                                                        @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-red-500 font-bold uppercase text-center block">Vehículo No Disponible</span>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Espacio vacío para mantener altura uniforme --}}
                                        <div class="h-3"></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 border-2 border-dashed border-gray-700 rounded-lg">
                            <svg class="mx-auto h-20 w-20 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-300 mb-2">No tienes reservas</h3>
                            <p class="text-gray-500 mb-6">Solicita un vehículo para comenzar con tus reservas</p>
                            <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-500 text-white font-semibold rounded-lg transition-colors shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Solicitar Vehículo
                            </a>
                        </div>
                    @endif

                    <!-- Modal de Confirmación -->
                    <x-modal name="confirm-return-modal" :show="false" focusable>
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
                            <h3
                                class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 dark:border-gray-700">
                                Check-in de Devolución
                            </h3>
                            <p class="mb-4 text-gray-600 dark:text-gray-400 text-sm">per complete los detalles del
                                estado del vehículo para finalizar el viaje.</p>

                            <form :action="returnUrl" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <!-- Kilometraje -->
                                    <div class="col-span-2">
                                        <x-input-label for="mileage" :value="__('Kilometraje de Devolución')" />
                                        <x-text-input id="mileage" type="text" name="return_mileage" required
                                            class="block mt-1 w-full" placeholder="Ingrese kilometraje actual"
                                            x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" />
                                        <x-input-error :messages="$errors->get('return_mileage')" class="mt-2" />
                                    </div>

                                    <!-- Nivel de Combustible -->
                                    <div>
                                        <x-input-label for="fuel_level" :value="__('Nivel de Combustible')" />
                                        <select id="fuel_level" name="fuel_level" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="" disabled selected>Seleccione...</option>
                                            <option value="1/4">1/4 de Estanque</option>
                                            <option value="1/2">1/2 Estanque</option>
                                            <option value="3/4">3/4 Estanque</option>
                                            <option value="casi_lleno">Casi Lleno</option>
                                            <option value="lleno">Lleno</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('fuel_level')" class="mt-2" />
                                    </div>

                                    <!-- Neumáticos Delanteros -->
                                    <div>
                                        <x-input-label for="tire_front" :value="__('Neumáticos Delanteros')" />
                                        <select id="tire_front" name="tire_status_front" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="good">🟢 Bueno</option>
                                            <option value="fair">🟡 Regular</option>
                                            <option value="poor">🔴 Malo/Daño</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('tire_status_front')" class="mt-2" />
                                    </div>

                                    <!-- Neumáticos Traseros -->
                                    <div>
                                        <x-input-label for="tire_rear" :value="__('Neumáticos Traseros')" />
                                        <select id="tire_rear" name="tire_status_rear" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="good">🟢 Bueno</option>
                                            <option value="fair">🟡 Regular</option>
                                            <option value="poor">🔴 Malo/Daño</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('tire_status_rear')" class="mt-2" />
                                    </div>

                                    <!-- Limpieza -->
                                    <div class="col-span-2 md:col-span-1">
                                        <x-input-label for="cleanliness" :value="__('Limpieza')" />
                                        <select id="cleanliness" name="cleanliness" required
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="clean">✨ Limpio</option>
                                            <option value="dirty">🌫️ Sucio</option>
                                            <option value="very_dirty">💩 Muy Sucio</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('cleanliness')" class="mt-2" />
                                    </div>

                                    <!-- Daños Carrocería -->
                                    <div class="col-span-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="body_damage_reported" value="1"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <span
                                                class="ml-2 text-gray-700 dark:text-gray-300">{{ __('Reportar nuevos daños en carrocería') }}</span>
                                        </label>
                                    </div>

                                    <!-- Comentarios -->
                                    <div class="col-span-2">
                                        <x-input-label for="comments" :value="__('Comentarios Adicionales')" />
                                        <textarea id="comments" name="comments" rows="2"
                                            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                        <x-input-error :messages="$errors->get('comments')" class="mt-2" />
                                    </div>

                                    <!-- Fotos (Con Compresión Múltiple) -->
                                    <div class="col-span-2" x-data="{
                                        files: [],
                                        isProcessing: false,
                                        previews: [],
                                        async compressImage(file) {
                                            return new Promise((resolve) => {
                                                const reader = new FileReader();
                                                reader.readAsDataURL(file);
                                                reader.onload = (event) => {
                                                    const img = new Image();
                                                    img.src = event.target.result;
                                                    img.onload = () => {
                                                        const canvas = document.createElement('canvas');
                                                        const ctx = canvas.getContext('2d');
                                                        const MAX_WIDTH = 1920;
                                                        let width = img.width;
                                                        let height = img.height;

                                                        if (width > MAX_WIDTH) {
                                                            height *= MAX_WIDTH / width;
                                                            width = MAX_WIDTH;
                                                        }

                                                        canvas.width = width;
                                                        canvas.height = height;
                                                        ctx.drawImage(img, 0, 0, width, height);

                                                        canvas.toBlob((blob) => {
                                                            const compressedFile = new File([blob], file.name, {
                                                                type: 'image/jpeg',
                                                                lastModified: Date.now(),
                                                            });
                                                            resolve({ file: compressedFile, preview: event.target.result });
                                                        }, 'image/jpeg', 0.8);
                                                    };
                                                };
                                            });
                                        },
                                        async handleFiles(event) {
                                            this.isProcessing = true;
                                            const selectedFiles = Array.from(event.target.files);

                                            if (selectedFiles.length === 0) {
                                                this.isProcessing = false;
                                                return;
                                            }

                                            try {
                                                const results = await Promise.all(selectedFiles.map(file => this.compressImage(file)));
                                                
                                                // Agregar nuevos archivos a los existentes (Acumulativo)
                                                results.forEach(result => {
                                                    // Evitar duplicados por nombre si se desea, o permitir todo. Aquí permitimos todo.
                                                    this.files.push(result.file);
                                                    this.previews.push(result.preview);
                                                });
                                                
                                                this.updateInputFiles();
                                                
                                                // Limpiar el input para permitir seleccionar los mismos archivos de nuevo si se desea
                                                event.target.value = ''; 
                                            } catch (error) {
                                                console.error('Error comprimiendo imágenes', error);
                                            } finally {
                                                this.isProcessing = false;
                                            }
                                        },
                                        removeFile(index) {
                                            this.files.splice(index, 1);
                                            this.previews.splice(index, 1);
                                            this.updateInputFiles();
                                        },
                                        updateInputFiles() {
                                            const dataTransfer = new DataTransfer();
                                            this.files.forEach(file => {
                                                dataTransfer.items.add(file);
                                            });
                                            // Update the HIDDEN input which is actually sent
                                            this.$refs.photosStorage.files = dataTransfer.files;
                                        }
                                    }">
                                        <x-input-label for="photos" :value="__('Fotos (Opcional - Máx 5)')" />
                                        
                                        <!-- Previews Grid -->
                                        <div class="grid grid-cols-5 gap-2 mt-2 mb-2" x-show="previews.length > 0">
                                            <template x-for="(preview, index) in previews" :key="index">
                                                <div class="relative aspect-square group">
                                                    <img :src="preview" class="w-full h-full object-cover rounded-md border border-gray-300 dark:border-gray-600">
                                                    <!-- Botón Eliminar -->
                                                    <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-600/90 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-700 focus:opacity-100">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Processing Status -->
                                        <div x-show="isProcessing" class="flex items-center text-sm text-blue-500 mb-2">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Optimizando imágenes...
                                        </div>

                                        <!-- Hidden Input for Submission -->
                                        <input type="file" name="photos[]" multiple class="hidden" x-ref="photosStorage">

                                        <!-- Visible Input for Selection Only -->
                                        <input type="file" id="photos" multiple accept="image/*"
                                            @change="handleFiles($event)"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300" />

                                        <div class="mt-2 text-xs text-gray-500" x-show="files.length > 0 && !isProcessing">
                                            <span x-text="files.length + ' imágenes listas para subir'"></span>
                                            <span class="text-green-500 font-bold ml-1">✓ Optimizadas</span>
                                        </div>
                                        <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                                        <x-input-error :messages="$errors->get('photos.*')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-4 border-t pt-4 dark:border-gray-700">
                                    <button type="button" @click="$dispatch('close')"
                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500 font-bold">
                                        Confirmar Entrega
                                    </button>
                                </div>
                            </form>
                        </div>
                    </x-modal>

                    <!-- Modal Carga Combustible -->
                    <x-modal name="fuel-load-modal" :show="false" focusable>
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
                            <h3
                                class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 dark:border-gray-700">
                                Registrar Carga de Combustible ⛽
                            </h3>

                            <div class="mb-4 p-4 rounded-md border-2 text-center" :class="{
                                    'bg-yellow-900/30 border-yellow-500 text-yellow-500': fuelType === 'diesel',
                                    'bg-green-900/30 border-green-500 text-green-500': fuelType === 'gasoline',
                                    'bg-gray-700 border-gray-500 text-gray-300': !fuelType || (fuelType !== 'diesel' && fuelType !== 'gasoline')
                                }">
                                <span class="block text-xs uppercase tracking-widest font-bold text-gray-400">Tipo de
                                    Combustible Requerido</span>
                                <span class="text-2xl font-black uppercase"
                                    x-text="fuelType === 'diesel' ? 'PETRÓLEO (DIESEL)' : (fuelType === 'gasoline' ? 'BENCINA (GASOLINA)' : 'CONSULTAR MANUAL')"></span>
                                <span class="block text-xs mt-1 text-white font-bold" x-show="fuelType">⚠️ Verifique
                                    antes de
                                    cargar</span>
                            </div>

                            <form action="{{ route('fuel-loads.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="vehicle_request_id" :value="fuelRequestId">
                                <input type="hidden" name="vehicle_id" :value="fuelVehicleId">
                                <input type="hidden" name="date" value="{{ now()->format('Y-m-d H:i') }}"> {{-- Default
                                to now --}}

                                <div class="space-y-4">
                                    <!-- Fecha (Editable) -->
                                    <div>
                                        <x-input-label for="fuel_date" :value="__('Fecha y Hora')" />
                                        <x-text-input id="fuel_date" type="datetime-local" name="date" required
                                            class="block mt-1 w-full" value="{{ now()->format('Y-m-d\TH:i') }}" />
                                    </div>

                                    <!-- Kilometraje -->
                                    <div>
                                        <x-input-label for="fuel_mileage" :value="__('Kilometraje Actual (Odometer)')" />
                                        <x-text-input id="fuel_mileage" type="number" name="mileage" required
                                            class="block mt-1 w-full" placeholder="Ej: 45200" />
                                        <span class="text-xs text-gray-500">Debe ser mayor o igual al último
                                            registrado.</span>
                                    </div>

                                    <!-- Litros y Precio -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="fuel_liters" :value="__('Litros')" />
                                            <x-text-input id="fuel_liters" type="number" step="0.01" name="liters"
                                                required class="block mt-1 w-full" placeholder="Ej: 40.5" x-data="{}"
                                                @input="$refs.total.innerText = '$' + Math.round(($el.value || 0) * (document.getElementById('fuel_price').value || 0))" />
                                        </div>
                                        <div>
                                            <x-input-label for="fuel_price" :value="__('Precio por Litro')" />
                                            <x-text-input id="fuel_price" type="number" name="price_per_liter" required
                                                class="block mt-1 w-full" placeholder="Ej: 1350"
                                                @input="$refs.total.innerText = '$' + Math.round(($el.value || 0) * (document.getElementById('fuel_liters').value || 0))" />
                                        </div>
                                    </div>

                                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded text-center">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Costo Total
                                            Estimado:</span>
                                        <div class="text-2xl font-bold text-green-600" x-ref="total">$0</div>
                                    </div>

                                    <!-- Foto Boleta (Con Compresión) -->
                                    <div x-data="{
                                        preview: null,
                                        isProcessing: false,
                                        async compressImage(file) {
                                            this.isProcessing = true;
                                            return new Promise((resolve) => {
                                                const reader = new FileReader();
                                                reader.readAsDataURL(file);
                                                reader.onload = (event) => {
                                                    const img = new Image();
                                                    img.src = event.target.result;
                                                    img.onload = () => {
                                                        const canvas = document.createElement('canvas');
                                                        const ctx = canvas.getContext('2d');
                                                        const MAX_WIDTH = 1920;
                                                        let width = img.width;
                                                        let height = img.height;

                                                        if (width > MAX_WIDTH) {
                                                            height *= MAX_WIDTH / width;
                                                            width = MAX_WIDTH;
                                                        }

                                                        canvas.width = width;
                                                        canvas.height = height;
                                                        ctx.drawImage(img, 0, 0, width, height);

                                                        canvas.toBlob((blob) => {
                                                            const compressedFile = new File([blob], file.name, {
                                                                type: 'image/jpeg',
                                                                lastModified: Date.now(),
                                                            });
                                                            resolve(compressedFile);
                                                        }, 'image/jpeg', 0.8);
                                                    };
                                                };
                                            });
                                        }
                                    }">
                                        <x-input-label for="fuel_photo" :value="__('Foto Boleta / Recibo')" />
                                        
                                        <!-- Preview -->
                                        <div class="mt-2 mb-2" x-show="preview" style="display: none;">
                                            <img :src="preview" class="max-h-40 rounded-md border border-gray-300 dark:border-gray-600">
                                            <div class="text-xs text-green-500 font-bold mt-1" x-show="!isProcessing && preview">✓ Imagen optimizada para carga rápida</div>
                                        </div>

                                        <!-- Processing State -->
                                        <div x-show="isProcessing" class="flex items-center text-sm text-blue-500 mb-2">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Optimizando imagen...
                                        </div>

                                        <input type="file" id="fuel_photo" name="receipt_photo" accept="image/*" x-ref="fuelPhotoInput"
                                            @change="
                                                const file = $event.target.files[0];
                                                if (file) {
                                                    // Preview inmediato
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { preview = e.target.result; };
                                                    reader.readAsDataURL(file);

                                                    // Comprimir
                                                    compressImage(file).then(compressedFile => {
                                                        const dataTransfer = new DataTransfer();
                                                        dataTransfer.items.add(compressedFile);
                                                        $refs.fuelPhotoInput.files = dataTransfer.files;
                                                        isProcessing = false;
                                                    });
                                                }
                                            "
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-gray-700 dark:file:text-gray-300" />
                                    </div>

                                    <!-- Nro Factura -->
                                    <div>
                                        <x-input-label for="invoice_number" :value="__('Nº Boleta/Factura (Opcional)')" />
                                        <x-text-input id="invoice_number" type="text" name="invoice_number"
                                            class="block mt-1 w-full" />
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-4 border-t pt-4 mt-6 dark:border-gray-700">
                                    <button type="button" @click="$dispatch('close')"
                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 font-bold">Registrar
                                        Carga</button>
                                </div>
                            </form>
                        </div>
                    </x-modal>

                    <!-- Modal Subir Fotos Recepción -->
                    <x-modal name="upload-delivery-photos-modal" :show="false" focusable>
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg" x-data="{
                            deliveryRequestId: null,
                            deliveryRequestUrl: '',
                            existingPhotos: [],
                            deliveryComment: '',
                            init() {
                                this.$watch('deliveryRequestId', (value) => {
                                    if (value) {
                                        this.deliveryRequestUrl = '/requests/' + value + '/delivery-photos';
                                    }
                                });
                            },
                            deletePhoto(path) {
                                if (!confirm('¿Estás seguro de eliminar esta foto?')) return;

                                axios.delete('/requests/' + this.deliveryRequestId + '/delivery-photos', {
                                    data: { photo_path: path }
                                })
                                .then(response => {
                                    if (response.data.success) {
                                        // Remover localmente
                                        this.existingPhotos = this.existingPhotos.filter(p => p !== path);
                                    } else {
                                        alert('Error al eliminar foto.');
                                    }
                                })
                                .catch(error => {
                                    console.error(error);
                                    alert('Error al eliminar foto.');
                                });
                            }
                        }" @set-delivery-photos.window="
                            deliveryRequestId = $event.detail.requestId; 
                            existingPhotos = $event.detail.photos || [];
                            deliveryComment = $event.detail.comment || '';
                        ">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2 dark:border-gray-700">
                                Fotos de Recepción (Check-in)
                            </h3>
                            <p class="mb-4 text-gray-600 dark:text-gray-400 text-sm">
                                Suba fotos del estado actual del vehículo ANTES de comenzar su viaje. Esto servirá como evidencia de las condiciones en que recibió la unidad.
                            </p>

                            <!-- Existing Photos Grid -->
                            <div x-show="existingPhotos.length > 0" class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fotos Subidas:</h4>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">
                                    <template x-for="(photo, index) in existingPhotos" :key="index">
                                        <div class="relative aspect-square group">
                                            <img :src="'/storage/' + photo" class="w-full h-full object-cover rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer" @click="$dispatch('open-gallery', { photos: existingPhotos.map(p => '/storage/' + p), title: 'Fotos de Recepción' })">
                                            
                                            <!-- Delete Button -->
                                            <button type="button" @click="deletePhoto(photo)" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 shadow-lg hover:bg-red-700 focus:outline-none transition-transform hover:scale-110">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <form :action="deliveryRequestUrl" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Comment Field -->
                                <div class="mb-4">
                                    <label for="delivery_comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Comentario / Observaciones (Opcional)
                                    </label>
                                    <textarea 
                                        id="delivery_comment" 
                                        name="delivery_comment" 
                                        x-model="deliveryComment"
                                        rows="3" 
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" 
                                        placeholder="Describa el estado del vehículo, daños existentes, etc."></textarea>
                                </div>
                                
                                <div class="mb-4" x-data="{
                                    files: [],
                                    previews: [],
                                    isProcessing: false,
                                    async compressImage(file) {
                                        return new Promise((resolve) => {
                                            const reader = new FileReader();
                                            reader.readAsDataURL(file);
                                            reader.onload = (event) => {
                                                const img = new Image();
                                                img.src = event.target.result;
                                                img.onload = () => {
                                                    const canvas = document.createElement('canvas');
                                                    const ctx = canvas.getContext('2d');
                                                    const MAX_WIDTH = 1920;
                                                    let width = img.width;
                                                    let height = img.height;

                                                    if (width > MAX_WIDTH) {
                                                        height *= MAX_WIDTH / width;
                                                        width = MAX_WIDTH;
                                                    }

                                                    canvas.width = width;
                                                    canvas.height = height;
                                                    ctx.drawImage(img, 0, 0, width, height);

                                                    canvas.toBlob((blob) => {
                                                        const compressedFile = new File([blob], file.name, {
                                                            type: 'image/jpeg',
                                                            lastModified: Date.now(),
                                                        });
                                                        resolve({ file: compressedFile, preview: event.target.result });
                                                    }, 'image/jpeg', 0.8);
                                                };
                                            };
                                        });
                                    },
                                    async handleFiles(event) {
                                        this.isProcessing = true;
                                        const selectedFiles = Array.from(event.target.files);

                                        if (selectedFiles.length === 0) {
                                            this.isProcessing = false;
                                            return;
                                        }

                                        try {
                                            const results = await Promise.all(selectedFiles.map(file => this.compressImage(file)));
                                            
                                            // Agregar nuevos archivos a los existentes (Acumulativo)
                                            results.forEach(result => {
                                                this.files.push(result.file);
                                                this.previews.push(result.preview);
                                            });
                                            
                                            this.updateInputFiles();
                                            
                                            // Limpiar el input visual para permitir seleccionar los mismos archivos de nuevo
                                            event.target.value = ''; 
                                        } catch (error) {
                                            console.error('Error procesando imágenes', error);
                                        } finally {
                                            this.isProcessing = false;
                                        }
                                    },
                                    removeFile(index) {
                                        this.files.splice(index, 1);
                                        this.previews.splice(index, 1);
                                        this.updateInputFiles();
                                    },
                                    updateInputFiles() {
                                        const dataTransfer = new DataTransfer();
                                        this.files.forEach(file => {
                                            dataTransfer.items.add(file);
                                        });
                                        // Update the HIDDEN input which is actually sent
                                        this.$refs.deliveryPhotosStorage.files = dataTransfer.files;
                                    }
                                }">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Agregar Nuevas Fotos
                                    </label>
                                    
                                    <!-- Previews Grid -->
                                    <div class="grid grid-cols-5 gap-2 mt-2 mb-2" x-show="previews.length > 0">
                                        <template x-for="(preview, index) in previews" :key="index">
                                            <div class="relative aspect-square group">
                                                <img :src="preview" class="w-full h-full object-cover rounded-md border border-gray-300 dark:border-gray-600">
                                                <!-- Botón Eliminar -->
                                                <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-600/90 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-700 focus:outline-none">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Processing Status -->
                                    <div x-show="isProcessing" class="flex items-center text-sm text-blue-500 mb-2">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Procesando imágenes...
                                    </div>

                                    <!-- Hidden Input for Submission -->
                                    <input type="file" name="delivery_photos[]" multiple class="hidden" x-ref="deliveryPhotosStorage">

                                    <!-- Visible Input for Selection Only -->
                                    <input type="file" multiple accept="image/*"
                                        @change="handleFiles($event)"
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                                        
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" x-show="files.length === 0">
                                        Puede seleccionar múltiples imágenes (Máx 10MB c/u).
                                    </p>
                                    <div class="mt-2 text-xs text-gray-500" x-show="files.length > 0 && !isProcessing">
                                        <span x-text="files.length + ' imágenes listas para subir'"></span>
                                        <span class="text-green-500 font-bold ml-1">✓ Listas</span>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <x-secondary-button @click="$dispatch('close')" type="button">
                                        {{ __('Cancelar') }}
                                    </x-secondary-button>

                                    <x-primary-button class="bg-blue-600 hover:bg-blue-500">
                                        {{ __('Subir Fotos') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </x-modal>

                    <!-- Modal Comenzar Viaje -->
                    <x-modal name="start-trip-modal" :show="false" focusable>
                        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-green-100 rounded-full dark:bg-green-900">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            
                            <h3 class="text-xl font-bold text-center text-gray-900 dark:text-gray-100 mb-2">
                                ¿Comenzar Viaje?
                            </h3>
                            
                            <div class="mt-2 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Al comenzar el viaje, confirmas que has recibido el vehículo.
                                </p>
                                <p class="mt-2 text-sm font-bold text-yellow-600 dark:text-yellow-500 bg-yellow-100 dark:bg-yellow-900/30 p-2 rounded-lg">
                                    ⚠️ Recuerda: Es recomendable subir fotos del estado del vehículo antes de iniciar. Una vez iniciado el viaje, no podrás subir fotos de recepción.
                                </p>
                            </div>

                            <form :action="startTripUrl" method="POST" class="mt-6 flex justify-end space-x-3">
                                @csrf
                                <x-secondary-button @click="$dispatch('close')" type="button">
                                    {{ __('Cancelar') }}
                                </x-secondary-button>

                                <x-primary-button class="bg-green-600 hover:bg-green-500">
                                    {{ __('Confirmar e Iniciar') }}
                                </x-primary-button>
                            </form>
                        </div>
                    </x-modal>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>