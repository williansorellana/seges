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
                'description' => 'Reserva y gestión de disponibilidad de salas de reuniones.',
                'theme' => 'purple',
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

            'renditions' => [
                'name' => 'Módulo Rendiciones',
                'description' => 'Solicitudes, rendiciones, aprobaciones y auditoría, según tu rol.',
                'theme' => 'orange',
                'icon' => '📄',
                'actions' => array_filter([
                    $user->role === 'worker' ? ['label' => 'Crear Planificación', 'route' => 'route-plannings.create'] : null,
                    ['label' => 'Mis Solicitudes', 'route' => 'route-plannings.index'],
                    ['label' => 'Mis Rendiciones', 'route' => 'renditions.index'],
                    $user->role === 'jefatura' ? ['label' => 'Aprobaciones jefatura', 'route' => 'renditions.approvals'] : null,
                    $user->role === 'controlling' ? ['label' => 'Panel Controlling', 'route' => 'renditions.controlling'] : null,
                    $user->role === 'finances' ? ['label' => 'Panel Finanzas', 'route' => 'renditions.finances'] : null,
                    in_array($user->role, ['worker', 'jefatura', 'controlling', 'finances'], true)
                        ? ['label' => 'Historial', 'route' => 'renditions.history'] : null,
                    in_array($user->role, ['controlling', 'finances'], true)
                        ? ['label' => 'Reportes Rendiciones', 'route' => 'renditions.reports'] : null,
                ])
            ],
        ];
        // 🔹 Filtrar módulos según usuario
        $allModules = array_filter($allModules, function ($key) use ($user) {
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
