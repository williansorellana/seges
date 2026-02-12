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
            <div class="flex justify-end mb-4 space-x-4">
                @if(request('view') === 'trash')
                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Volver a Usuarios') }}
                    </a>
                @else
                    <a href="{{ route('users.index', ['view' => 'trash']) }}"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('Papelera') }}
                    </a>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-user-modal')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Nuevo Usuario') }}
                    </button>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Rol
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 cursor-pointer" x-data=""
                                                    x-on:click.prevent="$dispatch('open-modal', 'view-user-{{ $user->id }}')">
                                                    @if ($user->profile_photo_path)
                                                        <img class="h-10 w-10 rounded-full object-cover"
                                                            src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                            alt="{{ $user->name }}">
                                                    @else
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xl">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 cursor-pointer hover:text-indigo-600"
                                                        x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'view-user-{{ $user->id }}')">
                                                        {{ $user->short_name }}
                                                        @if(auth()->id() === $user->id)
                                                            <span
                                                                class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                                (Tú)
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->deleted_at)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($user->role === 'worker' ? 'Trabajador' : ($user->role === 'supervisor' ? 'Supervisor' : ($user->role === 'viewer' ? 'Visualizador' : 'Administrador'))) }}
                                                </span>
                                            @else
                                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="role" onchange="this.form.submit()"
                                                        class="text-xs font-semibold rounded-full border-none focus:ring-0 cursor-pointer
                                                                                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                                                                                        {{ $user->role === 'supervisor' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                                                        {{ $user->role === 'worker' ? 'bg-green-100 text-green-800' : '' }}
                                                                                                        {{ $user->role === 'viewer' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                        <option value="worker" {{ $user->role === 'worker' ? 'selected' : '' }}
                                                            class="bg-white text-gray-900">Trabajador</option>
                                                        <option value="supervisor" {{ $user->role === 'supervisor' ? 'selected' : '' }} class="bg-white text-gray-900">Supervisor</option>
                                                        <option value="viewer" {{ $user->role === 'viewer' ? 'selected' : '' }}
                                                            class="bg-white text-gray-900">Visualizador</option>
                                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}
                                                            class="bg-white text-gray-900">Administrador</option>
                                                    </select>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->deleted_at)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Eliminado
                                                </span>
                                            @else
                                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="is_active" onchange="this.form.submit()"
                                                        class="text-xs font-semibold rounded-full border-none focus:ring-0 cursor-pointer
                                                                                                        {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        <option value="1" {{ $user->is_active ? 'selected' : '' }}
                                                            class="bg-white text-gray-900">Activo</option>
                                                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}
                                                            class="bg-white text-gray-900">Inactivo</option>
                                                    </select>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex justify-center space-x-2">
                                                @if(request('view') === 'trash')
                                                    <!-- Restore -->
                                                    <form action="{{ route('users.restore', $user->id) }}" method="POST"
                                                        class="inline-block">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit"
                                                            class="text-green-500 hover:text-green-700 transition"
                                                            title="Restaurar">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                class="w-6 h-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <!-- Force Delete -->
                                                    <form action="{{ route('users.force-delete', $user->id) }}" method="POST"
                                                        class="inline-block"
                                                        onsubmit="return confirm('¿Estás seguro de eliminar PERMANENTEMENTE a este usuario?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 transition"
                                                            title="Eliminar Definitivamente">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                class="w-6 h-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- View -->
                                                    <button x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'view-user-{{ $user->id }}')"
                                                        class="text-emerald-500 hover:text-emerald-700 transition"
                                                        title="Ver Detalles">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <!-- Edit Button -->
                                                    <button x-data=""
                                                        x-on:click.prevent="$dispatch('open-modal', 'edit-user-{{ $user->id }}')"
                                                        class="text-blue-500 hover:text-blue-700 transition"
                                                        title="Editar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                        </svg>
                                                    </button>
                                                    <!-- Delete -->
                                                    @if(auth()->id() !== $user->id)
                                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                            class="inline-block"
                                                            onsubmit="return confirm('¿Estás seguro de mover a este usuario a la papelera?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700 transition"
                                                                title="Eliminar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                                    class="w-6 h-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>


                                            <!-- Modal Ver Usuario -->
                                            <x-modal name="view-user-{{ $user->id }}" focusable>
                                                <div class="p-6 text-left">
                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                                                        {{ __('Detalle de Usuario') }}
                                                    </h2>
                                                    <div
                                                        class="flex items-center mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                                                        <div class="flex-shrink-0 h-16 w-16">
                                                            @if ($user->profile_photo_path)
                                                                <img class="h-16 w-16 rounded-full object-cover border-2 border-white dark:border-gray-600 shadow-sm"
                                                                    src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                                    alt="{{ $user->name }}">
                                                            @else
                                                                <div
                                                                    class="h-16 w-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-500 dark:text-indigo-300 font-bold text-2xl border-2 border-white dark:border-gray-600 shadow-sm">
                                                                    {{ substr($user->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                                                {{ $user->name }} {{ $user->last_name }}
                                                            </h3>
                                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 mt-1">
                                                                {{ ucfirst($user->role === 'worker' ? 'Trabajador' : ($user->role === 'supervisor' ? 'Supervisor' : ($user->role === 'viewer' ? 'Visualizador' : 'Administrador'))) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600 dark:text-gray-300 border-t border-gray-100 dark:border-gray-700 pt-6">
                                                        <div>
                                                            <strong>RUT:</strong> {{ $user->rut ?? 'No registrado' }}
                                                        </div>
                                                        <div>
                                                            <strong>Teléfono:</strong> {{ $user->phone ?? 'No registrado' }}
                                                        </div>
                                                        <div>
                                                            <strong>Dirección:</strong>
                                                            {{ $user->address ?? 'No registrada' }}
                                                        </div>
                                                        <div>
                                                            <strong>Cargo:</strong>
                                                            {{ $user->cargo ?? 'No registrado' }}
                                                        </div>
                                                        <div>
                                                            <strong>Departamento:</strong>
                                                            {{ $user->departamento ?? 'No registrado' }}
                                                        </div>
                                                        <div>
                                                            <strong>Estado:</strong>
                                                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                                        </div>
                                                        <div>
                                                            <strong>Verificado:</strong>
                                                            {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y') : 'Pendiente' }}
                                                        </div>
                                                        <div>
                                                            <strong>Creado:</strong>
                                                            {{ $user->created_at->format('d/m/Y') }}
                                                        </div>
                                                    </div>
                                                    <div class="mt-8 flex justify-end">
                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                            {{ __('Cerrar') }}
                                                        </x-secondary-button>
                                                    </div>
                                                </div>
                                            </x-modal>

                                            <!-- Modal Editar Usuario -->
                                            <x-modal name="edit-user-{{ $user->id }}" :show="$errors->has('email') && old('user_id') == $user->id" focusable>
                                                <form method="POST" action="{{ route('users.update', $user->id) }}" class="p-6 text-left">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                                                        {{ __('Editar Usuario') }}
                                                    </h2>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <!-- Nombre -->
                                                        <div>
                                                            <x-input-label for="name_{{ $user->id }}" :value="__('Nombres')" />
                                                            <x-text-input id="name_{{ $user->id }}" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                        </div>

                                                        <!-- Apellido -->
                                                        <div>
                                                            <x-input-label for="last_name_{{ $user->id }}" :value="__('Apellidos')" />
                                                            <x-text-input id="last_name_{{ $user->id }}" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required />
                                                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                                        </div>

                                                        <!-- Email -->
                                                        <div class="col-span-2">
                                                            <x-input-label for="email_{{ $user->id }}" :value="__('Correo Electrónico')" />
                                                            <x-text-input id="email_{{ $user->id }}" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                        </div>

                                                        <!-- Rol -->
                                                        <div>
                                                            <x-input-label for="role_{{ $user->id }}" :value="__('Rol')" />
                                                            <select id="role_{{ $user->id }}" name="role" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                                <option value="worker" {{ old('role', $user->role) == 'worker' ? 'selected' : '' }}>Trabajador</option>
                                                                <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                                                <option value="viewer" {{ old('role', $user->role) == 'viewer' ? 'selected' : '' }}>Visualizador</option>
                                                            </select>
                                                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                                        </div>

                                                        <!-- Estado -->
                                                        <div>
                                                            <x-input-label for="is_active_{{ $user->id }}" :value="__('Estado')" />
                                                            <select id="is_active_{{ $user->id }}" name="is_active" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                                                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Activo</option>
                                                                <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>Inactivo</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Módulos Autorizados -->
                                                    <div class="mt-4">
                                                         <x-input-label :value="__('Módulos Autorizados')" class="mb-2" />
                                                         @php
                                                            $userModules = $user->authorized_modules ?? [];
                                                            // Si es null o vacío, y es un usuario antiguo, tal vez asumimos 'all' en el checkbox para que no pierdan acceso,
                                                            // o lo dejamos vacío si queremos forzar la selección. 
                                                            // El helper hasModuleAccess retorna true si vacío.
                                                            // Aquí marcamos 'all' si está vacío o tiene 'all'.
                                                            $allChecked = empty($userModules) || in_array('all', $userModules);
                                                         @endphp
                                                         <div class="grid grid-cols-2 gap-2">
                                                            <label class="inline-flex items-center">
                                                                <input type="checkbox" name="authorized_modules[]" value="all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                                    {{ $allChecked ? 'checked' : '' }}>
                                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Todos</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="checkbox" name="authorized_modules[]" value="vehicles" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                                    {{ in_array('vehicles', $userModules) ? 'checked' : '' }}>
                                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Vehículos</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="checkbox" name="authorized_modules[]" value="rooms" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                                    {{ in_array('rooms', $userModules) ? 'checked' : '' }}>
                                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Salas</span>
                                                            </label>
                                                            <label class="inline-flex items-center">
                                                                <input type="checkbox" name="authorized_modules[]" value="assets" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                                    {{ in_array('assets', $userModules) ? 'checked' : '' }}>
                                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Activos</span>
                                                            </label>
                                                         </div>
                                                    </div>

                                                    <div class="mt-8 flex justify-end gap-3">
                                                        <x-secondary-button x-on:click="$dispatch('close')">
                                                            {{ __('Cancelar') }}
                                                        </x-secondary-button>
                                                        <x-primary-button>
                                                            {{ __('Guardar Cambios') }}
                                                        </x-primary-button>
                                                    </div>
                                                </form>
                                            </x-modal>


                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

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

            <div class="mt-6 space-y-4">
                <!-- Nombre -->
                <div>
                    <x-input-label for="new_name" :value="__('Nombres')" />
                    <x-text-input id="new_name" name="name" type="text" class="mt-1 block w-full" :value="old('name')"
                        required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Apellido -->
                <div>
                    <x-input-label for="new_last_name" :value="__('Apellidos')" />
                    <x-text-input id="new_last_name" name="last_name" type="text" class="mt-1 block w-full"
                        :value="old('last_name')" required />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="new_email" :value="__('Correo Electrónico')" />
                    <x-text-input id="new_email" name="email" type="email" class="mt-1 block w-full"
                        :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Rol -->
                <div>
                    <x-input-label for="new_role" :value="__('Rol')" />
                    <select id="new_role" name="role"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="worker" {{ old('role') == 'worker' ? 'selected' : '' }}>Trabajador</option>
                        <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Visualizador</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Módulos Autorizados -->
                <div>
                    <x-input-label :value="__('Módulos Autorizados')" class="mb-2" />
                    <div class="grid grid-cols-2 gap-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="authorized_modules[]" value="all"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Todos</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="authorized_modules[]" value="vehicles"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Vehículos</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="authorized_modules[]" value="rooms"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Salas</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="authorized_modules[]" value="assets"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Activos</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Selecciona "Todos" para acceso completo según el rol.</p>
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="new_password" :value="__('Contraseña Inicial')" />
                    <x-text-input id="new_password" name="password" type="password" class="mt-1 block w-full" required
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="new_password_confirmation" :value="__('Confirmar Contraseña')" />
                    <x-text-input id="new_password_confirmation" name="password_confirmation" type="password"
                        class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Crear Usuario') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>