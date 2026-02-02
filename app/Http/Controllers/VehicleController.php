<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\MaintenanceRequest;
use App\Models\VehicleRequest;
use App\Models\VehicleDocument;
use Illuminate\Support\Facades\Storage;

use App\Services\MaintenanceService;

class VehicleController extends Controller
{
    /**
     * Muestra un listado del recurso.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();

        // Filtro de Búsqueda (Patente, Marca o Modelo)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('plate', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filtro de Estado
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filtro de Estado Documentos
        if ($request->filled('document_status')) {
            if ($request->input('document_status') === 'expired') {
                $query->whereHas('documents', function ($q) {
                    $q->where('expires_at', '<', now()->startOfDay());
                });
            } elseif ($request->input('document_status') === 'up_to_date') {
                $query->whereDoesntHave('documents', function ($q) {
                    $q->where('expires_at', '<', now()->startOfDay());
                });
            }
        }

        // Filtro de Mantención
        if ($request->filled('maintenance_status')) {
            if ($request->input('maintenance_status') === 'needed') {
                // Filtra vehículos que están a menos de 1000km del cambio o pasados
                // Requiere join o whereHas con raw porque comparamos columnas de dos tablas
                $query->whereHas('currentMaintenanceState', function ($q) {
                    $q->whereColumn('next_oil_change_km', '<=', \DB::raw('vehicles.mileage + 1000'));
                });
            } elseif ($request->input('maintenance_status') === 'ok') {
                $query->whereDoesntHave('currentMaintenanceState', function ($q) {
                    $q->whereColumn('next_oil_change_km', '<=', \DB::raw('vehicles.mileage + 1000'));
                });
            }
        }

        $vehicles = $query->get();

        // Estados
        $countDisponible = Vehicle::where('status', 'available')->count();
        $countFueraDeServicio = Vehicle::where('status', 'out_of_service')->count();
        $countMantenimiento = Vehicle::where('status', 'maintenance')->count();
        $countAsignado = Vehicle::where('status', 'occupied')->count();

        // Solicitudes de mantenimiento
        $pendingRequests = MaintenanceRequest::with('vehicle')
            ->where('status', 'pending')
            ->latest()
            ->get();


        $pendingReservations = VehicleRequest::with(['vehicle', 'user', 'conductor'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 
        $data = compact(
            'vehicles',
            'pendingRequests',
            'pendingReservations',
            'countDisponible',
            'countAsignado',
            'countMantenimiento',
            'countFueraDeServicio'
        );

        // Lógica de separación de vistas
        if ($request->routeIs('dashboard')) {
            return view('dashboard', $data);
        }

        return view('vehicles.index', $data);
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     */
    public function store(Request $request)
    {
        // Limpiar el kilometraje de puntos antes de validar
        if ($request->has('mileage')) {
            $request->merge([
                'mileage' => str_replace('.', '', $request->input('mileage')),
            ]);
        }

        $request->validate([
            'plate' => 'required|unique:vehicles|max:255',
            'serial_number' => 'nullable|unique:vehicles|max:255',
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'image' => 'nullable|image|max:10240', // Máx 10MB
            'fuel_type' => 'required|in:gasoline,diesel',

            // Documentos
            'soap_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'soap_expires_at' => 'nullable|required_with:soap_file|date',
            'permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'permit_expires_at' => 'nullable|required_with:permit_file|date',
            'technical_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'technical_expires_at' => 'nullable|required_with:technical_file|date',
        ]);

        $data = $request->except(['image', 'soap_file', 'soap_expires_at', 'permit_file', 'permit_expires_at', 'technical_file', 'technical_expires_at']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vehicles', 'public');
            $data['image_path'] = $path;
        }

        $vehicle = Vehicle::create($data);

        // Guardar documentos si existen
        $this->handleDocumentUpload($request, $vehicle, 'soap', 'insurance');
        $this->handleDocumentUpload($request, $vehicle, 'permit', 'permit');
        $this->handleDocumentUpload($request, $vehicle, 'technical', 'technical_review');

        return redirect()->route('vehicles.index')->with('success', 'Vehículo creado exitosamente con sus documentos.');
    }

    private function handleDocumentUpload(Request $request, Vehicle $vehicle, string $inputPrefix, string $docType)
    {
        if ($request->hasFile("{$inputPrefix}_file")) {
            $path = $request->file("{$inputPrefix}_file")->store('vehicles/documents', 'public');

            // Buscar si ya existe para actualizar o crear nuevo (útil para update, aquí es create)
            // Para store siempre creamos, pero mejor reutilizar lógica.
            // En store es nuevo vehículo, así que create directo.

            $vehicle->documents()->create([
                'type' => $docType,
                'expires_at' => $request->input("{$inputPrefix}_expires_at"),
                'file_path' => $path,
                'status' => 'active',
            ]);
        }
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Limpiar el kilometraje de puntos antes de validar
        if ($request->has('mileage')) {
            $request->merge([
                'mileage' => str_replace('.', '', $request->input('mileage')),
            ]);
        }

        $request->validate([
            'plate' => 'required|max:255|unique:vehicles,plate,' . $vehicle->id,
            'serial_number' => 'nullable|max:255|unique:vehicles,serial_number,' . $vehicle->id,
            'brand' => 'required|max:255',
            'model' => 'required|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'image' => 'nullable|image|max:10240',
            'fuel_type' => 'required|in:gasoline,diesel',

            // Docs validation
            'soap_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'soap_expires_at' => 'nullable|date',
            'permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'permit_expires_at' => 'nullable|date',
            'technical_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'technical_expires_at' => 'nullable|date',
        ]);

        $data = $request->except(['image', 'soap_file', 'soap_expires_at', 'permit_file', 'permit_expires_at', 'technical_file', 'technical_expires_at']);

        // Prevent status change if vehicle is occupied
        if ($vehicle->status === 'occupied' && isset($data['status']) && $data['status'] !== 'occupied') {
            return back()->withErrors(['status' => 'No se puede modificar el estado de un vehículo que se encuentra en uso.']);
        }

        if ($request->hasFile('image')) {
            // Eliminar imagen antigua si existe
            if ($vehicle->image_path) {
                Storage::disk('public')->delete($vehicle->image_path);
            }
            $path = $request->file('image')->store('vehicles', 'public');
            $data['image_path'] = $path;
        }

        $vehicle->update($data);

        // Actualizar documentos
        $this->processDocumentUpdate($request, $vehicle, 'soap', 'insurance');
        $this->processDocumentUpdate($request, $vehicle, 'permit', 'permit');
        $this->processDocumentUpdate($request, $vehicle, 'technical', 'technical_review');

        // Check for maintenance alerts immediately
        (new MaintenanceService)->checkAndNotify();

        return redirect()->route('vehicles.index')->with('success', 'Vehículo actualizado correctamente.');
    }

    private function processDocumentUpdate(Request $request, Vehicle $vehicle, string $prefix, string $type)
    {
        // Buscar documento existente
        $doc = $vehicle->documents()->where('type', $type)->first();

        $hasFile = $request->hasFile("{$prefix}_file");
        $hasDate = $request->filled("{$prefix}_expires_at");

        if ($hasFile || $hasDate) {
            if (!$doc) {
                $doc = new VehicleDocument(['vehicle_id' => $vehicle->id, 'type' => $type, 'status' => 'active']);
            }

            if ($hasFile) {
                if ($doc->file_path) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->file_path = $request->file("{$prefix}_file")->store('vehicles/documents', 'public');
            }

            if ($hasDate) {
                $doc->expires_at = $request->input("{$prefix}_expires_at");
            }

            $vehicle->documents()->save($doc);
        }
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehículo enviado a la papelera.');
    }

    public function trash()
    {
        $vehicles = Vehicle::onlyTrashed()->get();
        return view('vehicles.trash', compact('vehicles'));
    }

    public function restore($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        $vehicle->restore();
        return redirect()->route('vehicles.trash')->with('success', 'Vehículo restaurado exitosamente.');
    }

    public function forceDelete($id)
    {
        $vehicle = Vehicle::withTrashed()->findOrFail($id);
        if ($vehicle->image_path) {
            Storage::disk('public')->delete($vehicle->image_path);
        }
        $vehicle->forceDelete();
        return redirect()->route('vehicles.trash')->with('success', 'Vehículo eliminado permanentemente.');
    }

    public function storeDocument(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'type' => 'required|string',
            'expires_at' => 'nullable|date',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ]);

        $path = $request->file('file')->store('course/documents', 'public');

        $vehicle->documents()->create([
            'type' => $request->type,
            'expires_at' => $request->expires_at,
            'file_path' => $path,
            'status' => 'active',
        ]);

        return back()->with('success', 'Documento agregado correctamente.');
    }

    public function deleteDocument($id)
    {
        $document = VehicleDocument::findOrFail($id);

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
