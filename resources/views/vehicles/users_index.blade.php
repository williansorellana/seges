<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Historial de Uso por Usuario') }}
            </h2>
            <a href="{{ route('vehicles.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-600 transition">
                Volver a Gestión de Vehículos
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-100">
                <div class="mb-6">
                    <input type="text" id="searchInput" placeholder="Buscar usuario o conductor..." class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($users as $user)
                        <a href="{{ route('vehicles.user-usage-history', $user->id) }}" class="user-card block bg-gray-700 hover:bg-gray-600 p-4 rounded-lg border border-gray-600 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center font-bold text-white uppercase">{{ substr($user->name, 0, 1) }}</div>
                                <div>
                                    <h4 class="font-bold text-white user-name">{{ $user->name }}</h4>
                                    <p class="text-xs text-gray-400">Usuario Sistema</p>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    @foreach($workers as $worker)
                        <a href="{{ route('vehicles.worker-usage-history', $worker->id) }}" class="user-card block bg-gray-700 hover:bg-gray-600 p-4 rounded-lg border border-gray-600 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center font-bold text-white uppercase">{{ substr($worker->nombre, 0, 1) }}</div>
                                <div>
                                    <h4 class="font-bold text-white user-name">{{ $worker->nombre }}</h4>
                                    <p class="text-xs text-gray-400">Conductor Externo</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.user-card').forEach(card => {
                const name = card.querySelector('.user-name').textContent.toLowerCase();
                card.style.display = name.includes(term) ? 'block' : 'none';
            });
        });
    </script>
</x-app-layout>