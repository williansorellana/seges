<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Trabajadores Externos') }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('workers.trash') }}"
                    class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-xs text-white hover:bg-rose-500 transition-all hover:-translate-y-0.5 duration-150 shadow-md shadow-rose-500/20 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Papelera
                </a>
                <button x-data="" @click="$dispatch('open-modal', 'create-worker-modal')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white hover:bg-blue-500 transition-all hover:-translate-y-0.5 duration-150 shadow-md shadow-blue-500/20 cursor-pointer">
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
                    <button type="button" x-on:click="$dispatch('close')"
                        class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-slate-500/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Cancelar
                    </button>

                    <button type="submit"
                        class="ml-3 px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-rose-500/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Eliminar Trabajador
                    </button>
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
                        <x-input-label for="nombre" :value="__('Nombre Completo')" class="mb-1" />
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                            <input type="text" id="nombre" name="nombre" required placeholder="Ej: Juan Pérez" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <x-input-label for="rut" :value="__('RUT')" class="mb-1" />
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                            <input type="text" id="rut" name="rut" x-model="createWorker.rut" @input="formatRut(createWorker)" required placeholder="Ej: 12.345.678-9" maxlength="12" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="departamento" :value="__('Departamento')" class="mb-1" />
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <input type="text" id="departamento" name="departamento" placeholder="Ej: Operaciones" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <x-input-label for="cargo" :value="__('Cargo')" class="mb-1" />
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <input type="text" id="cargo" name="cargo" placeholder="Ej: Supervisor" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" @click="$dispatch('close')"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-rose-500/20 transition-all hover:-translate-y-0.5 cursor-pointer mr-3">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-blue-500/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Guardar
                    </button>
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
                        <x-input-label for="edit_nombre" :value="__('Nombre Completo')" class="mb-1" />
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                            <input type="text" id="edit_nombre" name="nombre" x-model="editingWorker.nombre" required placeholder="Ej: Juan Pérez" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <x-input-label for="edit_rut" :value="__('RUT')" class="mb-1" />
                        <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                            <input type="text" id="edit_rut" name="rut" x-model="editingWorker.rut" @input="formatRut(editingWorker)" required placeholder="Ej: 12.345.678-9" maxlength="12" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="edit_departamento" :value="__('Departamento')" class="mb-1" />
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <input type="text" id="edit_departamento" name="departamento" x-model="editingWorker.departamento" placeholder="Ej: Operaciones" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <x-input-label for="edit_cargo" :value="__('Cargo')" class="mb-1" />
                            <div class="flex items-center border border-slate-700 rounded-lg bg-[#1e293b] px-3 py-2.5 focus-within:border-blue-500 focus-within:bg-[#0f172a] hover:border-slate-600 transition-colors w-full">
                                <input type="text" id="edit_cargo" name="cargo" x-model="editingWorker.cargo" placeholder="Ej: Supervisor" class="w-full bg-transparent border-none outline-none text-slate-100 placeholder-slate-500 text-sm focus:ring-0 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" @click="$dispatch('close')"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-rose-500/20 transition-all hover:-translate-y-0.5 cursor-pointer mr-3">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-blue-500/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Actualizar
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>