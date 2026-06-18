<x-app-layout>
<div x-data="{ openCreate: false, openEdit: null }" class="py-6 px-6 text-gray-100">

    <div class="max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">Categorías de Activos</h1>
                <p class="text-sm text-gray-400">Administra categorías de hardware y software.</p>
            </div>

            <button @click="openCreate = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">
                + Nueva Categoría
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-900/40 border border-green-500 text-green-200 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-900/40 border border-red-500 text-red-200 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-900/40 border border-red-500 text-red-200 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                <p class="text-sm text-gray-400">Hardware</p>
                <p class="text-3xl font-bold text-blue-400">{{ $totalHardware }}</p>
            </div>

            <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                <p class="text-sm text-gray-400">Software</p>
                <p class="text-3xl font-bold text-green-400">{{ $totalSoftware }}</p>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 mb-6">
            <form method="GET" action="{{ route('asset-categories.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar categoría..."
                    class="bg-gray-900 border-gray-700 text-gray-100 rounded-lg">

                <select name="tipo" class="bg-gray-900 border-gray-700 text-gray-100 rounded-lg">
                    <option value="">Todos los tipos</option>
                    <option value="hardware" {{ request('tipo') == 'hardware' ? 'selected' : '' }}>Hardware</option>
                    <option value="software" {{ request('tipo') == 'software' ? 'selected' : '' }}>Software</option>
                </select>

                <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
                    Filtrar
                </button>
            </form>
        </div>

        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-700 text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-700">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-700/40">
                            <td class="px-4 py-3 font-semibold">{{ $category->nombre }}</td>

                            <td class="px-4 py-3">
                                @if($category->tipo === 'hardware')
                                    <span class="px-3 py-1 rounded-full bg-blue-900/60 text-blue-300 text-xs font-bold">
                                        Hardware
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-green-900/60 text-green-300 text-xs font-bold">
                                        Software
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-300">
                                {{ $category->descripcion ?? 'Sin descripción' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button @click="openEdit = {{ $category->id }}"
                                    class="text-blue-400 hover:text-blue-300 mr-3">
                                    Editar
                                </button>

                                <form action="{{ route('asset-categories.destroy', $category) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('¿Eliminar esta categoría?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-400 hover:text-red-300">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div x-show="openEdit === {{ $category->id }}"
                            x-cloak
                            class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
                            <div class="bg-gray-800 rounded-xl p-6 w-full mx-4 border border-gray-700"
                                style="max-width: 520px;">
                                <h2 class="text-xl font-bold mb-4">Editar Categoría</h2>

                                <form method="POST" action="{{ route('asset-categories.update', $category) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4">
                                        <label class="block text-sm mb-1">Nombre</label>
                                        <input type="text" name="nombre" value="{{ $category->nombre }}"
                                            class="w-full bg-gray-900 border-gray-700 text-gray-100 rounded-lg" required>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm mb-1">Tipo</label>
                                        <select name="tipo" class="w-full bg-gray-900 border-gray-700 text-gray-100 rounded-lg" required>
                                            <option value="hardware" {{ $category->tipo == 'hardware' ? 'selected' : '' }}>Hardware</option>
                                            <option value="software" {{ $category->tipo == 'software' ? 'selected' : '' }}>Software</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm mb-1">Descripción</label>
                                        <textarea name="descripcion"
                                            class="w-full bg-gray-900 border-gray-700 text-gray-100 rounded-lg">{{ $category->descripcion }}</textarea>
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="openEdit = null"
                                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">
                                            Cancelar
                                        </button>

                                        <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold">
                                            Guardar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                No hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>

    <div x-show="openCreate"
        x-cloak
        class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
       <div class="bg-gray-800 rounded-xl p-6 w-full mx-4 border border-gray-700"
            style="max-width: 520px;">
            <h2 class="text-xl font-bold mb-4">Nueva Categoría</h2>

            <form method="POST" action="{{ route('asset-categories.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm mb-1">Nombre</label>
                    <input type="text" name="nombre"
                        class="w-full bg-gray-900 border-gray-700 text-gray-100 rounded-lg" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm mb-1">Tipo</label>
                    <select name="tipo" class="w-full bg-gray-900 border-gray-700 text-gray-100 rounded-lg" required>
                        <option value="">Seleccione...</option>
                        <option value="hardware">Hardware</option>
                        <option value="software">Software</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm mb-1">Descripción</label>
                    <textarea name="descripcion"
                        class="w-full bg-gray-900 border-gray-700 text-gray-100 rounded-lg"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="openCreate = false"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg">
                        Cancelar
                    </button>

                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-app-layout>