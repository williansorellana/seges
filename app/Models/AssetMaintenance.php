<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'activo_id',
        'tipo',
        'descripcion',
        'fecha',
        'costo',
        'evidencia_path',
        'detalles_solucion',
        'fecha_termino',
        'created_by',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_termino' => 'date',
    ];

    /**
     * Relación con activo
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'activo_id');
    }

    /**
     * Relación con creador (responsable)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con fotos de finalización de mantención
     */
    public function photos()
    {
        return $this->hasMany(MaintenancePhoto::class, 'maintenance_id');
    }
}
