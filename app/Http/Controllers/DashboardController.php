<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 🔹 Módulos definidos (MISMA lógica que sidebar)
        $allModules = [

            'vehicles' => [
                'name' => 'Módulo Vehículos',
                'icon' => '🚗',
                'actions' => array_filter([

                    // TODOS
                ['label' => 'Panel de Vehículos', 'route' => 'vehicles.dashboard'], //DASHBOARD GENERAL PARA TODOS para el MÓDULO VEHÍCULOS

                in_array($user->role, ['admin','supervisor','worker','driver']) ?
                    ['label' => 'Reservar Vehículos', 'route' => 'requests.create'] : null,

                in_array($user->role, ['admin','supervisor','worker','driver']) ?
                    ['label' => 'Ver tus reservas', 'route' => 'requests.index'] : null,

                    // SOLO supervisor
                    $user->role === 'supervisor'
                        ? ['label' => 'Gestión de Solicitudes', 'route' => 'requests.manage'] : null,

                    $user->role === 'supervisor'  
                        ? ['label' => 'Gestión de Vehículos', 'route' => 'vehicles.index'] : null,

                    $user->role === 'supervisor'
                        ? ['label' => 'Conductores', 'route' => 'conductores.index'] : null,

                    $user->role === 'supervisor' 
                        ? ['label' => 'Personas Externas', 'route' => 'external-people.index'] : null,

                ])
            ],

            'rooms' => [
                'name' => 'Módulo Salas',
                'icon' => '🏢',
                'actions' => array_filter([

                    // TODOS (incluye viewer)
                    ['label' => 'Agendar Sala', 'route' => 'reservations.catalog'],

                    // usuarios con reservas
                    in_array($user->role, ['admin','supervisor','worker','driver']) ? 
                        ['label' => 'Mis Reservas', 'route' => 'reservations.my_reservations'] : null,

                    // SOLO supervisor/
                    $user->role === 'supervisor' ? 
                        ['label' => 'Reserva Manual', 'route' => 'reservations.create_external'] : null,

                    $user->role === 'supervisor' ? 
                        ['label' => 'Gestionar Salas', 'route' => 'rooms.index'] : null,

                    $user->role === 'supervisor' ? 
                        ['label' => 'Gestion de Reservas', 'route' => 'rooms.agenda'] : null,

                    $user->role === 'supervisor' ? 
                        ['label' => 'Historial de Reservas', 'route' => 'rooms.history'] : null,

                ])
            ],

            'assets' => [
                'name' => 'Módulo Activos',
                'icon' => '📦',
                'actions' => array_filter([

                    // TODOS (lectura)
                    ['label' => 'Ver Activos', 'route' => 'assets.dashboard'],

                    // SOLO admin/supervisor
                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Gestionar Activos', 'route' => 'assets.index'] : null,

                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Reportes', 'route' => 'assets.reports.index'] : null,

                ])
            ],
        ];
        // 🔹 Módulos autorizados (FUENTE REAL)
        $modules = $user->authorized_modules ?? [];

        // 🔹 Filtrar módulos según usuario
        if (!in_array('all', $modules)) {
            $allModules = array_intersect_key($allModules, array_flip($modules));
        }

        // 🔹 Filtrar acciones que realmente existen
        foreach ($allModules as $key => $module) {
            $allModules[$key]['actions'] = array_filter(
                $module['actions'],
                fn($action) => \Route::has($action['route'])
            );
        }

        // 🔹 Eliminar módulos vacíos
        $allModules = array_filter($allModules, function ($module) {
            return count($module['actions']) > 0;
        });

        return view('dashboard.index', compact('user', 'allModules'));
    }
}