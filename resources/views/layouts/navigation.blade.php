<nav x-data="{ open: false, notifyOpen: false }"
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
            </div>

            <div class="flex items-center ms-6">

                <div class="relative mr-4">
                    <button @click="notifyOpen = ! notifyOpen"
                        class="relative inline-flex items-center p-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full border-2 border-white dark:border-gray-800">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <div x-show="notifyOpen" @click.away="notifyOpen = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="fixed left-4 right-4 top-16 mt-2 sm:absolute sm:right-0 sm:left-auto sm:top-full sm:mt-2 sm:w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">

                        <div
                            class="px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center sticky top-0 z-10">

                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Notificaciones
                            </span>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.markAll') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 hover:underline transition">
                                        Marcar leídas
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-[28rem] overflow-y-auto">
                            @if(Auth::user()->notifications->isEmpty())
                                <div
                                    class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                        </path>
                                    </svg>
                                    <p>Sin notificaciones</p>
                                </div>
                            @else
                                @foreach(Auth::user()->notifications as $notification)
                                    <div
                                        class="relative border-b border-gray-100 dark:border-gray-700 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 {{ $notification->read_at ? 'opacity-75 bg-white dark:bg-gray-800' : 'bg-blue-50/50 dark:bg-blue-900/10' }}">

                                        <a href="{{ route('notifications.read', $notification->id) }}"
                                            class="block px-4 py-3 pr-10">
                                            <div class="flex items-start gap-3">

                                                <div class="flex-shrink-0 mt-1">
                                                    @if(isset($notification->data['type']) && $notification->data['type'] == 'danger')
                                                        <div class="w-2 h-2 rounded-full bg-red-500 mt-1.5"></div>
                                                    @elseif(isset($notification->data['icon']) && $notification->data['icon'] == 'check')
                                                        <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5"></div>
                                                    @else
                                                        <div class="w-2 h-2 rounded-full bg-yellow-500 mt-1.5"></div>
                                                    @endif
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <p
                                                        class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug break-words">
                                                        {{ $notification->data['message'] }}
                                                    </p>

                                                    @if(isset($notification->data['reason']))
                                                        <p class="text-xs text-red-500 mt-1 italic break-words">
                                                            "{{ $notification->data['reason'] }}"
                                                        </p>
                                                    @endif

                                                    <div class="flex items-center justify-between mt-1">
                                                        <p
                                                            class="text-[10px] text-gray-500 uppercase tracking-wide truncate pr-2">
                                                            @if(isset($notification->data['plate']))
                                                                {{ $notification->data['brand_model'] }}
                                                            @else
                                                                Sistema de Reservas
                                                            @endif
                                                        </p>
                                                        <p class="text-[10px] text-gray-400 whitespace-nowrap">
                                                            {{ $notification->created_at->diffForHumans(null, true, true) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <div class="absolute top-2 right-2">
                                            <form method="POST"
                                                action="{{ route('notifications.destroy', $notification->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 text-gray-300 hover:text-red-500 transition-colors"
                                                    title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            @if (Auth::user()->profile_photo_path)
                                <img class="h-8 w-8 rounded-full object-cover mr-2"
                                    src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                                    alt="{{ Auth::user()->name }}" />
                            @else
                                <div
                                    class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold mr-2">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <div>{{ Auth::user()->short_name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="mobileSidebarOpen = ! mobileSidebarOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileSidebarOpen, 'inline-flex': ! mobileSidebarOpen }"
                            class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! mobileSidebarOpen, 'inline-flex': mobileSidebarOpen }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>