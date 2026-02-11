<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Papelera de Personas Externas') }}
            </h2>
            <div class="flex gap-3">
                @if($people->count() > 0)
                    <form action="{{ route('external-people.empty-trash') }}" method="POST"
                        onsubmit="return confirm('¿Estás seguro de vaciar la papelera? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Vaciar Papelera
                        </button>
                    </form>
                @endif
                <a href="{{ route('external-people.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Volver al Listado
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="bg-red-50 dark:bg-red-900/20">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Nombre</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        RUT</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Cargo</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Departamento/Empresa</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Eliminado</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Acciones</th>
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
                                        <td class="py-4 px-6">
                                            {{ $person->deleted_at->diffForHumans() }}
                                        </td>
                                        <td class="py-4 px-6 text-right space-x-2">
                                            <form action="{{ route('external-people.restore', $person->id) }}" method="POST"
                                                class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                    title="Restaurar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>

                                            <form action="{{ route('external-people.force-delete', $person->id) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('¿Estás seguro de eliminar permanentemente a esta persona? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    title="Eliminar Permanentemente">
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
                                        <td colspan="6" class="py-4 px-6 text-center text-gray-500">
                                            La papelera está vacía.
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
    </div>
</x-app-layout>