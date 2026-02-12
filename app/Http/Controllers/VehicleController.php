<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleRequest;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Filtro por búsqueda (patente, marca, modelo)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filtros (solo para la vista de lista, pero no hacen daño en dashboard)
        if ($request->has('status') && $request->status) {
            // Nota: El estado 'occupied' y permisos se calculan dinámicamente en el modelo,
            // pero si filtramos por columna 'status' en BD:
            $query->where('status', $request->status);
        }

        // Ordenar
        $vehicles = $query->orderBy('status', 'asc') // Available primero usualmente, o mejor por ID/fecha
            ->orderBy('created_at', 'desc')
            ->get();

        // Contadores para las tarjetas
        $totalVehicles = Vehicle::count();
        $countDisponible = Vehicle::where('status', 'available')->count();
        $countAsignado = Vehicle::where('status', 'occupied')->count();
        $countMantenimiento = Vehicle::where('status', 'maintenance')->count();
        $countFueraDeServicio = Vehicle::where('status', 'out_of_service')->count();

        // Solicitudes pendientes (para el badge en el botón y el modal)
        // 1. Solicitudes de reserva de vehículos (Trips)
        $pendingReservations = VehicleRequest::where('status', 'pending')
            ->with(['user', 'vehicle', 'companions.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 2. Solicitudes de mantenimiento (Maintenance)
        $pendingRequests = \App\Models\MaintenanceRequest::where('status', 'pending')
            ->with(['vehicle'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Si la ruta es dashboard
        if ($request->routeIs('dashboard')) {
            return view('dashboard', compact(
                'vehicles',
                'totalVehicles',
                'countDisponible',
                'countAsignado',
                'countMantenimiento',
                'countFueraDeServicio',
                'pendingRequests',
                'pendingReservations'
            ));
        }

        // Si es la lista de vehículos
        return view('vehicles.index', compact(
            'vehicles',
            'totalVehicles',
            'countDisponible',
            'countAsignado',
            'countMantenimiento',
            'countFueraDeServicio',
            'pendingRequests',
            'pendingReservations'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generalmente usamos modales, pero si existiera vista separada:
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plate' => 'required|string|unique:vehicles,plate|max:10',
            'serial_number' => 'nullable|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|numeric|min:0',
            'fuel_type' => 'required|string|in:diesel,gasoline,electric,hybrid',
            'image' => 'nullable|image|max:2048', // 2MB Max
            // Documentos opcionales al crear
            'soap_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'soap_expires_at' => 'nullable|date',
            'permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'permit_expires_at' => 'nullable|date',
            'technical_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'technical_expires_at' => 'nullable|date',
        ]);

        $data = $request->except(['image', 'soap_file', 'permit_file', 'technical_file']);
        $data['status'] = 'available'; // Default status

        // Guardar imagen si existe
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vehicles', 'public');
            $data['image_path'] = $path;
        }

        $vehicle = Vehicle::create($data);

        // Guardar documentos si se subieron
        $this->storeDocument($vehicle, $request, 'soap_file', 'insurance', $request->soap_expires_at);
        $this->storeDocument($vehicle, $request, 'permit_file', 'permit', $request->permit_expires_at);
        $this->storeDocument($vehicle, $request, 'technical_file', 'technical_review', $request->technical_expires_at);

        return redirect()->route('vehicles.index')->with('success', 'Vehículo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'plate' => ['required', 'string', 'max:10', Rule::unique('vehicles')->ignore($vehicle->id)],
            'serial_number' => 'nullable|string|max:50',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|numeric|min:0',
            'fuel_type' => 'required|string|in:diesel,gasoline,electric,hybrid',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|string',
        ]);

        $data = $request->except(['image']);

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($vehicle->image_path) {
                Storage::disk('public')->delete($vehicle->image_path);
            }
            $path = $request->file('image')->store('vehicles', 'public');
            $data['image_path'] = $path;
        }

        $vehicle->update($data);

        return redirect()->back()->with('success', 'Vehículo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehículo movido a la papelera.');
    }

    /**
     * Mostrar papelera
     */
    public function trash()
    {
        $vehicles = Vehicle::onlyTrashed()->get();
        return view('vehicles.trash', compact('vehicles'));
    }

    /**
     * Restaurar vehículo
     */
    public function restore($id)
    {
        $vehicle = Vehicle::onlyTrashed()->findOrFail($id);
        $vehicle->restore();
        return redirect()->route('vehicles.trash')->with('success', 'Vehículo restaurado exitosamente.');
    }

    /**
     * Eliminar permanentemente
     */
    public function forceDelete($id)
    {
        $vehicle = Vehicle::onlyTrashed()->findOrFail($id);

        // Eliminar imagen asociada
        if ($vehicle->image_path) {
            Storage::disk('public')->delete($vehicle->image_path);
        }

        // Eliminar documentos asociados (si aplica lógica de cascade o manual)
        foreach ($vehicle->documents as $doc) {
            if ($doc->file_path) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
        }

        $vehicle->forceDelete();
        return redirect()->route('vehicles.trash')->with('success', 'Vehículo eliminado permanentemente.');
    }

    /**
     * Vaciar papelera
     */
    public function emptyTrash()
    {
        $vehicles = Vehicle::onlyTrashed()->get();
        foreach ($vehicles as $vehicle) {
            if ($vehicle->image_path) {
                Storage::disk('public')->delete($vehicle->image_path);
            }
            foreach ($vehicle->documents as $doc) {
                if ($doc->file_path) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }
            $vehicle->forceDelete();
        }
        return redirect()->route('vehicles.trash')->with('success', 'Papelera vaciada.');
    }

    // Helper para guardar documentos
    private function storeDocument($vehicle, $request, $fileInputName, $type, $expiresAt)
    {
        if ($request->hasFile($fileInputName)) {
            $path = $request->file($fileInputName)->store('vehicle_documents', 'public');

            $vehicle->documents()->create([
                'type' => $type,
                'file_path' => $path,
                'expires_at' => $expiresAt,
            ]);
        }
    }
    public function usersHistoryIndex()
    {
        $users = User::all();
        $workers = Worker::all();
        return view('vehicles.users_index', compact('users', 'workers'));
    }

    public function userUsageHistory(Request $request, $id)
    {
        $recipient = User::findOrFail($id);
        $query = VehicleRequest::where('user_id', $id)
            ->with(['vehicle', 'conductor', 'vehicleReturn', 'companions.user'])
            ->orderBy('start_date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
        }

        $usageHistory = $query->get();
        return view('vehicles.user_usage_history', compact('recipient', 'usageHistory'));
    }

    public function workerUsageHistory(Request $request, $id)
    {
        $recipient = Worker::findOrFail($id);
        $query = VehicleRequest::where('conductor_id', $id)
            ->with(['vehicle', 'vehicleReturn', 'companions.user'])
            ->orderBy('start_date', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
        }

        $usageHistory = $query->get();
        return view('vehicles.user_usage_history', compact('recipient', 'usageHistory'));
    }
    public function downloadUserHistoryPdf(Request $request, $id)
    {
        $type = $request->input('type', 'user');
        $recipient = ($type === 'worker') ? \App\Models\Worker::findOrFail($id) : \App\Models\User::findOrFail($id);
        
        $query = \App\Models\VehicleRequest::where(($type === 'worker' ? 'conductor_id' : 'user_id'), $id)
            ->with(['vehicle', 'vehicleReturn']);

        $filters = [];
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
            $filters[] = "Desde: " . \Carbon\Carbon::parse($request->start_date)->format('d/m/Y');
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_date', '<=', $request->end_date);
            $filters[] = "Hasta: " . \Carbon\Carbon::parse($request->end_date)->format('d/m/Y');
        }

        $usageHistory = $query->orderBy('start_date', 'desc')->get();
        $generatedDate = now()->format('d/m/Y H:i');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vehicles.user-history-pdf', compact('recipient', 'usageHistory', 'filters', 'generatedDate'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download("historial_vehiculos_" . ($recipient->rut ?? $recipient->name) . ".pdf");
    }
}
