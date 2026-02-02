<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePhoto extends Model
{
    protected $fillable = [
        'maintenance_id',
        'photo_path',
    ];

    /**
     * Relación con AssetMaintenance
     */
    public function maintenance()
    {
        return $this->belongsTo(AssetMaintenance::class, 'maintenance_id');
    }

    /**
     * Accessor para obtener la URL pública de la foto
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->photo_path);
    }
}
