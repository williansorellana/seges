<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutePlanningCompanion extends Model
{
    protected $guarded = [];

    protected $casts = ['receives_amipass' => 'boolean'];

    public function routePlanning()
    {
        return $this->belongsTo(RoutePlanning::class);
    }
}
