<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RenditionObservation;

class Rendition extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function routePlanning()
    {
        return $this->belongsTo(RoutePlanning::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->hasMany(RenditionExpense::class);
    }

    public function observations()
    {
        return $this->morphMany(RenditionObservation::class, 'observable');
    }

    public function workflowHistories()
    {
        return $this->morphMany(\App\Models\WorkflowHistory::class, 'workflowable');
    }

    public function digitalSignatures()
    {
        return $this->morphMany(\App\Models\DigitalSignature::class, 'signable');
    }
}
