<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Models\Worker;
use App\Notifications\AssetConditionNotification;
use Illuminate\Support\Facades\Notification;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AssetController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function dashboard(Request $request)
    {
        $query = Asset::with(['category', 'assignments.user', 'assignments.worker', 'maintenances', 'writeOff']);

        // Filtro de Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('codigo_interno', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%")
                    ->orWhere('codigo_barra', 'like', "%{$search}%");
            });
        }

        // Filtro de Estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        // Filtro de Categoría
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->input('categoria'));
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        // Conteos para tarjetas
        $countDisponible = Asset::where('estado', 'available')->count();
        $countAsignado = Asset::where('estado', 'assigned')->count();
        $countMantenimiento = Asset::where('estado', 'maintenance')->count();
        $countBaja = Asset::where('estado', 'written_off')->count();

        $categories = AssetCategory::all();

        return view('assets.dashboard', compact('assets', 'countDisponible', 'countAsignado', 'countMantenimiento', 'countBaja', 'categories'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'assignments.user', 'assignments.worker', 'maintenances', 'writeOff']);

        // Filtro de Búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('codigo_interno', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%")
                    ->orWhere('codigo_barra', 'like', "%{$search}%");
            });
        }

        // Filtro de Estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        // Filtro de Categoría
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->input('categoria'));
        }

        // Conteos para tarjetas
        $totalAssets = Asset::count();
        $countDisponible = Asset::where('estado', 'available')->count();
        $countAsignado = Asset::where('estado', 'assigned')->count();
        $countMantenimiento = Asset::where('estado', 'maintenance')->count();
        $countBaja = Asset::where('estado', 'written_off')->count();

        $assets = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = AssetCategory::all();
        $users = User::all(); // Para el modal de asignación
        $workers = Worker::orderBy('nombre')->get(); // Para el modal de asignación

        return view('assets.index', compact(
            'assets',
            'categories',
            'users',
            'workers',
            'totalAssets',
            'countDisponible',
            'countAsignado',
            'countMantenimiento',
            'countBaja'
        ));
    }

    /**
     * Exporta el inventario a PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Asset::with(['category', 'assignments.user', 'assignments.worker', 'maintenances', 'writeOff']);

        // Filtro de Búsqueda
        $searchFilter = null;
        if ($request->filled('search')) {
            $search = $request->input('search');
            $searchFilter = $search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_interno', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%")
                    ->orWhere('codigo_barra', 'like', "%{$search}%");
            });
        }

        // Filtro de Estado
        // El parámetro 'export_filter' tiene prioridad sobre 'estado' del sidebar
        $estadoFilter = null;
        if ($request->filled('export_filter') && $request->export_filter !== 'all') {
            $estadoFilter = $request->export_filter;
            $query->where('estado', $request->export_filter);
        } elseif ($request->filled('estado')) {
            $estadoFilter = $request->estado;
            $query->where('estado', $request->input('estado'));
        }

        // Filtro de Categoría
        $categoriaFilter = null;
        if ($request->filled('categoria')) {
            $categoriaFilter = $request->input('categoria');
            $query->where('categoria_id', $request->input('categoria'));
        }

        // Obtener TODOS los registros (sin paginación)
        $assets = $query->orderBy('created_at', 'desc')->get();

        // Calcular estadísticas
        $totalActivos = $assets->count();
        $totalDisponibles = $assets->where('estado', 'available')->count();
        $totalAsignados = $assets->where('estado', 'assigned')->count();
        $totalMantenimiento = $assets->where('estado', 'maintenance')->count();
        $totalBaja = $assets->where('estado', 'written_off')->count();

        // Calcular valor total referencial
        $valorTotal = $assets->sum('valor_referencial');

        // Determinar filtros aplicados para mostrar en PDF
        $filtrosAplicados = [];
        if ($searchFilter) {
            $filtrosAplicados[] = "Búsqueda: {$searchFilter}";
        }
        if ($estadoFilter) {
            $estadoLabels = [
                'available' => 'Disponible',
                'assigned' => 'Asignado',
                'maintenance' => 'En Mantenimiento',
                'written_off' => 'Dado de Baja',
            ];
            $filtrosAplicados[] = "Estado: " . ($estadoLabels[$estadoFilter] ?? $estadoFilter);
        }
        if ($categoriaFilter) {
            $categoria = AssetCategory::find($categoriaFilter);
            if ($categoria) {
                $filtrosAplicados[] = "Categoría: {$categoria->nombre}";
            }
        }

        $generatedDate = now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('assets.pdf.inventory', compact(
            'assets',
            'generatedDate',
            'totalActivos',
            'totalDisponibles',
            'totalAsignados',
            'totalMantenimiento',
            'totalBaja',
            'valorTotal',
            'filtrosAplicados'
        ));

        // Orientación horizontal para que quepan más columnas
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('inventario_activos_' . now()->format('dmY') . '.pdf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Limpiar formato de moneda
        if ($request->has('valor_referencial')) {
            $val = $request->input('valor_referencial');
            $request->merge([
                'valor_referencial' => $val ? str_replace('.', '', $val) : null,
            ]);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:asset_categories,id',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'required|string|max:255',
            'estado' => 'required|in:available,assigned,maintenance,written_off',
            'ubicacion' => 'nullable|string|max:255',
            'fecha_adquisicion' => 'nullable|date',
            'valor_referencial' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|max:10240',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->except(['foto']);

        // Guardar foto si existe
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('assets', 'public');
            $data['foto_path'] = $path;
        }

        Asset::create($data);

        return redirect()->route('assets.index')->with('success', 'Activo creado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // Limpiar formato de moneda
        if ($request->has('valor_referencial')) {
            $val = $request->input('valor_referencial');
            $request->merge([
                'valor_referencial' => $val ? str_replace('.', '', $val) : null,
            ]);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:asset_categories,id',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'required|string|max:255',
            'estado' => 'required|in:available,assigned,maintenance,written_off',
            'ubicacion' => 'nullable|string|max:255',
            'fecha_adquisicion' => 'nullable|date',
            'valor_referencial' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|max:10240',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->except(['foto']);

        // Actualizar foto si se subió una nueva
        if ($request->hasFile('foto')) {
            // Eliminar foto antigua si existe
            if ($asset->foto_path) {
                Storage::disk('public')->delete($asset->foto_path);
            }
            $path = $request->file('foto')->store('assets', 'public');
            $data['foto_path'] = $path;
        }

        $asset->update($data);

        return redirect()->route('assets.index')->with('success', 'Activo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Activo enviado a la papelera.');
    }
    /**
     * Display a listing of trashed resources.
     */
    public function trash()
    {
        $assets = Asset::onlyTrashed()->with('category')->orderBy('deleted_at', 'desc')->get();
        return view('assets.trash', compact('assets'));
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);
        $asset->restore();
        return redirect()->route('assets.trash')->with('success', 'Activo restaurado exitosamente.');
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);

        // Eliminar foto si existe
        if ($asset->foto_path) {
            Storage::disk('public')->delete($asset->foto_path);
        }

        $asset->forceDelete();
        return redirect()->route('assets.trash')->with('success', 'Activo eliminado permanentemente.');
    }

    /**
     * Vaciar papelera (eliminar permanentemente todos los activos en papelera)
     */
    public function emptyTrash()
    {
        $assets = Asset::onlyTrashed()->get();

        if ($assets->isEmpty()) {
            return redirect()->route('assets.trash')->with('info', 'La papelera ya está vacía.');
        }

        $count = $assets->count();

        // Eliminar fotos asociadas y registros
        foreach ($assets as $asset) {
            if ($asset->foto_path) {
                Storage::disk('public')->delete($asset->foto_path);
            }
            $asset->forceDelete();
        }

        return redirect()->route('assets.trash')->with('success', "Se eliminaron permanentemente {$count} activos de la papelera.");
    }
    public function assign(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        if ($asset->estado !== 'available') {
            return back()->with('error', 'El activo no está disponible para asignación.');
        }

        // Validaciones dinámicas
        $rules = [
            'tipo_asignacion' => 'required|in:user,worker',
            'fecha_entrega' => 'required|date',
            'fecha_estimada_devolucion' => 'nullable|date|after_or_equal:fecha_entrega',
            'observaciones' => 'nullable|string',
        ];

        if ($request->tipo_asignacion === 'user') {
            $rules['usuario_id'] = 'required|exists:users,id';
        } else {
            // Si es trabajador
            if ($request->has('is_new_worker') && $request->is_new_worker == 1) {
                // Si marcó "Nuevo Trabajador", validamos los campos de texto
                $rules['trabajador_nombre'] = 'required|string|max:255';
                $rules['trabajador_rut'] = 'required|string|max:20';
                $rules['trabajador_departamento'] = 'nullable|string|max:255';
                $rules['trabajador_cargo'] = 'nullable|string|max:255';
            } else {
                // Si NO marcó nuevo, debe haber seleccionado uno
                $rules['worker_id_select'] = 'required|exists:workers,id';
            }
        }

        $request->validate($rules);

        $workerId = null;
        $workerData = null;

        if ($request->tipo_asignacion === 'worker') {
            if ($request->has('is_new_worker') && $request->is_new_worker == 1) {
                // Crear o actualizar (si el RUT ya existía, actualizamos datos)
                $worker = Worker::updateOrCreate(
                    ['rut' => $request->trabajador_rut],
                    [
                        'nombre' => $request->trabajador_nombre,
                        'departamento' => $request->trabajador_departamento,
                        'cargo' => $request->trabajador_cargo
                    ]
                );
                $workerId = $worker->id;
                $workerData = $worker; // Para llenar campos de redundancia si se desea
            } else {
                // Usar existente
                $workerId = $request->worker_id_select;
                $workerData = Worker::find($workerId);
            }
        }

        AssetAssignment::create([
            'activo_id' => $asset->id,
            'usuario_id' => $request->tipo_asignacion === 'user' ? $request->usuario_id : null,
            'created_by' => auth()->id(),
            'worker_id' => $workerId,
            'trabajador_nombre' => $workerData ? $workerData->nombre : null,
            'trabajador_rut' => $workerData ? $workerData->rut : null,
            'trabajador_departamento' => $workerData ? $workerData->departamento : null,
            'trabajador_cargo' => $workerData ? $workerData->cargo : null,
            'fecha_entrega' => $request->fecha_entrega,
            'fecha_estimada_devolucion' => $request->fecha_estimada_devolucion,
            'estado_entrega' => 'good',
            'observaciones' => $request->observaciones,
        ]);

        $asset->update(['estado' => 'assigned']);

        return redirect()->route('assets.index')->with('success', 'Activo asignado correctamente.');
    }

    public function updateAssignment(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $rules = [
            'fecha_entrega' => 'required|date',
            'fecha_estimada_devolucion' => 'nullable|date|after_or_equal:fecha_entrega',
            'observaciones' => 'nullable|string',
        ];

        $request->validate($rules);

        // Buscar la asignación activa
        $assignment = $asset->active_assignment;

        if (!$assignment) {
            return back()->with('error', 'No se encontró una asignación activa para este activo.');
        }

        $assignment->update([
            'fecha_entrega' => $request->fecha_entrega,
            'fecha_estimada_devolucion' => $request->fecha_estimada_devolucion,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('assets.index')->with('success', 'Asignación actualizada correctamente.');
    }

    /**
     * Download barcode PDF.
     */
    public function downloadBarcode(Request $request, $id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);

        // Obtener tamaño de etiqueta (default: medium)
        $size = $request->query('size', 'medium');

        // Validar tamaño
        if (!in_array($size, ['small', 'medium', 'large'])) {
            $size = 'medium';
        }

        $generator = new BarcodeGeneratorPNG();

        // Dimensiones del código de barras según tamaño de etiqueta
        $barcodeDimensions = [
            'small' => ['widthFactor' => 2, 'height' => 30],
            'medium' => ['widthFactor' => 2, 'height' => 40],
            'large' => ['widthFactor' => 3, 'height' => 50],
        ];

        $dims = $barcodeDimensions[$size];
        $barcode = base64_encode($generator->getBarcode($asset->codigo_barra, $generator::TYPE_CODE_128, $dims['widthFactor'], $dims['height']));


        // Dimensiones del papel según tamaño de etiqueta
        $paperSizes = [
            'small' => [0, 0, 142, 71],    // 50mm x 25mm
            'medium' => [0, 0, 200, 120],   // 70mm x 42mm
            'large' => [0, 0, 283, 142],    // 100mm x 50mm
        ];

        $pdf = Pdf::loadView('assets.barcode', compact('asset', 'barcode', 'size'));
        $pdf->setPaper($paperSizes[$size], 'landscape');

        return $pdf->download(\Illuminate\Support\Str::slug($asset->nombre . ' ' . ($asset->marca ?? '') . ' ' . $asset->codigo_interno) . '.pdf');
    }

    /**
     * Download multiple barcodes PDF (batch printing).
     */
    public function downloadBarcodes(Request $request)
    {
        // Validar request
        $request->validate([
            'asset_ids' => 'required|array|min:1|max:50',
            'asset_ids.*' => 'required|exists:assets,id',
            'size' => 'nullable|in:small,medium,large',
        ]);

        $assetIds = $request->input('asset_ids');
        $size = $request->input('size', 'medium');

        // Obtener activos
        $assets = Asset::withTrashed()->whereIn('id', $assetIds)->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'No se encontraron activos.');
        }

        // Generar códigos de barras para todos los activos
        $generator = new BarcodeGeneratorPNG();

        $barcodeDimensions = [
            'small' => ['widthFactor' => 2, 'height' => 25],
            'medium' => ['widthFactor' => 2, 'height' => 40],
            'large' => ['widthFactor' => 3, 'height' => 50],
        ];

        $dims = $barcodeDimensions[$size];

        $assetsWithBarcodes = $assets->map(function ($asset) use ($generator, $dims) {
            return [
                'asset' => $asset,
                'barcode' => base64_encode($generator->getBarcode($asset->codigo_barra, $generator::TYPE_CODE_128, $dims['widthFactor'], $dims['height']))
            ];
        });

        // Generar PDF con layout de grid
        $pdf = Pdf::loadView('assets.barcode-batch', compact('assetsWithBarcodes', 'size'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('etiquetas-activos-' . now()->format('dmY-His') . '.pdf');
    }

    public function cancelAssignment(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        $assignment = $asset->activeAssignment;

        if ($assignment) {
            $request->validate([
                'estado_devolucion' => 'required|string',
                'comentarios_devolucion' => 'nullable|string',
                'photos' => 'nullable|array|max:5',
                'photos.*' => 'image|mimes:jpg,jpeg,png|max:10240',
            ]);

            $assignment->update([
                'fecha_devolucion' => now(),
                'estado_devolucion' => $request->estado_devolucion,
                'comentarios_devolucion' => $request->comentarios_devolucion,
            ]);

            // Guardar fotos si existen
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('assignment_photos', 'public');

                    \App\Models\AssetAssignmentPhoto::create([
                        'assignment_id' => $assignment->id,
                        'photo_path' => $path,
                    ]);
                }
            }

            // Notificar si el estado es malo
            if (in_array($request->estado_devolucion, ['poor', 'damaged'])) {
                $admins = User::where('role', 'admin')->get(); // Enviar solo a administradores
                foreach ($admins as $admin) {
                    $admin->notify(new AssetConditionNotification($asset, $request->estado_devolucion, $request->comentarios_devolucion));
                }
            }
        }

        $asset->update(['estado' => 'available']);

        $photoCount = $request->hasFile('photos') ? count($request->file('photos')) : 0;
        $message = $photoCount > 0
            ? "Asignación terminada correctamente. Se subieron {$photoCount} foto(s)."
            : 'Asignación terminada correctamente.';

        return back()->with('success', $message);
    }

    public function history(Request $request, $id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);

        // Query Assignments
        $assignmentsQuery = $asset->assignments()
            ->with(['user', 'worker', 'creator'])
            ->orderBy('created_at', 'desc');

        // Query Maintenances
        $maintenancesQuery = $asset->maintenances()
            ->with(['creator'])
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $assignmentsQuery->whereDate('fecha_entrega', '>=', $request->start_date);
            $maintenancesQuery->whereDate('fecha', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $assignmentsQuery->whereDate('fecha_entrega', '<=', $request->end_date);
            $maintenancesQuery->whereDate('fecha', '<=', $request->end_date);
        }

        $assignments = $assignmentsQuery->get();
        $maintenances = $maintenancesQuery->get();

        return view('assets.history', compact('asset', 'assignments', 'maintenances'));
    }

    public function downloadHistoryPdf(Request $request, $id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);
        $generatedDate = now()->format('d/m/Y H:i');

        // Determinar filtros aplicados
        $filtrosAplicados = [];
        $startDate = null;
        $endDate = null;

        if ($request->filled('start_date')) {
            $startDate = $request->start_date;
            $filtrosAplicados[] = "Desde: " . \Carbon\Carbon::parse($startDate)->format('d/m/Y');
        }

        if ($request->filled('end_date')) {
            $endDate = $request->end_date;
            $filtrosAplicados[] = "Hasta: " . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
        }

        if ($request->query('type') === 'maintenances') {
            // Logica para PDF de Mantenciones
            $query = $asset->maintenances()->with('creator')
                ->orderBy('fecha', 'desc')
                ->orderBy('created_at', 'desc');

            if ($request->filled('start_date')) {
                $query->whereDate('fecha', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('fecha', '<=', $request->end_date);
            }

            $maintenances = $query->get();

            // Calcular estadísticas de mantenciones
            $totalMantenciones = $maintenances->count();
            $totalPreventivas = $maintenances->where('tipo', 'preventiva')->count();
            $totalCorrectivas = $maintenances->where('tipo', 'correctiva')->count();
            $totalCompletadas = $maintenances->where('fecha_termino', '!=', null)->count();
            $totalEnProceso = $maintenances->where('fecha_termino', null)->count();
            $costoTotal = $maintenances->sum('costo');

            $pdf = Pdf::loadView('assets.history-maintenance-pdf', compact(
                'asset',
                'maintenances',
                'generatedDate',
                'filtrosAplicados',
                'totalMantenciones',
                'totalPreventivas',
                'totalCorrectivas',
                'totalCompletadas',
                'totalEnProceso',
                'costoTotal',
                'startDate',
                'endDate'
            ));
            $filename = 'historial-mantenciones-' . $asset->codigo_interno . '-' . now()->format('dmY-His') . '.pdf';

        } else {
            // Lógica por defecto (Asignaciones)
            $query = $asset->assignments()
                ->with(['user', 'worker', 'creator'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('start_date')) {
                $query->whereDate('fecha_entrega', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('fecha_entrega', '<=', $request->end_date);
            }

            $assignments = $query->get();

            // Calcular estadísticas de asignaciones
            $totalAsignaciones = $assignments->count();
            $totalActivas = $assignments->where('fecha_devolucion', null)->count();
            $totalDevueltas = $assignments->where('fecha_devolucion', '!=', null)->count();
            $totalBueno = $assignments->where('estado_devolucion', 'good')->count();
            $totalRegular = $assignments->where('estado_devolucion', 'regular')->count();
            $totalMalo = $assignments->where('estado_devolucion', 'bad')->count();
            $totalDanado = $assignments->where('estado_devolucion', 'damaged')->count();

            $pdf = Pdf::loadView('assets.history-pdf', compact(
                'asset',
                'assignments',
                'generatedDate',
                'filtrosAplicados',
                'totalAsignaciones',
                'totalActivas',
                'totalDevueltas',
                'totalBueno',
                'totalRegular',
                'totalMalo',
                'totalDanado',
                'startDate',
                'endDate'
            ));
            $filename = 'historial-asignaciones-' . $asset->codigo_interno . '-' . now()->format('dmY-His') . '.pdf';
        }

        return $pdf->download($filename);
    }

    /**
     * Enviar activo a mantención desde alerta de daño
     */
    public function sendToMaintenance(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'fecha_mantencion' => 'required|date',
            'motivo_mantencion' => 'required|string|max:255',
        ]);

        // Crear registro de mantención
        \App\Models\AssetMaintenance::create([
            'activo_id' => $asset->id,
            'created_by' => auth()->id(),
            'tipo' => 'correctiva', // Asumimos correctiva por defecto al venir de un daño
            'descripcion' => $request->motivo_mantencion,
            'fecha' => $request->fecha_mantencion,
        ]);

        // Actualizar estado del activo
        $asset->update(['estado' => 'maintenance']);

        return back()->with('success', 'Activo enviado a mantención correctamente.');
    }

    /**
     * Dar de baja activo desde alerta de daño
     */
    public function writeOff(Request $request, $id)
    {
        \Illuminate\Support\Facades\Log::info("WriteOff Request initiated for ID: $id", $request->all());

        $asset = Asset::findOrFail($id);

        $request->validate([
            'motivo' => 'required|string|max:1000',
            'fecha' => 'required|date',
        ]);

        // Crear registro de baja
        $writeOff = \App\Models\AssetWriteOff::create([
            'asset_id' => $asset->id,
            'user_id' => auth()->id(),
            'motivo' => $request->motivo,
            'fecha' => $request->fecha,
        ]);

        \Illuminate\Support\Facades\Log::info("WriteOff created for asset {$asset->id} (No evidence upload).");

        // Actualizar estado del activo
        $asset->update(['estado' => 'written_off']);

        return back()->with('success', 'Activo dado de baja correctamente. Evidencia guardada.');
    }

    /**
     * Finalizar mantención de un activo
     */
    public function finishMaintenance(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'fecha_termino' => 'required|date',
            'detalles_solucion' => 'required|string',
            'costo' => 'nullable|integer|min:0',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:10240',
        ]);

        // Buscar el último registro de mantenimiento abierto (sin fecha de término)
        // O simplemente el último creado si asumimos que es el activo
        $maintenance = \App\Models\AssetMaintenance::where('activo_id', $asset->id)
            ->whereNull('fecha_termino')
            ->latest()
            ->first();

        if ($maintenance) {
            $maintenance->update([
                'fecha_termino' => $request->fecha_termino,
                'detalles_solucion' => $request->detalles_solucion,
                'costo' => $request->costo,
            ]);
        } else {
            // Si no hay uno abierto, podemos optar por crear uno cerrado o simplemente ignorar la actualización del registro
            // y solo cambiar el estado. Pero lo ideal es que siempre haya correspondencia.
            // Para robustez, si no encuentra uno abierto, buscamos el ultimo creado.
            $maintenance = \App\Models\AssetMaintenance::where('activo_id', $asset->id)->latest()->first();
            if ($maintenance) {
                $maintenance->update([
                    'fecha_termino' => $request->fecha_termino,
                    'detalles_solucion' => $request->detalles_solucion,
                    'costo' => $maintenance->costo ?? $request->costo, // Mantiene costo si ya existía
                ]);
            }
        }

        // Guardar fotos si se subieron
        $photoCount = 0;
        if ($request->hasFile('photos') && $maintenance) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('maintenance_photos', 'public');

                \App\Models\MaintenancePhoto::create([
                    'maintenance_id' => $maintenance->id,
                    'photo_path' => $path,
                ]);

                $photoCount++;
            }
        }

        // Actualizar estado del activo a disponible
        $asset->update(['estado' => 'available']);

        $message = 'Mantención finalizada. Activo disponible nuevamente.';
        if ($photoCount > 0) {
            $message .= " Se subieron {$photoCount} foto(s).";
        }

        return back()->with('success', $message);
    }

    public function usersHistoryIndex()
    {
        $users = \App\Models\User::all();
        $workers = \App\Models\Worker::all();
        return view('assets.users-index', compact('users', 'workers'));
    }


    public function userAssetHistory(Request $request, $id)
    {
        $recipient = \App\Models\User::findOrFail($id);

        $query = \App\Models\AssetAssignment::with(['asset', 'photos', 'creator'])
            ->where('usuario_id', $recipient->id);

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('return_status')) {
            $status = $request->return_status;
            if ($status === 'pending') {
                $query->whereNull('fecha_devolucion');
            } else {
                $query->where('estado_devolucion', $status);
            }
        }

        $assignments = $query->orderBy('created_at', 'desc')->get();

        return view('assets.user-history', compact('recipient', 'assignments'));
    }


    public function workerAssetHistory(Request $request, $id)
    {
        $recipient = \App\Models\Worker::findOrFail($id);

        $query = \App\Models\AssetAssignment::with(['asset', 'photos', 'creator'])
            ->where('worker_id', $recipient->id);

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('return_status')) {
            $status = $request->return_status;
            if ($status === 'pending') {
                $query->whereNull('fecha_devolucion');
            } else {
                $query->where('estado_devolucion', $status);
            }
        }

        $assignments = $query->orderBy('created_at', 'desc')->get();

        return view('assets.user-history', compact('recipient', 'assignments'));
    }
    public function downloadUserHistoryPdf(Request $request, $id)
    {
        $recipient = ($request->type === 'worker') ? Worker::findOrFail($id) : User::findOrFail($id);
        $generatedDate = now()->format('d/m/Y H:i');

        $query = AssetAssignment::with(['asset', 'creator'])
            ->where(($request->type === 'worker' ? 'worker_id' : 'usuario_id'), $id);

        $filtrosAplicados = [];
        if ($request->filled('start_date')) {
            $query->whereDate('fecha_entrega', '>=', $request->start_date);
            $filtrosAplicados[] = "Desde: " . \Carbon\Carbon::parse($request->start_date)->format('d/m/Y');
        }
        if ($request->filled('end_date')) {
            $query->whereDate('fecha_entrega', '<=', $request->end_date);
            $filtrosAplicados[] = "Hasta: " . \Carbon\Carbon::parse($request->end_date)->format('d/m/Y');
        }
        if ($request->filled('return_status')) {
            $status = $request->return_status;
            if ($status === 'pending') {
                $query->whereNull('fecha_devolucion');
                $filtrosAplicados[] = "Estado: En Uso";
            } else {
                $query->where('estado_devolucion', $status);
                $filtrosAplicados[] = "Estado: " . ucfirst($status);
            }
        }

        $assignments = $query->orderBy('fecha_entrega', 'desc')->get();

        // Estadísticas detalladas
        $stats = [
            'total' => $assignments->count(),
            'activas' => $assignments->where('fecha_devolucion', null)->count(),
            'devueltas' => $assignments->where('fecha_devolucion', '!=', null)->count(),
            'good' => $assignments->where('estado_devolucion', 'good')->count(),
            'regular' => $assignments->where('estado_devolucion', 'regular')->count(),
            'bad' => $assignments->where('estado_devolucion', 'bad')->count(),
            'damaged' => $assignments->where('estado_devolucion', 'damaged')->count(),
        ];

        $pdf = Pdf::loadView('assets.pdf.user-history', compact(
            'recipient',
            'assignments',
            'generatedDate',
            'filtrosAplicados',
            'stats'
        ));

        return $pdf->download('historial_uso_' . ($recipient->rut) . '.pdf');
    }
}
