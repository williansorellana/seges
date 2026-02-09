<x-app-layout>
    <div x-data="{ openDeleteModal: false, deleteAction: '', openEmptyTrashModal: false }"
        @open-empty-trash-modal.window="openEmptyTrashModal = true">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                    {{ __('Papelera de Vehículos') }}
                </h2>
                <div class="flex gap-3">
                    @if($vehicles->count() > 0)
                        <button @click="$dispatch('open-empty-trash-modal')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
                            🗑️ Vaciar Papelera
                        </button>
                    @endif
                    <a href="{{ route('vehicles.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition ease-in-out duration-150">
                        &larr; Volver al Listado
                    </a>
                </div>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-red-50 dark:bg-red-900/20">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Foto</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Patente</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Marca / Modelo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Eliminado</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($vehicles as $vehicle)
                                    <tr class="opacity-75 hover:opacity-100 transition-opacity">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="h-10 w-10 flex-shrink-0 bg-gray-600 rounded-full flex items-center justify-center overflow-hidden grayscale">
                                                @if($vehicle->image_path)
                                                    <img src="{{ Storage::url($vehicle->image_path) }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <span class="text-gray-400 text-[10px] font-bold">N/A</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-100">
                                            {{ $vehicle->plate }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-300">
                                            {{ $vehicle->brand }} {{ $vehicle->model }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            {{ $vehicle->deleted_at->diffForHumans() }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end items-center space-x-4">
                                                <form action="{{ route('vehicles.restore', $vehicle->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="text-green-400 hover:text-green-300 transition duration-150"
                                                        title="Restaurar">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>

                                                <button
                                                    @click="deleteAction = '{{ route('vehicles.force-delete', $vehicle->id) }}'; openDeleteModal = true"
                                                    class="text-red-500 hover:text-red-400 transition duration-150"
                                                    title="Eliminar Permanentemente">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
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
                                        <td colspan="5" class="px-6 py-20 text-center text-gray-500">
                                            La papelera está vacía.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmación (Estilo Mejorado) -->
            <div x-show="openDeleteModal"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6"
                style="display: none;">
                <div x-show="openDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                    @click="openDeleteModal = false">
                </div>

                <div x-show="openDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="relative bg-gray-800 rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-gray-700 z-50">
                    <div class="p-8">
                        <div class="text-center">
                            <h2 class="text-xl font-bold text-red-500 mb-4">
                                {{ __('¿Eliminar permanentemente?') }}
                            </h2>
                            <p class="text-sm text-gray-300 mb-8">
                                {{ __('Esta acción NO se puede deshacer. Se eliminará el vehículo y su historial.') }}
                            </p>
                        </div>

                        <div class="flex justify-center space-x-4">
                            <button @click="openDeleteModal = false"
                                class="px-6 py-2 bg-gray-700 text-gray-300 font-semibold rounded-lg hover:bg-gray-600 transition duration-150">
                                {{ __('CANCELAR') }}
                            </button>
                            <form :action="deleteAction" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-6 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition duration-150">
                                    {{ __('ELIMINAR DEFINITIVAMENTE') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmación para Vaciar Papelera -->
            <div x-show="openEmptyTrashModal"
                class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6"
                style="display: none;">
                <div x-show="openEmptyTrashModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                    @click="openEmptyTrashModal = false">
                </div>

                <div x-show="openEmptyTrashModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="relative bg-gray-800 rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-red-500 z-50">
                    <div class="p-8">
                        <div class="text-center">
                            <div
                                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-900/30 mb-4">
                                <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-black text-red-500 mb-4 uppercase tracking-wide">
                                {{ __('⚠️ ADVERTENCIA') }}
                            </h2>
                            <p class="text-sm text-gray-300 mb-2 font-semibold">
                                {{ __('Estás a punto de eliminar PERMANENTEMENTE todos los vehículos de la papelera.') }}
                            </p>
                            <p class="text-xs text-red-400 mb-8">
                                {{ __('Esta acción NO SE PUEDE DESHACER. Se eliminará el historial completo de todos los vehículos.') }}
                            </p>
                        </div>

                        <div class="flex justify-center space-x-4">
                            <button @click="openEmptyTrashModal = false"
                                class="px-6 py-2 bg-gray-700 text-gray-300 font-semibold rounded-lg hover:bg-gray-600 transition duration-150">
                                {{ __('CANCELAR') }}
                            </button>
                            <form action="{{ route('vehicles.empty-trash') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-6 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition duration-150">
                                    {{ __('🗑️ VACIAR PAPELERA') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>