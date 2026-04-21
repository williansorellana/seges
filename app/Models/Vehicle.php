<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\VehicleMaintenanceState;
use App\Models\MaintenanceRequest;
use App\Models\VehicleRequest;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plate',
        'serial_number',
        'brand',
        'model',
        'year',
        'mileage',
        'status',
        'image_path',
        'fuel_type',
    ];

    /**
     * Obtiene los atributos que deben ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    /**
     * Obtiene las reservas asociadas al vehículo.
     */
    public function reservations()
    {
        return $this->hasMany(VehicleRequest::class);
    }

    public function documents()
    {
        return $this->hasMany(VehicleDocument::class);
    }

    /**
     * Verifica si el vehículo está disponible en un rango de fechas.
     */
    public function isAvailable($startDate, $endDate)
    {
        return !$this->reservations()
            ->whereIn('status', ['approved', 'in_trip'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }
    /**
     * Obtiene el estado para mostrar (incluyendo reservas activas).
     */
    /**
     * Obtiene el estado para mostrar (incluyendo reservas activas).
     */
    /**
     * Obtiene el estado para mostrar (incluyendo reservas activas y documentos).
     */
    public function getDisplayStatusAttribute()
    {
        // 1. Estado manual (falla mecánica, mantención, etc.)
        if ($this->status !== 'available') {
            return $this->status;
        }

        // 2. Reserva Activa (ocupado)
        $activeReservation = $this->getActiveReservationAttribute();

        if ($activeReservation) {
            return 'occupied';
        }

        return 'available';
    }

    /**
     * Verifica si tiene documentos vencidos.
     */
    public function hasExpiredDocuments()
    {
        return $this->documents()->where('expires_at', '<', now()->startOfDay())->exists();
    }

    /**
     * Verifica si tiene documentos por vencer en 7 días.
     */
    public function hasWarningDocuments()
    {
        return $this->documents()
            ->where('expires_at', '>=', now()->startOfDay())
            ->where('expires_at', '<=', now()->addDays(7)->endOfDay())
            ->exists();
    }

    /**
     * Obtiene la reserva activa actual (si existe).
     */
    public function getActiveReservationAttribute()
    {
        return $this->reservations()
            ->whereIn('status', ['approved', 'in_trip'])
            ->where('start_date', '<=', now()->endOfDay()) // Por si la reserva empieza hoy más tarde
            ->where('end_date', '>=', now()->startOfDay()) // Incluir todo el día de término
            ->with(['user', 'conductor'])
            ->orderBy('start_date', 'asc') // Tomar la más cercana
            ->first();
    }

    /**
     * Obtiene la reserva efectiva (activa o próxima) que justifica el estado 'ocupado'.
     */
    public function getEffectiveReservationAttribute()
    {
        // 1. Intentar obtener la activa real
        $active = $this->getActiveReservationAttribute();
        if ($active) {
            return $active;
        }

        // 2. Si no hay activa pero el vehículo está marcado como ocupado,
        // buscar la próxima reserva aprobada (aunque empiece en el futuro).
        if ($this->status === 'occupied') {
            return $this->reservations()
                ->whereIn('status', ['approved', 'in_trip'])
                ->where('end_date', '>=', now()->startOfDay()) // Que no haya terminado ayer
                ->with(['user', 'conductor'])
                ->orderBy('start_date', 'asc')
                ->first();
        }

        return null;
    }


    public function currentMaintenanceState()
    {
        return $this->hasOne(VehicleMaintenanceState::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function fuelLoads()
    {
        return $this->hasMany(FuelLoad::class);
    }

    public function getAverageEfficiencyAttribute()
    {
        // Get all fuel loads with a calculated efficiency
        $efficiencies = $this->fuelLoads()
            ->whereNotNull('efficiency_km_l')
            ->where('efficiency_km_l', '>', 0)
            ->pluck('efficiency_km_l');

        return $efficiencies->avg();
    }

    public function getMaintenanceRemainingKmAttribute()
    {
        if (!$this->currentMaintenanceState || !$this->currentMaintenanceState->next_oil_change_km) {
            return null;
        }

        return $this->currentMaintenanceState->next_oil_change_km - $this->mileage;
    }

    public function getCostPerKmAttribute()
    {
        // 1. Get loads that have efficiency calculated (implying we know the distance for those loads)
        // We need to re-calculate distance for each load to get accurate cost/km for that segment.
        // Or simpler: Total Cost of ALL loads / Total Liters of ALL loads * Total Liters / Total Km?
        // Let's use: Sum(Total Cost) / Sum(Distance covered by these loads).

        $loads = $this->fuelLoads()->orderBy('date', 'asc')->get();

        if ($loads->count() < 2) {
            return null;
        }

        // Simplification: We take the mileage range covered by the registered fuel loads.
        // Min mileage in loads vs Max mileage in loads.
        $minMileage = $loads->min('mileage');
        $maxMileage = $loads->max('mileage');
        $totalDistance = $maxMileage - $minMileage;

        if ($totalDistance <= 0) {
            return null;
        }

        // We should exclude the FIRST load's cost from the calculation if we consider 
        // that the first load "fills" the tank for FUTURE driving, but we don't know the distance BEFORE it.
        // Actually, usually:
        // Load 1: 1000km, 50L, $50000. (We don't know distance before this).
        // Load 2: 1500km, 40L, $40000. (Distance 500km. The 40L refilled what was used for those 500km).
        // So Cost for those 500km is the cost of the REFILL (Load 2)? Or the cost of the previous fill?
        // Standard method: Cost is the price of the fuel consumed.
        // If we fill up at Load 2 ($40000), that pays for the 500km we just drove (assuming full tank strategy).

        // Strategy: Sum costs of loads 2..N. Sum distances of loads 2..N.
        // Distance for Load i = Load[i].mileage - Load[i-1].mileage.
        // Cost for that distance = Load[i].total_cost.

        $totalCost = 0;
        $totalDist = 0;

        for ($i = 1; $i < $loads->count(); $i++) {
            $prev = $loads[$i - 1];
            $curr = $loads[$i];

            $dist = $curr->mileage - $prev->mileage;
            if ($dist > 0) {
                $totalDist += $dist;
                $totalCost += $curr->total_cost;
            }
        }

        if ($totalDist == 0)
            return null;

        return $totalCost / $totalDist;
    }
}
