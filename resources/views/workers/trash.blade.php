<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Papelera de Trabajadores') }}
            </h2>
            <a href="{{ route('workers.index') }}"
                class="inline-flex items-center px-4 py-2 bg-slate-600 border border-transparent rounded-lg font-semibold text-xs text-white hover:bg-slate-500 transition-all hover:-translate-y-0.5 duration-150 shadow-md shadow-slate-500/20 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ forceDeleteAction: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-red-50 dark:bg-red-900/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Nombre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                RUT
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Departamento
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Fecha Eliminación
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($workers as $worker)
                            <tr
                                class="opacity-75 hover:opacity-100 transition-opacity bg-white dark:bg-gray-800 border-b border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-100">{{ $worker->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $worker->rut }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $worker->departamento ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-300">
                                    {{ $worker->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center space-x-3">

                                        <!-- Restaurar -->
                                        <form action="{{ route('workers.restore', $worker->id) }}" method="POST"
                                            class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="text-green-400 hover:text-green-300 transition duration-150"
                                                title="Restaurar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>

                                        <!-- Eliminar Permanente -->
                                        <button @click="
                                                forceDeleteAction = '{{ route('workers.force-delete', $worker->id) }}';
                                                $dispatch('open-modal', 'confirm-force-delete-modal');
                                            " class="text-red-600 hover:text-red-500 transition duration-150"
                                            title="Eliminar Permanentemente">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-gray-500">
                                    La papelera está vacía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $workers->links() }}
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <x-modal name="confirm-force-delete-modal" focusable>
            <form method="POST" :action="forceDeleteAction" class="p-6 bg-gray-800 text-gray-100">
                @csrf
                @method('DELETE')

                <h2 class="text-lg font-medium text-gray-100 text-red-500">
                    {{ __('¿Eliminar Definitivamente?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-300">
                    {{ __('Esta acción no se puede deshacer. Todos los datos asociados a este trabajador se eliminarán permanentemente.') }}
                </p>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')"
                        class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-slate-500/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Cancelar
                    </button>

                    <button type="submit"
                        class="ml-3 px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-md shadow-rose-500/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                        Eliminar Permanentemente
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>