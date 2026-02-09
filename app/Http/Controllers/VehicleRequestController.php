<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleRequest;
use App\Models\VehicleReturn;
use App\Models\VehicleMaintenanceState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NewVehicleRequestNotification;
use App\Notifications\VehicleReturnedNotification;

class VehicleRequestController extends Controller
{
    /**
     * Muestra las reservas del usuario actual.
     */
    public function index(Request $request)
    {
        $query = VehicleRequest::with([
            'vehicle' => function ($query) {
                $query->withTrashed();
            }
        ])->where('user_id', Auth::id());

        // Filtro por pestaña (estado)
        $tab = $request->get('tab', 'all');
        if ($tab !== 'all') {
            $query->where('status', $tab);
        }

        // Filtro por búsqueda (vehículo)
        if ($search = $request->get('search')) {
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('plate', 'like', "%{$search}%");
            });
        }

        // Filtro por rango de fechas
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return view('requests.index', compact('requests'));
    }

    /**
     * Muestra el formulario para solicitar un vehículo.
     */
    public function create()
    {
        $user = Auth::user();

        // 1. Verificar si tiene licencia registrada
        if (!$user->license_expires_at) {
            return redirect()->route('profile.edit')
                ->with('error', 'Para solicitar un vehículo, primero debe registrar su Licencia de Conducir en su perfil.');
        }

        // 2. Verificar si está vencida
        if ($user->license_expires_at < now()->startOfDay()) {
            return redirect()->route('profile.edit')
                ->with('error', 'Su Licencia de Conducir está vencida. Por favor actualice el documento para continuar.');
        }

        $vehicles = Vehicle::all()->filter(function ($vehicle) {
            return $vehicle->display_status === 'available';
        });

        $conductors = ($user->role === 'admin') ? \App\Models\Conductor::all() : collect([]);

        return view('requests.create', compact('vehicles', 'conductors'));
    }

    /**
     * Almacena una nueva solicitud de reserva.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'destination_type' => 'required|in:local,outside',
        ]);

        $user = Auth::user();
        if (!$user->license_expires_at || $user->license_expires_at < now()->startOfDay()) {
            return redirect()->route('profile.edit')
                ->with('error', 'Su licencia de conducir no es válida o no está registrada.');
        }

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        if (!$vehicle->isAvailable($request->start_date, $request->end_date)) {
            return back()->withErrors(['vehicle_id' => 'El vehículo no está disponible en las fechas seleccionadas.'])->withInput();
        }

        $vehicleRequest = VehicleRequest::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $vehicle->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
            'destination_type' => $request->destination_type,
            'conductor_id' => ($user->role === 'admin' && $request->has('is_third_party') && $request->conductor_id) ? $request->conductor_id : null,
        ]);

        // Manejo de nuevo conductor si se solicita
        if ($user->role === 'admin' && $request->has('is_third_party') && !$request->conductor_id && $request->input('new_conductor_name')) {
            $conductor = \App\Models\Conductor::create([
                'nombre' => $request->input('new_conductor_name'),
                'rut' => $request->input('new_conductor_rut'), // Puede ser nulo
                'cargo' => 'Externo', // Valor por defecto
                'departamento' => 'Externo',
                'fecha_licencia' => now()->addYear(), // Valor por defecto para evitar error SQL
            ]);

            $vehicleRequest->update(['conductor_id' => $conductor->id]);
        }

        // Notificar a administradores
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewVehicleRequestNotification($vehicleRequest));

        return redirect()->route('requests.create')->with('success', 'Solicitud enviada correctamente. Esperando aprobación.');
    }

    /**
     * Aprueba una solicitud de reserva (Admin).
     */
    public function approve($id)
    {
        $request = VehicleRequest::findOrFail($id);

        // Verificar conflicto nuevamente por seguridad
        if (!$request->vehicle->isAvailable($request->start_date, $request->end_date)) {
            return back()->with('error', 'No se puede aprobar: Existe conflicto de fechas con otra reserva aprobada.');
        }

        $request->update(['status' => 'approved']);

        $request->vehicle->update(['status' => 'occupied']);

        // Notificar usuario
        $request->user->notify(new \App\Notifications\VehicleRequestStatusNotification($request, 'approved'));

        return back()->with('success', 'Reserva aprobada exitosamente.');
    }

    /**
     * Rechaza una solicitud de reserva (Admin).
     */
    public function reject(Request $req, $id)
    {
        $request = VehicleRequest::findOrFail($id);

        $reason = $req->input('rejection_reason');

        $request->update([
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);

        // Notificar usuario
        $request->user->notify(new \App\Notifications\VehicleRequestStatusNotification($request, 'rejected', $reason));

        return back()->with('success', 'Reserva rechazada.');
    }

    /**
     * Finaliza una reserva (Devolución del vehículo) con checklist detallado.
     */
    public function complete(Request $request, $id)
    {
        $vehicleRequest = VehicleRequest::with('vehicle')->where('user_id', Auth::id())->findOrFail($id);

        // Limpiar formato de kilometraje (eliminar puntos)
        if ($request->has('return_mileage')) {
            $request->merge([
                'return_mileage' => (int) str_replace('.', '', $request->return_mileage)
            ]);
        }

        $request->validate([
            'return_mileage' => 'required|integer|min:' . $vehicleRequest->vehicle->mileage,
            'fuel_level' => 'required|in:1/4,1/2,3/4,full',
            'tire_status_front' => 'required|in:good,fair,poor',
            'tire_status_rear' => 'required|in:good,fair,poor',
            'cleanliness' => 'required|in:clean,dirty,very_dirty',
            'body_damage_reported' => 'nullable|boolean',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|max:10240', // Max 10MB per photo
            'comments' => 'nullable|string|max:1000',
        ]);

        // Procesar fotos si existen
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Generar nombre unico: return_{reqId}_{timestamp}_{uniqid}.jpg
                $filename = 'return_' . $vehicleRequest->id . '_' . time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('returns', $filename, 'public');
                $photoPaths[] = $path;
            }
        }

        // Crear registro de devolución
        VehicleReturn::create([
            'vehicle_request_id' => $vehicleRequest->id,
            'return_mileage' => $request->return_mileage,
            'fuel_level' => $request->fuel_level,
            'tire_status_front' => $request->tire_status_front,
            'tire_status_rear' => $request->tire_status_rear,
            'cleanliness' => $request->cleanliness,
            'body_damage_reported' => $request->has('body_damage_reported'),
            'comments' => $request->comments,
            'photos_paths' => $photoPaths, // Casted to array in model
        ]);

        // Actualizar vehículo
        $vehicleRequest->vehicle->update([
            'mileage' => $request->return_mileage,
            'status' => 'available'
        ]);

        // Actualizar estado de mantenimiento del vehículo (Neumáticos)
        // Buscamos o creamos el estado de mantenimiento
        $maintenanceState = VehicleMaintenanceState::firstOrCreate(
            ['vehicle_id' => $vehicleRequest->vehicle_id]
        );

        $maintenanceState->update([
            'tire_status_front' => $request->tire_status_front,
            'tire_status_rear' => $request->tire_status_rear,
        ]);

        // Finalizar solicitud
        $vehicleRequest->update([
            'status' => 'completed',
            'return_mileage' => $request->return_mileage
        ]);

        // Detectar si hubo daños reportados
        $hasDamage = $request->has('body_damage_reported') && $request->body_damage_reported;

        // Notificar a administradores
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new VehicleReturnedNotification($vehicleRequest, $hasDamage));

        return back()->with('success', 'Devolución registrada correctamente. Historial actualizado.');
    }

    /**
     * Termina una asignación anticipadamente (sin necesariamente devolución completa todavía, o asumiendo liberación inmediata).
     * Según requerimiento: "terminar la asignación... poner la razon... mostrarse en historial".
     * Esto libera el vehículo y completa la solicitud.
     */
    public function finishEarly(Request $request, $id)
    {
        $vehicleRequest = VehicleRequest::with('vehicle')->findOrFail($id);

        // Validar que sea el usuario correcto o admin (asumimos lógica similar a complete o approve)
        // Por seguridad, si no es admin, debería ser el dueño de la request
        if (Auth::user()->role !== 'admin' && $vehicleRequest->user_id !== Auth::id()) {
            abort(403, 'No autorizado para finalizar esta asignación.');
        }

        $now = now();
        $rules = [];

        // Si termina antes de tiempo, la razón es obligatoria
        if ($now < $vehicleRequest->end_date) {
            $rules['early_termination_reason'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // Actualizar datos
        $updateData = [
            'status' => 'completed',
            'original_end_date' => $vehicleRequest->end_date, // Guardar fecha original
            'end_date' => $now, // Cortar fecha a ahora
        ];

        if ($request->has('early_termination_reason')) {
            $updateData['early_termination_reason'] = $request->early_termination_reason;
        }

        $updateData['completed_by_user_id'] = Auth::id();

        $vehicleRequest->update($updateData);

        // Crear registro de devolución (Entrega) automáticamente
        // Usamos valores por defecto ya que es un término rápido sin formulario de inspección
        VehicleReturn::create([
            'vehicle_request_id' => $vehicleRequest->id,
            'return_mileage' => $vehicleRequest->vehicle->mileage, // Asumimos kilometraje actual del vehículo
            'fuel_level' => 'full', // Valor por defecto
            'tire_status_front' => 'good', // Valor por defecto
            'tire_status_rear' => 'good', // Valor por defecto
            'cleanliness' => 'clean', // Valor por defecto
            'body_damage_reported' => false,
            'comments' => 'Término Anticipado: ' . ($request->early_termination_reason ?? 'Sin motivo especificado'),
        ]);

        // Liberar vehículo
        $vehicleRequest->vehicle->update(['status' => 'available']);

        // Notificar a administradores (sin daños reportados por defecto en termino anticipado)
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new VehicleReturnedNotification($vehicleRequest, false));

        return back()->with('success', 'Asignación finalizada anticipadamente y devolución registrada.');
    }

    /**
     * Muestra el historial de uso de vehículos con filtros.
     */
    public function history(Request $request)
    {
        $query = VehicleRequest::with(['user', 'vehicle', 'conductor', 'vehicleReturn'])
            ->whereIn('status', ['completed', 'approved']) // Solo completadas y en uso
            ->orderBy('start_date', 'desc');

        // Filtro por rango de fechas (día, mes, año)
        if ($request->filled('filter_type') && $request->filled('filter_value')) {
            $filterType = $request->filter_type;
            $filterValue = $request->filter_value;

            if ($filterType === 'day') {
                $query->whereDate('start_date', $filterValue);
            } elseif ($filterType === 'month') {
                $query->whereYear('start_date', substr($filterValue, 0, 4))
                    ->whereMonth('start_date', substr($filterValue, 5, 2));
            } elseif ($filterType === 'year') {
                $query->whereYear('start_date', $filterValue);
            }
        }

        // Filtro por cargo
        if ($request->filled('cargo')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('cargo', $request->cargo);
            });
        }

        // Búsqueda por nombre de empleado (exacta)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', $searchTerm);
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        // Obtener lista única de cargos para el filtro
        $cargos = User::whereNotNull('cargo')
            ->distinct()
            ->pluck('cargo')
            ->sort()
            ->values();

        return view('requests.history', compact('requests', 'cargos'));
    }

    /**
     * Vista de papelera
     */
    public function trash(Request $request)
    {
        $query = VehicleRequest::onlyTrashed()
            ->with(['user', 'vehicle', 'conductor', 'vehicleReturn'])
            ->whereIn('status', ['completed', 'approved'])
            ->orderBy('deleted_at', 'desc');

        // Aplicar mismos filtros que en history
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', $searchTerm);
            });
        }

        if ($request->filled('filter_type') && $request->filled('filter_value')) {
            $filterType = $request->filter_type;
            $filterValue = $request->filter_value;

            if ($filterType === 'day') {
                $query->whereDate('start_date', $filterValue);
            } elseif ($filterType === 'month') {
                $query->whereYear('start_date', substr($filterValue, 0, 4))
                    ->whereMonth('start_date', substr($filterValue, 5, 2));
            } elseif ($filterType === 'year') {
                $query->whereYear('start_date', $filterValue);
            }
        }

        if ($request->filled('cargo')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('cargo', $request->cargo);
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        $cargos = User::whereNotNull('cargo')
            ->distinct()
            ->pluck('cargo')
            ->sort()
            ->values();

        return view('requests.trash', compact('requests', 'cargos'));
    }

    /**
     * Soft delete - mover a papelera
     */
    public function destroy($id)
    {
        $request = VehicleRequest::findOrFail($id);
        $request->delete();

        return redirect()->route('requests.history.index')
            ->with('success', 'Solicitud movida a la papelera.');
    }

    /**
     * Restaurar desde papelera
     */
    public function restore($id)
    {
        $request = VehicleRequest::withTrashed()->findOrFail($id);
        $request->restore();

        return redirect()->route('requests.history.trash')
            ->with('success', 'Solicitud restaurada exitosamente.');
    }

    /**
     * Eliminar permanentemente
     */
    public function forceDelete($id)
    {
        $request = VehicleRequest::withTrashed()->findOrFail($id);
        $request->forceDelete();

        return redirect()->route('requests.history.trash')
            ->with('success', 'Solicitud eliminada permanentemente.');
    }
}
