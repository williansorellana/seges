<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowHistory extends Model
{
    protected $guarded = [];

    public function workflowable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}