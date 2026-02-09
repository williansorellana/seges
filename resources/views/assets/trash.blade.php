<x-app-layout>
    <div x-data="{ restoreAction: '', forceDeleteAction: '', openEmptyTrashModal: false }"
        @open-empty-trash-modal.window="openEmptyTrashModal = true">
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Papelera de Activos') }}
                </h2>
                <div class="flex gap-2">
                    @if($assets->count() > 0)
                        <button @click="$dispatch('open-empty-trash-modal')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9">
                            🗑️ Vaciar Papelera
                        </button>
                    @endif
                    <a href="{{ route('assets.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 h-9">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-red-50 dark:bg-red-900/20">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Foto
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Código
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Nombre
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Categoría
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Eliminado
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($assets as $asset)
                                    <tr
                                        class="opacity-75 hover:opacity-100 transition-opacity bg-white dark:bg-gray-800 border-b border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100">
                                        <td class="px-5 py-4 text-sm">
                                            @if($asset->foto_path)
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full object-cover border border-gray-600"
                                                        src="{{ Storage::url($asset->foto_path) }}" alt="{{ $asset->nombre }}">
                                                </div>
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-400 border border-gray-600">
                                                    N/A
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-sm font-bold">
                                            {{ $asset->codigo_interno }}
                                        </td>
                                        <td class="px-5 py-4 text-sm">
                                            {{ $asset->nombre }}
                                        </td>
                                        <td class="px-5 py-4 text-sm">
                                            {{ $asset->category->nombre ?? 'Sin categoría' }}
                                        </td>
                                        <td class="px-5 py-4 text-sm">
                                            {{ $asset->deleted_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-5 py-4 text-sm font-medium">
                                            <div class="flex items-center space-x-4">
                                                <!-- Restaurar -->
                                                <button
                                                    @click="$dispatch('open-modal', 'restore-asset-modal'); restoreAction = '{{ route('assets.restore', $asset->id) }}'"
                                                    class="text-green-400 hover:text-green-300 transition duration-150"
                                                    title="Restaurar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <!-- Eliminar Permanentemente -->
                                                <button
                                                    @click="$dispatch('open-modal', 'force-delete-asset-modal'); forceDeleteAction = '{{ route('assets.force-delete', $asset->id) }}'"
                                                    class="text-red-400 hover:text-red-300 transition duration-150"
                                                    title="Eliminar Permanentemente">
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
                                        <td colspan="6" class="px-5 py-5 text-sm text-center text-gray-500">
                                            La papelera está vacía.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- Modal Restaurar -->
            <x-modal name="restore-asset-modal" :show="false" focusable>
                <div class="p-6 bg-gray-800 text-gray-100">
                    <h2 class="text-lg font-medium text-gray-100">
                        {{ __('Restaurar Activo') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('¿Estás seguro que deseas restaurar este activo? Volverá a estar disponible en el inventario.') }}
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <x-secondary-button @click="$dispatch('close')"
                            class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                            {{ __('Cancelar') }}
                        </x-secondary-button>
                        <form method="POST" :action="restoreAction">
                            @csrf
                            @method('PUT')
                            <x-primary-button class="ml-3 bg-green-600 hover:bg-green-700 border-transparent">
                                {{ __('Restaurar') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Eliminar Permanentemente -->
            <x-modal name="force-delete-asset-modal" :show="false" focusable>
                <div class="p-6 bg-gray-800 text-gray-100">
                    <h2 class="text-lg font-medium text-gray-100">
                        {{ __('¿Estás seguro?') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-400">
                        {{ __('Esta acción es irreversible. El activo se eliminará permanentemente del sistema.') }}
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <x-secondary-button @click="$dispatch('close')"
                            class="bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600">
                            {{ __('Cancelar') }}
                        </x-secondary-button>
                        <form method="POST" :action="forceDeleteAction">
                            @csrf
                            @method('DELETE')
                            <x-danger-button class="ml-3">
                                {{ __('Eliminar Permanentemente') }}
                            </x-danger-button>
                        </form>
                    </div>
                </div>
            </x-modal>
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
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-900/30 mb-4">
                            <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-black text-red-500 mb-4 uppercase tracking-wide">
                            {{ __('⚠️ ADVERTENCIA') }}
                        </h2>
                        <p class="text-sm text-gray-300 mb-2 font-semibold">
                            {{ __('Estás a punto de eliminar PERMANENTEMENTE todos los activos de la papelera.') }}
                        </p>
                        <p class="text-xs text-red-400 mb-8">
                            {{ __('Esta acción NO SE PUEDE DESHACER. Se eliminará el historial completo de todos los activos.') }}
                        </p>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button @click="openEmptyTrashModal = false"
                            class="px-6 py-2 bg-gray-700 text-gray-300 font-semibold rounded-lg hover:bg-gray-600 transition duration-150">
                            {{ __('CANCELAR') }}
                        </button>
                        <form action="{{ route('assets.empty-trash') }}" method="POST">
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