<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'status',
        'return_mileage',
        'destination_type',
        'conductor_id',
        'early_termination_reason',
        'original_end_date',
        'completed_by_user_id',
    ];

    /**
     * Obtiene conductor asociado (si aplica).
     */
    public function conductor()
    {
        return $this->belongsTo(Conductor::class);
    }

    /**
     * Get the user who completed (finished) the request.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    /**
     * Obtiene los atributos que deben ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'original_end_date' => 'datetime',
        ];
    }

    /**
     * Obtiene el usuario que hizo la solicitud.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el vehículo solicitado.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Obtiene las cargas de combustible asociadas a esta solicitud.
     */
    public function fuelLoads()
    {
        return $this->hasMany(FuelLoad::class);
    }

    /**
     * Obtiene el registro de entrega asociado a esta solicitud.
     */
    public function vehicleReturn()
    {
        return $this->hasOne(VehicleReturn::class);
    }
}
