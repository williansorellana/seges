<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RenditionObservation;

class Rendition extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'funds_received' => 'decimal:2',
        'total_declared' => 'decimal:2',
        'total_approved' => 'decimal:2',
        'difference' => 'decimal:2',

        'refund_to_company' => 'boolean',
        'refund_to_worker' => 'boolean',
        'payment_completed' => 'boolean',

        'refund_resolved_at' => 'datetime',
        'payment_completed_at' => 'datetime',
    ];

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