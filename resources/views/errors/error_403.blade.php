<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Acceso denegado
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 text-center">
                <div class="text-red-500 text-6xl mb-4">403</div>

                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                    No tienes permisos para acceder a esta sección
                </h1>

                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Tu usuario no cuenta con los permisos necesarios para realizar esta acción.
                    Si crees que esto es un error, contacta al administrador del sistema.
                </p>

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition">
                    Volver al inicio
                </a>
            </div>
        </div>
    </div>
</x-app-layout>