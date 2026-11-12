<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanningException extends Model
{
    protected $guarded = [];

    protected $casts = ['valid_on' => 'date', 'decided_at' => 'datetime', 'used_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
