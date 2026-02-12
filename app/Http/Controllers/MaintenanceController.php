<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

use App\Services\MaintenanceService;

class MaintenanceController extends Controller
{
    public function updateState(Request $request, Vehicle $vehicle)
    {
        // Sanear entradas (eliminar puntos de los separadores de miles)
        $request->merge([
            'last_oil_change_km' => $request->last_oil_change_km ? str_replace('.', '', $request->last_oil_change_km) : null,
            'next_oil_change_km' => $request->next_oil_change_km ? str_replace('.', '', $request->next_oil_change_km) : null,
        ]);

        $validated = $request->validate([
            'last_oil_change_km' => 'nullable|integer|min:0',
            'next_oil_change_km' => 'nullable|integer|min:0',
            'tire_status_front' => 'required|in:good,fair,poor',
            'tire_status_rear' => 'required|in:good,fair,poor',
        ]);

        $vehicle->currentMaintenanceState()->updateOrCreate(
            ['vehicle_id' => $vehicle->id],
            $validated
        );

        // Check for maintenance alerts immediately
        (new MaintenanceService)->checkAndNotify();

        return back()->with('success', 'Estado de mantenimiento actualizado.');
    }

    public function storeRequest(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'type' => 'required|in:oil,tires,mechanics,general',
            'description' => 'required|string|max:1000',
        ]);

        $vehicle->maintenanceRequests()->create([
            'type' => $validated['type'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Solicitud de mantenimiento creada.');
    }

    public function acceptRequest($id)
    {
        $maintenanceRequest = \App\Models\MaintenanceRequest::findOrFail($id);

        // Actualizar estado de la solicitud
        $maintenanceRequest->update(['status' => 'in_progress']);

        // Actualizar estado del vehículo
        if ($maintenanceRequest->vehicle) {
            $maintenanceRequest->vehicle->update(['status' => 'maintenance']);
        }

        return back()->with('success', 'Solicitud aceptada. El vehículo ha pasado a mantenimiento.');
    }

    public function complete(Request $request, Vehicle $vehicle)
    {
        // 1. Sanear entradas
        $request->merge([
            'last_oil_change_km' => $request->last_oil_change_km ? str_replace('.', '', $request->last_oil_change_km) : null,
            'next_oil_change_km' => $request->next_oil_change_km ? str_replace('.', '', $request->next_oil_change_km) : null,
        ]);

        // 2. Validar datos básicos
        $validated = $request->validate([
            'last_oil_change_km' => 'nullable|integer|min:0',
            'next_oil_change_km' => 'required|integer|min:0',
            'tire_status_front' => 'required|in:good,fair,poor',
            'tire_status_rear' => 'required|in:good,fair,poor',
        ]);

        // 3. Validaciones estricas para finalizar
        $errors = [];

        // A) Aceite: El próximo cambio debe ser MAYOR al kilometraje actual (futuro)
        if ($validated['next_oil_change_km'] <= $vehicle->mileage) {
            $errors['next_oil_change_km'] = 'Para finalizar, el próximo cambio de aceite debe ser mayor al kilometraje actual (' . number_format($vehicle->mileage, 0, '', '.') . ' km).';
        }

        // B) Neumáticos: Deben estar en buen estado
        if ($validated['tire_status_front'] !== 'good') {
            $errors['tire_status_front'] = 'Los neumáticos delanteros deben estar en estado "Bueno" para finalizar.';
        }
        if ($validated['tire_status_rear'] !== 'good') {
            $errors['tire_status_rear'] = 'Los neumáticos traseros deben estar en estado "Bueno" para finalizar.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput()->with('error', 'No se puede finalizar el mantenimiento. Verifique los requisitos.');
        }

        // 4. Actualizar estado de mantenimiento con los nuevos datos
        $vehicle->currentMaintenanceState()->updateOrCreate(
            ['vehicle_id' => $vehicle->id],
            $validated
        );

        // 5. Completar solicitudes y liberar vehículo
        $vehicle->maintenanceRequests()
            ->where('status', 'in_progress')
            ->update(['status' => 'completed']);

        $vehicle->update(['status' => 'available']);

        // 6. Limpiar notificaciones
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('data->vehicle_id', $vehicle->id)
            ->delete();

        return back()->with('success', 'Mantenimiento finalizado exitosamente. Vehículo liberado.');
    }

    public function history(Request $request, Vehicle $vehicle)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $hasDamages = $request->input('has_damages');

        // 1. Filtrar solicitudes de mantención
        $requestsQuery = $vehicle->maintenanceRequests()
            ->orderBy('created_at', 'desc');

        if ($startDate) {
            $requestsQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $requestsQuery->whereDate('created_at', '<=', $endDate);
        }

        $requests = $requestsQuery->get();

        // 2. Filtrar historial de uso/reservas (y devoluciones)
        $usageQuery = $vehicle->reservations()
            ->with(['user', 'conductor', 'vehicleReturn', 'fuelLoads', 'completedBy','companions.user'])
            ->orderBy('start_date', 'desc');

        if ($startDate) {
            $usageQuery->whereDate('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $usageQuery->whereDate('start_date', '<=', $endDate);
        }

        // Filtro específico para Daños (solo afecta si se busca daños en Devoluciones)
        if ($hasDamages === 'yes') {
            $usageQuery->whereHas('vehicleReturn', function ($q) {
                $q->where('body_damage_reported', true);
            });
        } elseif ($hasDamages === 'no') {
            $usageQuery->whereHas('vehicleReturn', function ($q) {
                $q->where('body_damage_reported', false);
            });
        }

        $usageHistory = $usageQuery->get();;

        return view('vehicles.maintenance_history', compact('vehicle', 'requests', 'usageHistory'));
    }
    public function downloadHistoryPdf(Request $request, Vehicle $vehicle)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $hasDamages = $request->input('has_damages');
        $tab = $request->input('tab', 'maintenance'); // Pestaña activa

        // Datos para la vista PDF
        $data = [
            'vehicle' => $vehicle,
            'tab' => $tab,
            'filters' => [],
            'requests' => collect([]),
            'usageHistory' => collect([]),
            'returns' => collect([]),
            'generatedDate' => now()->format('d/m/Y H:i'),
        ];

        // Construir string de filtros aplicados
        if ($startDate)
            $data['filters'][] = "Desde: " . \Carbon\Carbon::parse($startDate)->format('d/m/Y');
        if ($endDate)
            $data['filters'][] = "Hasta: " . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
        if ($hasDamages === 'yes')
            $data['filters'][] = "Solo con daños";
        if ($hasDamages === 'no')
            $data['filters'][] = "Sin daños";

        // Lógica de obtención de datos según la pestaña
        if ($tab === 'maintenance') {
            $requestsQuery = $vehicle->maintenanceRequests()->orderBy('created_at', 'desc');
            if ($startDate)
                $requestsQuery->whereDate('created_at', '>=', $startDate);
            if ($endDate)
                $requestsQuery->whereDate('created_at', '<=', $endDate);
            $data['requests'] = $requestsQuery->get();

        } elseif ($tab === 'usage' || $tab === 'returns') {
            $usageQuery = $vehicle->reservations()
                ->with(['user', 'conductor', 'vehicleReturn', 'fuelLoads'])
                ->orderBy('start_date', 'desc');

            if ($startDate)
                $usageQuery->whereDate('start_date', '>=', $startDate);
            if ($endDate)
                $usageQuery->whereDate('start_date', '<=', $endDate);

            // Filtrar por daños si aplica (solo tiene sentido si hay devolución, pero se puede filtrar la query base)
            if ($hasDamages === 'yes') {
                $usageQuery->whereHas('vehicleReturn', function ($q) {
                    $q->where('body_damage_reported', true);
                });
            } elseif ($hasDamages === 'no') {
                $usageQuery->whereHas('vehicleReturn', function ($q) {
                    $q->where('body_damage_reported', false);
                });
            }

            $usageResults = $usageQuery->get();

            if ($tab === 'usage') {
                $data['usageHistory'] = $usageResults;
            } else {
                // Filtrar solo las que tienen devolución para la pestaña de devoluciones
                $data['returns'] = $usageResults->filter(fn($u) => $u->vehicleReturn);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vehicles.history-pdf', $data);
        $pdf->setPaper('letter', 'landscape'); // Tamaño Carta (Letter)

        // Traducir nombre del tab para el archivo
        $tabNameEs = match ($tab) {
            'maintenance' => 'mantenciones',
            'usage' => 'uso',
            'returns' => 'devoluciones',
            default => 'historial'
        };

        $filename = "historial_{$tabNameEs}_{$vehicle->plate}_" . now()->format('YmdHis') . ".pdf";
        return $pdf->download($filename);
    }
}
