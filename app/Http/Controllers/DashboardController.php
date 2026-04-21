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
                    ['label' => 'Reservar Vehículos', 'route' => 'requests.create'],
                    ['label' => 'Ver tus reservas', 'route' => 'requests.index'],

                    // SOLO supervisor/admin
                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Panel de Vehículos', 'route' => 'vehicles.dashboard'] : null,

                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Gestión de Vehículos', 'route' => 'vehicles.index'] : null,

                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Conductores', 'route' => 'conductores.index'] : null,

                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Personas Externas', 'route' => 'external-people.index'] : null,

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

                    // SOLO supervisor/admin
                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Reserva Manual', 'route' => 'reservations.create_external'] : null,

                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Gestionar Salas', 'route' => 'rooms.index'] : null,

                    in_array($user->role, ['admin','supervisor']) ? 
                        ['label' => 'Agenda', 'route' => 'rooms.agenda'] : null,

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