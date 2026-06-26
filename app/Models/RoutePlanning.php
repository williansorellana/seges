<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutePlanning extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',

        'requires_funds' => 'boolean',
        'requested_funds' => 'decimal:2',
        'funds_peaje' => 'decimal:2',
        'funds_bencina' => 'decimal:2',
        'funds_alojamiento' => 'decimal:2',
        'funds_alimentacion' => 'decimal:2',
        'funds_otros' => 'decimal:2',

        'destinations' => 'array',

        'requires_amipass' => 'boolean',
        'amipass_days' => 'integer',
        'amipass_business_days' => 'integer',
        'amipass_amount' => 'decimal:2',

        'signed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

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

    public function digitalSignatures()
    {
        return $this->morphMany(DigitalSignature::class, 'signable');
    }

    public function workflowHistories()
    {
        return $this->morphMany(WorkflowHistory::class, 'workflowable');
    }

    public function rendition()
    {
        return $this->hasOne(\App\Models\Rendition::class);
    }
}