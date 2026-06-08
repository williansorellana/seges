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
                'description' => 'Administra la flota, reservas, mantenimientos y combustible.',
                'theme' => 'blue',
                'icon' => '🚙',
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
                'description' => 'Reserva y gestión de disponibilidad de salas de reuniones.',
                'theme' => 'purple',
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
                'description' => 'Control de inventario, código de barras y asignaciones de equipos.',
                'theme' => 'emerald',
                'icon' => '💻',
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

            'finances' => [
                'name' => 'Módulo Finanzas',
                'description' => 'Aprobación de fondos, revisión de rendiciones y auditoría financiera.',
                'theme' => 'rose',
                'icon' => '💰',
                'actions' => array_filter([
                    in_array($user->role, ['admin', 'jefatura']) ? 
                        ['label' => 'Aprobaciones', 'route' => 'renditions.approvals'] : null,
                        
                    in_array($user->role, ['admin']) || $user->departamento === 'Finanzas' ? 
                        ['label' => 'Panel Finanzas', 'route' => 'renditions.finances'] : null,
                        
                    in_array($user->role, ['admin']) || $user->departamento === 'Controlling' ? 
                        ['label' => 'Panel Controlling', 'route' => 'renditions.controlling'] : null,

                    in_array($user->role, ['admin', 'jefatura']) || in_array($user->departamento, ['Finanzas', 'Controlling']) ? 
                        ['label' => 'Historial General', 'route' => 'renditions.history'] : null,
                ])
            ],

            'renditions' => [
                'name' => 'Módulo Rendiciones',
                'description' => 'Solicitudes de fondos, rendiciones y seguimiento de gastos personales.',
                'theme' => 'orange',
                'icon' => '📄',
                'actions' => array_filter([
                    ['label' => 'Crear Planificación', 'route' => 'route-plannings.create'],
                    ['label' => 'Mis Solicitudes', 'route' => 'route-plannings.index'],
                    ['label' => 'Mis Rendiciones', 'route' => 'renditions.index'],

                    !in_array($user->role, ['admin', 'jefatura']) && !in_array($user->departamento, ['Finanzas', 'Controlling'])
                        ? ['label' => 'Historial', 'route' => 'renditions.history']
                        : null,
                ])
            ],
        ];
        // 🔹 Filtrar módulos según usuario
        $allModules = array_filter($allModules, function ($key) use ($user) {
            if ($key === 'finances') {
                return in_array($user->role, ['admin', 'jefatura'])
                    || in_array($user->departamento, ['Finanzas', 'Controlling']);
            }

            if ($key === 'renditions') {
                return $user->hasModuleAccess('renditions')
                    || $user->role === 'admin';
            }

            return $user->hasModuleAccess($key);
        }, ARRAY_FILTER_USE_KEY);

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