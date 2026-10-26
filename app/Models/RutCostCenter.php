<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutCostCenter extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function center()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }
}
