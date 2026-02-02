<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetWriteOff;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetReportController extends Controller
{
    /**
     * Display report dashboard.
     */
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // 1. Most Used Assets
        $mostUsedAssetsQuery = AssetAssignment::select('activo_id', DB::raw('count(*) as total'))
            ->groupBy('activo_id')
            ->orderByDesc('total')
            ->with([
                'asset' => function ($q) {
                    $q->withTrashed();
                }
            ])
            ->limit(10);

        if ($startDate) {
            $mostUsedAssetsQuery->whereDate('fecha_entrega', '>=', $startDate);
        }
        if ($endDate) {
            $mostUsedAssetsQuery->whereDate('fecha_entrega', '<=', $endDate);
        }
        $mostUsedAssets = $mostUsedAssetsQuery->get();

        // 2. Top Users (Internal)
        $topUsersQuery = AssetAssignment::select('usuario_id', DB::raw('count(*) as total'))
            ->whereNotNull('usuario_id')
            ->groupBy('usuario_id')
            ->orderByDesc('total')
            ->with('user')
            ->limit(10);

        if ($startDate) {
            $topUsersQuery->whereDate('fecha_entrega', '>=', $startDate);
        }
        if ($endDate) {
            $topUsersQuery->whereDate('fecha_entrega', '<=', $endDate);
        }
        $topUsers = $topUsersQuery->get();

        // 3. Top Workers (External)
        $topWorkersQuery = AssetAssignment::select('worker_id', DB::raw('count(*) as total'))
            ->whereNotNull('worker_id')
            ->groupBy('worker_id')
            ->orderByDesc('total')
            ->with('worker')
            ->limit(10);

        if ($startDate) {
            $topWorkersQuery->whereDate('fecha_entrega', '>=', $startDate);
        }
        if ($endDate) {
            $topWorkersQuery->whereDate('fecha_entrega', '<=', $endDate);
        }
        $topWorkers = $topWorkersQuery->get();

        // 4. Write Offs
        $writeOffsQuery = AssetWriteOff::with([
            'asset' => function ($q) {
                $q->withTrashed();
            },
            'user'
        ])
            ->orderBy('fecha', 'desc');

        if ($startDate) {
            $writeOffsQuery->whereDate('fecha', '>=', $startDate);
        }
        if ($endDate) {
            $writeOffsQuery->whereDate('fecha', '<=', $endDate);
        }
        $writeOffs = $writeOffsQuery->get();

        return view('assets.reports.index', compact(
            'mostUsedAssets',
            'topUsers',
            'topWorkers',
            'writeOffs'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type'); // most_used, top_users, write_offs

        $generatedDate = now()->format('d/m/Y H:i');

        // Determinar filtros aplicados
        $filtrosAplicados = [];
        if ($startDate) {
            $filtrosAplicados[] = "Desde: " . \Carbon\Carbon::parse($startDate)->format('d/m/Y');
        }
        if ($endDate) {
            $filtrosAplicados[] = "Hasta: " . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
        }

        $data = [];
        $view = '';
        $filename = 'reporte';

        if ($type === 'most_used') {
            $query = AssetAssignment::select('activo_id', DB::raw('count(*) as total'))
                ->groupBy('activo_id')
                ->orderByDesc('total')
                ->with([
                    'asset' => function ($q) {
                        $q->withTrashed();
                    }
                ]);

            if ($startDate)
                $query->whereDate('fecha_entrega', '>=', $startDate);
            if ($endDate)
                $query->whereDate('fecha_entrega', '<=', $endDate);

            $items = $query->limit(20)->get();

            // Calcular estadísticas
            $totalActivos = $items->count();
            $totalAsignaciones = $items->sum('total');
            $promedioAsignaciones = $totalActivos > 0 ? round($totalAsignaciones / $totalActivos, 1) : 0;
            $topActivo = $items->first();

            $data = compact(
                'items',
                'generatedDate',
                'filtrosAplicados',
                'totalActivos',
                'totalAsignaciones',
                'promedioAsignaciones',
                'topActivo',
                'startDate',
                'endDate'
            );

            $view = 'assets.reports.pdf.most-used';
            $filename = 'reporte-activos-mas-utilizados';

        } elseif ($type === 'top_users') {
            // Internal
            $queryUsers = AssetAssignment::select('usuario_id', DB::raw('count(*) as total'))
                ->whereNotNull('usuario_id')
                ->groupBy('usuario_id')
                ->orderByDesc('total')
                ->with('user');

            // External
            $queryWorkers = AssetAssignment::select('worker_id', DB::raw('count(*) as total'))
                ->whereNotNull('worker_id')
                ->groupBy('worker_id')
                ->orderByDesc('total')
                ->with('worker');

            if ($startDate) {
                $queryUsers->whereDate('fecha_entrega', '>=', $startDate);
                $queryWorkers->whereDate('fecha_entrega', '>=', $startDate);
            }
            if ($endDate) {
                $queryUsers->whereDate('fecha_entrega', '<=', $endDate);
                $queryWorkers->whereDate('fecha_entrega', '<=', $endDate);
            }

            $users = $queryUsers->limit(15)->get();
            $workers = $queryWorkers->limit(15)->get();

            // Calcular estadísticas
            $totalUsuarios = $users->count();
            $totalTrabajadores = $workers->count();
            $totalAsignacionesUsuarios = $users->sum('total');
            $totalAsignacionesTrabajadores = $workers->sum('total');
            $totalAsignaciones = $totalAsignacionesUsuarios + $totalAsignacionesTrabajadores;
            $topUsuario = $users->first();
            $topTrabajador = $workers->first();

            $data = compact(
                'users',
                'workers',
                'generatedDate',
                'filtrosAplicados',
                'totalUsuarios',
                'totalTrabajadores',
                'totalAsignacionesUsuarios',
                'totalAsignacionesTrabajadores',
                'totalAsignaciones',
                'topUsuario',
                'topTrabajador',
                'startDate',
                'endDate'
            );

            $view = 'assets.reports.pdf.top-users';
            $filename = 'reporte-usuarios-mas-asignaciones';

        } elseif ($type === 'write_offs') {
            $query = AssetWriteOff::with([
                'asset' => function ($q) {
                    $q->withTrashed();
                },
                'user'
            ])
                ->orderBy('fecha', 'desc');

            if ($startDate)
                $query->whereDate('fecha', '>=', $startDate);
            if ($endDate)
                $query->whereDate('fecha', '<=', $endDate);

            $items = $query->get();

            // Calcular estadísticas
            $totalBajas = $items->count();
            $valorTotal = $items->sum(function ($item) {
                return $item->asset->precio ?? 0;
            });

            // Motivo más frecuente
            $motivosAgrupados = $items->groupBy('motivo')->map(function ($group) {
                return $group->count();
            })->sortDesc();
            $motivoPrincipal = $motivosAgrupados->keys()->first();
            $casosPrincipal = $motivosAgrupados->first() ?? 0;

            $data = compact(
                'items',
                'generatedDate',
                'filtrosAplicados',
                'totalBajas',
                'valorTotal',
                'motivoPrincipal',
                'casosPrincipal',
                'startDate',
                'endDate'
            );

            $view = 'assets.reports.pdf.write-offs';
            $filename = 'reporte-activos-baja';
        } else {
            return back()->with('error', 'Tipo de reporte inválido.');
        }

        $pdf = Pdf::loadView($view, $data);
        return $pdf->download($filename . '-' . now()->format('dmY') . '.pdf');
    }
}
