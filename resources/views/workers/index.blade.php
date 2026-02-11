<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Trabajadores Externos') }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('workers.trash') }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Papelera
                </a>
                <button x-data="" @click="$dispatch('open-modal', 'create-worker-modal')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    Nuevo Trabajador
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        editingWorker: {}, 
        editAction: '', 
        deleteAction: '',
        createWorker: { rut: '' },
        formatRut(target) {
            if (!target.rut) return;
            let value = target.rut.replace(/[^0-9kK]/g, '').toUpperCase();
            if (value.length > 1) {
                const dv = value.slice(-1);
                let body = value.slice(0, -1);
                body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                target.rut = body + '-' + dv;
            } else {
                target.rut = value;
            }
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="px-6 py-3 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        RUT</th>
                                    <th
                                        class="px-6 py-3 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Departamento</th>
                                    <th
                                        class="px-6 py-3 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Cargo</th>
                                    <th
                                        class="px-6 py-3 bg-gray-900 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-800 divide-y divide-gray-700">
                                @forelse($workers as $worker)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-100">{{ $worker->nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $worker->rut }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $worker->departamento ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            {{ $worker->cargo ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end items-center space-x-3">
                                                <button @click="
                                                                    editingWorker = {{ $worker }};
                                                                    editAction = '{{ route('workers.update', $worker->id) }}';
                                                                    $dispatch('open-modal', 'edit-worker-modal');
                                                                " class="text-indigo-400 hover:text-indigo-300"
                                                    title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <button @click="
                                                                deleteAction = '{{ route('workers.destroy', $worker->id) }}';
                                                                $dispatch('open-modal', 'confirm-worker-deletion');
                                                            " class="text-red-400 hover:text-red-300" title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-400">No hay trabajadores
                                            registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $workers->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <x-modal name="confirm-worker-deletion" focusable>
            <form method="POST" :action="deleteAction" class="p-6 bg-gray-800 text-gray-100">
                @csrf
                @method('DELETE')

                <h2 class="text-lg font-medium text-gray-100">
                    {{ __('¿Estás seguro de que quieres eliminar este trabajador?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-300">
                    {{ __('Una vez eliminado, se moverá a la papelera donde podrás restaurarlo si es necesario.') }}
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')"
                        class="mr-3 bg-gray-700 text-gray-300 border-gray-600">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-danger-button class="ml-3">
                        {{ __('Eliminar Trabajador') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>

        <!-- Create Modal -->
        <x-modal name="create-worker-modal" :show="$errors->has('rut_create')" focusable>
            <form method="POST" action="{{ route('workers.store') }}" class="p-6 bg-gray-800 text-gray-100">
                @csrf
                <h2 class="text-lg font-medium text-gray-100 mb-4">Nuevo Trabajador</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="nombre" value="Nombre Completo" class="text-gray-300" />
                        <x-text-input id="nombre" name="nombre" type="text"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100" required />
                    </div>
                    <div>
                        <x-input-label for="rut" value="RUT" class="text-gray-300" />
                        <x-text-input id="rut" name="rut" type="text"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100"
                            x-model="createWorker.rut" @input="formatRut(createWorker)" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="departamento" value="Departamento" class="text-gray-300" />
                            <x-text-input id="departamento" name="departamento" type="text"
                                class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100" />
                        </div>
                        <div>
                            <x-input-label for="cargo" value="Cargo" class="text-gray-300" />
                            <x-text-input id="cargo" name="cargo" type="text"
                                class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button @click="$dispatch('close')"
                        class="mr-3 bg-gray-700 text-gray-300 border-gray-600">Cancelar</x-secondary-button>
                    <x-primary-button
                        class="bg-blue-600 border-transparent hover:bg-blue-500">Guardar</x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- Edit Modal -->
        <x-modal name="edit-worker-modal" :show="$errors->has('rut_edit')" focusable>
            <form method="POST" :action="editAction" class="p-6 bg-gray-800 text-gray-100">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-medium text-gray-100 mb-4">Editar Trabajador</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="edit_nombre" value="Nombre Completo" class="text-gray-300" />
                        <x-text-input id="edit_nombre" name="nombre" type="text"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100"
                            x-model="editingWorker.nombre" required />
                    </div>
                    <div>
                        <x-input-label for="edit_rut" value="RUT" class="text-gray-300" />
                        <x-text-input id="edit_rut" name="rut" type="text"
                            class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100"
                            x-model="editingWorker.rut" @input="formatRut(editingWorker)" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_departamento" value="Departamento" class="text-gray-300" />
                            <x-text-input id="edit_departamento" name="departamento" type="text"
                                class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100"
                                x-model="editingWorker.departamento" />
                        </div>
                        <div>
                            <x-input-label for="edit_cargo" value="Cargo" class="text-gray-300" />
                            <x-text-input id="edit_cargo" name="cargo" type="text"
                                class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100"
                                x-model="editingWorker.cargo" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button @click="$dispatch('close')"
                        class="mr-3 bg-gray-700 text-gray-300 border-gray-600">Cancelar</x-secondary-button>
                    <x-primary-button
                        class="bg-blue-600 border-transparent hover:bg-blue-500">Actualizar</x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>