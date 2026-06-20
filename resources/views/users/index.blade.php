@php
    $existingDepartments = \App\Models\User::select('departamento')
        ->whereNotNull('departamento')
        ->where('departamento', '!=', '')
        ->distinct()
        ->pluck('departamento')
        ->toArray();
    // Aseguramos que Finanzas, Controlling, Operaciones y dimak estén siempre en la lista
    $defaultDepartments = ['Finanzas', 'Controlling', 'Operaciones', 'dimak'];
    $departments = array_unique(array_merge($defaultDepartments, $existingDepartments));
    sort($departments);
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Usuarios') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header Actions -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-6 space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Directorio de Usuarios
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestiona los accesos, roles y permisos de la plataforma.</p>
                </div>
                <div class="flex space-x-3">
                    @if(request('view') === 'trash')
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-600 rounded-lg font-medium text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver al Directorio
                        </a>
                    @else
                        <a href="{{ route('users.index', ['view' => 'trash']) }}"
                            class="inline-flex items-center px-6 py-2.5 bg-rose-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-lg shadow-rose-500/30 hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Papelera
                        </a>
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-user-modal')"
                            class="inline-flex items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo Usuario
                        </button>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-slate-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Usuario
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Rol / Privilegios
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 cursor-pointer relative" x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'view-user-{{ $user->id }}')">
                                                @if ($user->profile_photo_path)
                                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-600"
                                                        src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                        alt="{{ $user->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold text-lg border border-indigo-200 dark:border-indigo-800">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white dark:border-slate-800 {{ $user->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                                    x-data=""
                                                    x-on:click.prevent="$dispatch('open-modal', 'view-user-{{ $user->id }}')">
                                                    {{ $user->short_name }}
                                                    @if(auth()->id() === $user->id)
                                                        <span class="ml-2 px-2 py-0.5 inline-flex text-[10px] font-semibold rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                            TÚ
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user->email }}
                                                </div>
                                                @if($user->jefatura)
                                                    <div class="flex items-center gap-1 mt-0.5 text-[11px] text-amber-500 dark:text-amber-400">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                        <span>Jefe: {{ $user->jefatura->name }} {{ $user->jefatura->last_name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->deleted_at)
                                            <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded border bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-300 dark:text-slate-950 dark:border-slate-400">
                                                {{ ucfirst($user->role === 'worker' ? 'Trabajador' : ($user->role === 'supervisor' ? 'Supervisor' : ($user->role === 'viewer' ? 'Visualizador' : ($user->role === 'jefatura' ? 'Jefatura' : 'Administrador')))) }}
                                            </span>
                                        @else
                                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" onchange="this.form.submit()"
                                                    class="text-xs font-semibold rounded-md border focus:ring-0 cursor-pointer py-1 pl-3 pr-8 appearance-none bg-no-repeat transition-colors
                                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-900 border-purple-200 hover:bg-purple-200 dark:bg-purple-300 dark:text-purple-950 dark:border-purple-400 dark:hover:bg-purple-200' : '' }}
                                                        {{ $user->role === 'supervisor' ? 'bg-blue-100 text-blue-900 border-blue-200 hover:bg-blue-200 dark:bg-blue-300 dark:text-blue-950 dark:border-blue-400 dark:hover:bg-blue-200' : '' }}
                                                        {{ $user->role === 'worker' ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-500 dark:bg-emerald-600 dark:text-white dark:border-emerald-600 dark:hover:bg-emerald-500' : '' }}
                                                        {{ $user->role === 'viewer' ? 'bg-slate-100 text-slate-900 border-slate-200 hover:bg-slate-200 dark:bg-slate-300 dark:text-slate-950 dark:border-slate-400 dark:hover:bg-slate-200' : '' }}
                                                        {{ $user->role === 'jefatura' ? 'bg-amber-100 text-amber-900 border-amber-200 hover:bg-amber-200 dark:bg-amber-300 dark:text-amber-950 dark:border-amber-400 dark:hover:bg-amber-200' : '' }}"
                                                    style="background-position: right 0.5rem center; background-size: 1em 1em; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%22currentColor%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22 /%3E%3C/svg%3E');">
                                                    <option value="worker" {{ $user->role === 'worker' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">Trabajador</option>
                                                    <option value="supervisor" {{ $user->role === 'supervisor' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">Supervisor</option>
                                                    <option value="viewer" {{ $user->role === 'viewer' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">Visualizador</option>
                                                    <option value="jefatura" {{ $user->role === 'jefatura' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">Jefatura</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">Administrador</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->deleted_at)
                                            <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded border bg-red-100 text-red-900 border-red-200 dark:bg-red-300 dark:text-red-950 dark:border-red-400">
                                                Eliminado
                                            </span>
                                        @else
                                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select name="is_active" onchange="this.form.submit()"
                                                    class="text-xs font-semibold rounded-md border focus:ring-0 cursor-pointer py-1 pl-3 pr-8 appearance-none bg-no-repeat transition-colors
                                                        {{ $user->is_active ? 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-500 dark:bg-emerald-600 dark:text-white dark:border-emerald-600 dark:hover:bg-emerald-500' : 'bg-red-100 text-red-900 border-red-200 hover:bg-red-200 dark:bg-red-300 dark:text-red-950 dark:border-red-400 dark:hover:bg-red-200' }}"
                                                    style="background-position: right 0.5rem center; background-size: 1em 1em; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%22currentColor%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19 9l-7 7-7-7%22 /%3E%3C/svg%3E');">
                                                    <option value="1" {{ $user->is_active ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">Activo</option>
                                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }} class="bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100">Inactivo</option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-3">
                                            @if(request('view') === 'trash')
                                                <!-- Restore -->
                                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="inline-block">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors" title="Restaurar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                                        </svg>
                                                    </button>
                                                </form>
                                                <!-- Force Delete -->
                                                <form action="{{ route('users.force-delete', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar PERMANENTEMENTE a este usuario?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors ml-1" title="Eliminar Definitivamente">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <!-- View -->
                                                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'view-user-{{ $user->id }}')"
                                                    class="text-gray-400 hover:text-indigo-600 dark:text-gray-500 dark:hover:text-indigo-400 transition-colors" title="Ver Detalles">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </button>
                                                
                                                <!-- Edit -->
                                                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-user-{{ $user->id }}')"
                                                    class="text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors ml-2" title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </button>
                                                
                                                <!-- Delete -->
                                                @if(auth()->id() !== $user->id)
                                                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-user-{{ $user->id }}')" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors ml-2" title="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales para cada Usuario -->
    @foreach ($users as $user)
        <!-- Modal Ver Usuario -->
        <x-modal name="view-user-{{ $user->id }}" focusable>
            <div class="p-6 text-left">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                    {{ __('Detalle de Usuario') }}
                </h2>
                <div class="flex items-center mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                    <div class="flex-shrink-0 h-16 w-16">
                        @if ($user->profile_photo_path)
                            <img class="h-16 w-16 rounded-full object-cover border-2 border-white dark:border-gray-600 shadow-sm"
                                src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                alt="{{ $user->name }}">
                        @else
                            <div class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-500 dark:text-indigo-300 font-bold text-2xl border-2 border-white dark:border-gray-600 shadow-sm">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $user->name }} {{ $user->last_name }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 mt-1">
                            {{ ucfirst($user->role === 'worker' ? 'Trabajador' : ($user->role === 'supervisor' ? 'Supervisor' : ($user->role === 'viewer' ? 'Visualizador' : ($user->role === 'jefatura' ? 'Jefatura' : 'Administrador')))) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600 dark:text-gray-300 border-t border-gray-100 dark:border-gray-700 pt-6">
                    <div><strong>RUT:</strong> {{ $user->rut ?? 'No registrado' }}</div>
                    <div><strong>Teléfono:</strong> {{ $user->phone ?? 'No registrado' }}</div>
                    <div><strong>Dirección:</strong> {{ $user->address ?? 'No registrada' }}</div>
                    <div><strong>Cargo:</strong> {{ $user->cargo ?? 'No registrado' }}</div>
                    <div><strong>Departamento:</strong> {{ $user->departamento ?? 'No registrado' }}</div>
                    <div><strong>Jefatura:</strong> {{ $user->jefatura ? $user->jefatura->name . ' ' . $user->jefatura->last_name : 'N/A' }}</div>
                    <div><strong>Estado:</strong> {{ $user->is_active ? 'Activo' : 'Inactivo' }}</div>
                    <div><strong>Verificado:</strong> {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y') : 'Pendiente' }}</div>
                    <div><strong>Creado:</strong> {{ $user->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="mt-8 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cerrar') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>

        <x-modal name="edit-user-{{ $user->id }}" :show="$errors->has('email') && old('user_id') == $user->id" focusable>
            @php
                $editModules = $user->authorized_modules ?? [];
                $editAllChecked = empty($editModules) || in_array('all', $editModules);
                $editModulesJs = collect($editModules)->filter(fn($m) => $m !== 'all')->map(fn($m) => "'$m'")->implode(',');
            @endphp
            <form method="POST" action="{{ route('users.update', $user->id) }}" class="p-6 text-left"
                x-data="{
                    openRole: false,
                    selectedRole: '{{ old('role', $user->role) }}',
                    roleLabel: '{{ old('role', $user->role) == 'admin' ? 'Administrador' : (old('role', $user->role) == 'supervisor' ? 'Supervisor' : (old('role', $user->role) == 'jefatura' ? 'Jefatura' : (old('role', $user->role) == 'viewer' ? 'Visualizador' : 'Trabajador'))) }}',
                    roles: [{v:'worker',l:'Trabajador'},{v:'supervisor',l:'Supervisor'},{v:'jefatura',l:'Jefatura'},{v:'admin',l:'Administrador'},{v:'viewer',l:'Visualizador'}],
                    openEstado: false,
                    selectedEstado: '{{ old('is_active', $user->is_active) ? '1' : '0' }}',
                    estadoLabel: '{{ old('is_active', $user->is_active) ? 'Activo' : 'Inactivo' }}',
                    allChecked: {{ $editAllChecked ? 'true' : 'false' }},
                    modules: [{!! $editModulesJs !!}]
                }"
                x-init="$watch('allChecked', v => { if(v) modules = [] }); $watch('modules', v => { if(v.length > 0) allChecked = false })">
                @csrf
                @method('PATCH')
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <h2 class="text-lg font-medium text-gray-100 mb-6">Editar Usuario</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nombres</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="text" name="name" id="name_{{ $user->id }}" value="{{ old('name', $user->name) }}" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Apellidos</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="text" name="last_name" id="last_name_{{ $user->id }}" value="{{ old('last_name', $user->last_name) }}" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm">
                        </div>
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Correo Electrónico</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                            <input type="email" name="email" id="email_{{ $user->id }}" value="{{ old('email', $user->email) }}" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <!-- Rol -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Rol</label>
                        <input type="hidden" name="role" x-model="selectedRole">
                        <button type="button" @click="openRole = !openRole" @click.away="openRole = false" class="w-full flex items-center justify-between border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 hover:border-slate-600 transition-colors text-left focus:outline-none focus:border-blue-500">
                            <span x-text="roleLabel" class="text-slate-100 text-sm"></span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" :class="openRole ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="openRole" x-transition class="absolute z-50 w-full mt-1 bg-[#1e293b] shadow-lg max-h-60 rounded-lg py-1 text-sm ring-1 ring-slate-700 overflow-auto" style="display:none;">
                            <template x-for="r in roles" :key="r.v">
                                <li @click="selectedRole = r.v; roleLabel = r.l; openRole = false" class="text-gray-200 cursor-pointer select-none py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors" :class="selectedRole === r.v ? 'bg-blue-600/20 text-blue-400' : ''">
                                    <span x-text="r.l"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <!-- Departamento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Departamento</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                            <select name="departamento" id="departamento_{{ $user->id }}" class="w-full bg-transparent border-none text-slate-100 text-sm py-2.5 px-3 focus:ring-0 focus:outline-none cursor-pointer" style="background-color: transparent;">
                                <option value="" class="bg-[#1e293b]">-- Sin Departamento --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('departamento', $user->departamento) === $dept ? 'selected' : '' }} class="bg-[#1e293b]">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Jefatura -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Jefatura Asignada (Opcional)</label>
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                            <select name="jefatura_id" class="w-full bg-transparent border-none text-slate-100 text-sm py-2.5 px-3 focus:ring-0 focus:outline-none cursor-pointer" style="background-color:transparent;">
                                <option value="" class="bg-[#1e293b]">-- Sin Jefatura --</option>
                                @foreach($jefaturas as $jefe)
                                    <option value="{{ $jefe->id }}" {{ old('jefatura_id', $user->jefatura_id) == $jefe->id ? 'selected' : '' }} class="bg-[#1e293b]">{{ $jefe->name }} {{ $jefe->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Estado -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Estado</label>
                        <input type="hidden" name="is_active" x-model="selectedEstado">
                        <button type="button" @click="openEstado = !openEstado" @click.away="openEstado = false" class="w-full flex items-center justify-between border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 hover:border-slate-600 transition-colors text-left focus:outline-none focus:border-blue-500">
                            <span x-text="estadoLabel" class="text-sm" :class="selectedEstado === '1' ? 'text-emerald-400' : 'text-slate-400'"></span>
                            <svg class="w-4 h-4 text-slate-500 transition-transform" :class="openEstado ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <ul x-show="openEstado" x-transition class="absolute z-50 w-full mt-1 bg-[#1e293b] shadow-lg rounded-lg py-1 text-sm ring-1 ring-slate-700 overflow-auto" style="display:none;">
                            <li @click="selectedEstado = '1'; estadoLabel = 'Activo'; openEstado = false" class="text-emerald-400 cursor-pointer py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors" :class="selectedEstado === '1' ? 'bg-blue-600/20' : ''">Activo</li>
                            <li @click="selectedEstado = '0'; estadoLabel = 'Inactivo'; openEstado = false" class="text-slate-400 cursor-pointer py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors" :class="selectedEstado === '0' ? 'bg-blue-600/20' : ''">Inactivo</li>
                        </ul>
                    </div>
                </div>

                <!-- Módulos Autorizados -->
                <div class="mt-4">
                     <label class="block text-sm font-medium text-gray-300 mb-2">Módulos Autorizados</label>
                     <div class="grid grid-cols-2 gap-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="all" x-model="allChecked" class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-300">Todos</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="vehicles" x-model="modules" class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-300">Vehículos</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="rooms" x-model="modules" class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-300">Salas</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="assets" x-model="modules" class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-300">Activos</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="renditions" x-model="modules" class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-300">Rendiciones</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="finances" x-model="modules" class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-300">Finanzas</span>
                        </label>
                     </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 bg-rose-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-rose-500/30 hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Modal Confirmar Eliminación -->
        @if(auth()->id() !== $user->id)
        <x-modal name="delete-user-{{ $user->id }}" focusable>
            <div class="p-6 text-center">
                <!-- Warning Icon -->
                <div class="mx-auto w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mb-5 ring-4 ring-red-500/20">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-white mb-2">¿Eliminar este usuario?</h3>
                <p class="text-sm text-slate-400 mb-6">Esta acción moverá al usuario a la papelera. Podrás restaurarlo más tarde si lo necesitas.</p>

                <!-- User Profile Card -->
                <div class="flex items-center gap-4 bg-slate-800/60 border border-slate-700 rounded-xl p-4 mb-6 text-left">
                    <div class="flex-shrink-0">
                        @if ($user->profile_photo_path)
                            <img class="h-12 w-12 rounded-full object-cover border-2 border-red-500/30" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-12 w-12 rounded-full bg-red-500/20 flex items-center justify-center text-red-400 font-bold text-lg border-2 border-red-500/30">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white truncate">{{ $user->name }} {{ $user->last_name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-semibold rounded-full bg-slate-700 text-slate-300">
                            {{ ucfirst($user->role === 'worker' ? 'Trabajador' : ($user->role === 'supervisor' ? 'Supervisor' : ($user->role === 'viewer' ? 'Visualizador' : ($user->role === 'jefatura' ? 'Jefatura' : 'Administrador')))) }}
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 border border-slate-600 text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-800 hover:border-slate-500 transition-all">
                        Cancelar
                    </button>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-red-500/30 hover:bg-red-500 transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
        @endif
    @endforeach

    <!-- Modal Crear Usuario (Mantener igual) -->
    <x-modal name="create-user-modal" :show="$errors->has('password') || $errors->has('password_confirmation') || (session('errors') && !request()->routeIs('users.update'))" focusable>
        <form method="POST" action="{{ route('users.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Crear Nuevo Usuario') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('El usuario recibirá un correo para verificar su cuenta. La primera vez que ingrese deberá cambiar su contraseña obligatoriamente.') }}
            </p>

            <div class="mt-6 space-y-4" x-data="{ openRole: false, selectedRole: '{{ old('role', 'worker') }}', roleLabel: '{{ old('role') == 'admin' ? 'Administrador' : (old('role') == 'supervisor' ? 'Supervisor' : (old('role') == 'jefatura' ? 'Jefatura' : (old('role') == 'viewer' ? 'Visualizador' : 'Trabajador'))) }}', roles: [{v:'worker',l:'Trabajador'},{v:'supervisor',l:'Supervisor'},{v:'jefatura',l:'Jefatura'},{v:'admin',l:'Administrador'},{v:'viewer',l:'Visualizador'}] }">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Nombres</label>
                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                        <input type="text" name="name" id="new_name" value="{{ old('name') }}" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm" placeholder="Ej: Juan Ignacio">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Apellido -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Apellidos</label>
                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                        <input type="text" name="last_name" id="new_last_name" value="{{ old('last_name') }}" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm" placeholder="Ej: Pérez González">
                    </div>
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Correo Electrónico</label>
                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors">
                        <input type="email" name="email" id="new_email" value="{{ old('email') }}" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm" placeholder="usuario@empresa.cl">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Rol (Custom Dropdown) -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Rol</label>
                    <input type="hidden" name="role" x-model="selectedRole">
                    <button type="button" @click="openRole = !openRole" @click.away="openRole = false" class="w-full flex items-center justify-between border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 hover:border-slate-600 transition-colors text-left focus:outline-none focus:border-blue-500">
                        <span x-text="roleLabel" class="text-slate-100 text-sm"></span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform" :class="openRole ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <ul x-show="openRole" x-transition class="absolute z-50 w-full mt-1 bg-[#1e293b] shadow-lg max-h-60 rounded-lg py-1 text-sm ring-1 ring-slate-700 overflow-auto" style="display:none;">
                        <template x-for="r in roles" :key="r.v">
                            <li @click="selectedRole = r.v; roleLabel = r.l; openRole = false" class="text-gray-200 cursor-pointer select-none py-2.5 px-4 hover:bg-blue-600 hover:text-white transition-colors" :class="selectedRole === r.v ? 'bg-blue-600/20 text-blue-400' : ''">
                                <span x-text="r.l"></span>
                            </li>
                        </template>
                    </ul>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Departamento -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Departamento</label>
                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                        <select id="new_departamento" name="departamento" class="w-full bg-transparent border-none text-slate-100 text-sm py-2.5 px-3 focus:ring-0 focus:outline-none cursor-pointer" style="background-color: transparent;">
                            <option value="" class="bg-[#1e293b]">-- Sin Departamento --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ old('departamento') === $dept ? 'selected' : '' }} class="bg-[#1e293b]">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-input-error :messages="$errors->get('departamento')" class="mt-2" />
                </div>

                <!-- Jefatura -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Jefatura Asignada (Opcional)</label>
                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] focus-within:border-blue-500 hover:border-slate-600 transition-colors">
                        <select id="new_jefatura" name="jefatura_id" class="w-full bg-transparent border-none text-slate-100 text-sm py-2.5 px-3 focus:ring-0 focus:outline-none cursor-pointer" style="background-color: transparent;">
                            <option value="" class="bg-[#1e293b]">-- Sin Jefatura --</option>
                            @foreach($jefaturas as $jefe)
                                <option value="{{ $jefe->id }}" {{ old('jefatura_id') == $jefe->id ? 'selected' : '' }} class="bg-[#1e293b]">{{ $jefe->name }} {{ $jefe->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Módulos Autorizados -->
                <div x-data="{ allChecked: true, modules: [] }" x-init="$watch('allChecked', v => { if(v) modules = [] }); $watch('modules', v => { if(v.length > 0) allChecked = false })">
                    <x-input-label :value="__('Módulos Autorizados')" class="mb-2" />
                    <div class="grid grid-cols-2 gap-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="all" x-model="allChecked"
                                class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Todos</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="vehicles" x-model="modules"
                                class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Vehículos</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="rooms" x-model="modules"
                                class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Salas</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="assets" x-model="modules"
                                class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Activos</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="renditions" x-model="modules"
                                class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Rendiciones</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="authorized_modules[]" value="finances" x-model="modules"
                                class="rounded border-slate-600 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Finanzas</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Selecciona "Todos" para acceso completo según el rol.</p>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Contraseña Inicial</label>
                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors relative">
                        <input id="new_password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm pr-10">
                        <button type="button" @click="show = !show" class="absolute right-3 text-slate-500 hover:text-slate-300 focus:outline-none">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.18-3.64m6.308-1.353a4.5 4.5 0 015.657 5.657m0 0l-5.657-5.657m0 0L3 3m3.343 3.343L3 3m18 18l-3.343-3.343" /></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Confirmar Contraseña</label>
                    <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors relative">
                        <input id="new_password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm pr-10">
                        <button type="button" @click="show = !show" class="absolute right-3 text-slate-500 hover:text-slate-300 focus:outline-none">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.18-3.64m6.308-1.353a4.5 4.5 0 015.657 5.657m0 0l-5.657-5.657m0 0L3 3m3.343 3.343L3 3m18 18l-3.343-3.343" /></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2.5 bg-rose-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-rose-500/30 hover:bg-rose-500 transition-all hover:-translate-y-0.5 cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:-translate-y-0.5">
                    Crear Usuario
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>