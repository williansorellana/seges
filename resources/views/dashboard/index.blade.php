<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Bienvenido {{ $user->name }} {{ $user->last_name ?? '' }} 👋
        </h2>
        <p class="text-sm text-gray-400">
            Aquí tienes un resumen de tus módulos disponibles
        </p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($allModules as $module)
                    <div class="bg-gray-800 rounded-2xl p-6 shadow-md hover:shadow-xl hover:scale-[1.02] transition">

                        <!-- Título -->
                        <div class="flex items-center gap-2 mb-4 text-white font-semibold text-lg">
                            <span class="text-xl">{{ $module['icon'] }}</span>
                            <span>{{ $module['name'] }}</span>
                        </div>

                        <!-- Acciones -->
                        <ul class="space-y-2 text-gray-300 text-sm">
                            @foreach($module['actions'] as $action)
                                <li>
                                    <a href="{{ route($action['route']) }}"
                                       class="hover:text-white transition">
                                        → {{ $action['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                @endforeach

            </div>

        </div>
    </div>

</x-app-layout>