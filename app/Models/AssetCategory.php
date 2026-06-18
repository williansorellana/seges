<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
    ];

    /**
     * Obtiene los activos de esta categoría
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'categoria_id');
    }
}
