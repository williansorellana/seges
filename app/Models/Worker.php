<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Representa trabajadores externos o internos para asignación de activos.
 */
class Worker extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'rut',
        'departamento',
        'cargo',
    ];
}
