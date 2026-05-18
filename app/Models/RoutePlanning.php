<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutePlanning extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function observations()
    {
        return $this->morphMany(RenditionObservation::class, 'observable');
    }

    public function signatures()
    {
        return $this->morphMany(DigitalSignature::class, 'signable');
    }

    public function workflowHistories()
    {
        return $this->morphMany(WorkflowHistory::class, 'workflowable');
    }
}
