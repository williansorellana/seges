<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRequestCompanion extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_request_id',
        'user_id',
        'external_name',
        'external_rut',
        'external_position',
        'external_department',
    ];

    /**
     * Relación con la solicitud de vehículo
     */
    public function vehicleRequest()
    {
        return $this->belongsTo(VehicleRequest::class);
    }

    /**
     * Relación con el usuario (si es interno)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor para obtener el nombre del acompañante
     * (unifica usuarios internos y externos)
     */
    public function getNameAttribute()
    {
        if ($this->user_id && $this->user) {
            return $this->user->name . ' ' . $this->user->last_name;
        }

        return $this->external_name ?? 'Sin nombre';
    }

    /**
     * Accessor para obtener el RUT/identificación
     */
    public function getIdentificationAttribute()
    {
        if ($this->user_id && $this->user) {
            return $this->user->rut ?? 'N/A';
        }

        return $this->external_rut ?? 'N/A';
    }
}
