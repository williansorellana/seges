<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Personas Externas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        editingPerson: {}, 
        editAction: '',
        resetForm() {
            this.editingPerson = { name: '', rut: '', position: '', department: '' };
            this.editAction = '{{ route('external-people.store') }}';
            $dispatch('open-modal', 'external-person-modal');
        },
        editPerson(person) {
            this.editingPerson = { ...person };
            this.editAction = '{{ url('external-people') }}/' + person.id;
            $dispatch('open-modal', 'external-person-modal');
        },
        formatRut() {
            if (!this.editingPerson.rut) return;
            let value = this.editingPerson.rut.replace(/[^0-9kK]/g, '').toUpperCase();
            if (value.length > 1) {
                const dv = value.slice(-1);
                let body = value.slice(0, -1);
                body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                this.editingPerson.rut = body + '-' + dv;
            } else {
                this.editingPerson.rut = value;
            }
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Listado de Personas Frecuentes
                        </h3>
                        <div class="space-x-2">
                            <a href="{{ route('external-people.trash') }}"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Papelera
                            </a>
                            <button @click="resetForm()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Agregar Persona
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">Nombre</th>
                                    <th scope="col" class="py-3 px-6">RUT</th>
                                    <th scope="col" class="py-3 px-6">Cargo</th>
                                    <th scope="col" class="py-3 px-6">Departamento/Empresa</th>
                                    <th scope="col" class="py-3 px-6 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($people as $person)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $person->name }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $person->rut ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $person->position ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $person->department ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-6 text-right space-x-2">
                                            <button @click="editPerson({{ $person }})"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>

                                            <form action="{{ route('external-people.destroy', $person->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('¿Estás seguro de eliminar a esta persona?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                            No hay personas registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $people->links() }}
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal Create/Edit -->
        <x-modal name="external-person-modal" :show="false" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    x-text="editingPerson.id ? 'Editar Persona' : 'Nueva Persona'">
                </h2>

                <form :action="editAction" method="POST" class="mt-6">
                    @csrf
                    <template x-if="editingPerson.id">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div>
                            <x-input-label for="name" value="Nombre Completo" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                x-model="editingPerson.name" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- RUT -->
                        <div>
                            <x-input-label for="rut" value="RUT" />
                            <x-text-input id="rut" name="rut" type="text" class="mt-1 block w-full"
                                x-model="editingPerson.rut" @input="formatRut()" placeholder="12.345.678-9"
                                maxlength="12" />
                            <x-input-error class="mt-2" :messages="$errors->get('rut')" />
                        </div>

                        <!-- Cargo -->
                        <div>
                            <x-input-label for="position" value="Cargo" />
                            <x-text-input id="position" name="position" type="text" class="mt-1 block w-full"
                                x-model="editingPerson.position" />
                            <x-input-error class="mt-2" :messages="$errors->get('position')" />
                        </div>

                        <!-- Departamento/Empresa -->
                        <div>
                            <x-input-label for="department" value="Departamento / Empresa" />
                            <x-text-input id="department" name="department" type="text" class="mt-1 block w-full"
                                x-model="editingPerson.department" />
                            <x-input-error class="mt-2" :messages="$errors->get('department')" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button @click="$dispatch('close')" type="button">
                            {{ __('Cancelar') }}
                        </x-secondary-button>

                        <x-primary-button class="ml-3">
                            {{ __('Guardar') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>