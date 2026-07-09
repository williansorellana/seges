@php
    $themes = [
        'blue' => [
            'iconBg' => 'bg-blue-500/10 text-blue-500 dark:bg-blue-500/20 dark:text-blue-400',
            'iconColor' => 'text-blue-500 dark:text-blue-400', 
            'hoverBorder' => 'group-hover:border-blue-500/30',
            'topGlow' => 'from-transparent via-blue-500 to-transparent'
        ],
        'purple' => [
            'iconBg' => 'bg-purple-500/10 text-purple-500 dark:bg-purple-500/20 dark:text-purple-400', 
            'iconColor' => 'text-purple-500 dark:text-purple-400', 
            'hoverBorder' => 'group-hover:border-purple-500/30',
            'topGlow' => 'from-transparent via-purple-500 to-transparent'
        ],
        'emerald' => [
            'iconBg' => 'bg-emerald-500/10 text-emerald-500 dark:bg-emerald-500/20 dark:text-emerald-400', 
            'iconColor' => 'text-emerald-500 dark:text-emerald-400', 
            'hoverBorder' => 'group-hover:border-emerald-500/30',
            'topGlow' => 'from-transparent via-emerald-500 to-transparent'
        ],
        'orange' => [
            'iconBg' => 'bg-orange-500/10 text-orange-500 dark:bg-orange-500/20 dark:text-orange-400', 
            'iconColor' => 'text-orange-500 dark:text-orange-400', 
            'hoverBorder' => 'group-hover:border-orange-500/30',
            'topGlow' => 'from-transparent via-orange-500 to-transparent'
        ],
        'amber' => [
            'iconBg' => 'bg-amber-500/10 text-amber-500 dark:bg-amber-500/20 dark:text-amber-400', 
            'iconColor' => 'text-amber-500 dark:text-amber-400', 
            'hoverBorder' => 'group-hover:border-amber-500/30',
            'topGlow' => 'from-transparent via-amber-500 to-transparent'
        ],
        'rose' => [
            'iconBg' => 'bg-rose-500/10 text-rose-500 dark:bg-rose-500/20 dark:text-rose-400', 
            'iconColor' => 'text-rose-500 dark:text-rose-400', 
            'hoverBorder' => 'group-hover:border-rose-500/30',
            'topGlow' => 'from-transparent via-rose-500 to-transparent'
        ],
    ];
@endphp

<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-1 py-2">
            <h2 class="font-bold text-2xl md:text-3xl text-gray-800 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                Bienvenido, {{ $user->name }} {{ $user->last_name ?? '' }} 
                <span class="animate-wave inline-block origin-[70%_70%]">👋</span>
            </h2>
            <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">
                Aquí tienes un resumen de tus módulos y herramientas disponibles.
            </p>
        </div>
    </x-slot>

    <div class="py-12 relative overflow-hidden min-h-[70vh]">
        <!-- Background decorative elements -->
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-[100px] -z-10 pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-purple-500/5 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach($allModules as $module)
                    @php 
                        $theme = $themes[$module['theme'] ?? 'blue']; 
                    @endphp
                    
                    <div class="group relative bg-white dark:bg-[#151821] rounded-3xl p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 dark:border-gray-800/60 {{ $theme['hoverBorder'] }} overflow-hidden flex flex-col h-full z-10">
                        
                        <!-- Top Gradient glow -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r {{ $theme['topGlow'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <!-- Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $theme['iconBg'] }} text-3xl shadow-inner transition-transform group-hover:scale-110 duration-300">
                                    {{ $module['icon'] }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">{{ $module['name'] }}</h3>
                                    @if(isset($module['description']))
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-relaxed line-clamp-2">{{ $module['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-700/50 to-transparent mb-5"></div>

                        <!-- Acciones -->
                        <div class="flex-grow">
                            <h4 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3 px-1">Acciones Rápidas</h4>
                            <ul class="space-y-1.5">
                                @foreach($module['actions'] as $action)
                                    <li>
                                        <a href="{{ route($action['route']) }}"
                                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all group/link">
                                            
                                            <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-gray-100 dark:bg-gray-800 group-hover/link:bg-white dark:group-hover/link:bg-gray-700 shadow-sm transition-colors">
                                                <svg class="w-3.5 h-3.5 text-gray-400 group-hover/link:{{ $theme['iconColor'] }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </span>
                                            
                                            {{ $action['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>
    </div>

    <style>
        @keyframes wave-animation {
            0% { transform: rotate( 0.0deg) }
            10% { transform: rotate(14.0deg) }
            20% { transform: rotate(-8.0deg) }
            30% { transform: rotate(14.0deg) }
            40% { transform: rotate(-4.0deg) }
            50% { transform: rotate(10.0deg) }
            60% { transform: rotate( 0.0deg) }
            100% { transform: rotate( 0.0deg) }
        }
        .animate-wave {
            animation: wave-animation 2.5s infinite;
        }
    </style>
</x-app-layout>