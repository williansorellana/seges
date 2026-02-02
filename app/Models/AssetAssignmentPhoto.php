<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AssetAssignmentPhoto extends Model
{
    protected $fillable = [
        'assignment_id',
        'photo_path',
    ];

    /**
     * Relación con la asignación
     */
    public function assignment()
    {
        return $this->belongsTo(AssetAssignment::class, 'assignment_id');
    }

    /**
     * Accesor para obtener la URL pública de la foto
     */
    public function getUrlAttribute()
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }
}
