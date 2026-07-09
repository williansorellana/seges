<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RenditionObservation;
use App\Traits\Lockable;

class Rendition extends Model
{
    use HasFactory, Lockable;

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

    /**
     * Obtiene el auditor final de la rendición.
     */
    public function finalAuditor()
    {
        $history = $this->workflowHistories()
            ->whereIn('action', [
                'approved_by_finances',
                'rejected_by_finances',
                'approved_by_controlling',
                'rejected_by_controlling',
                'approved_by_jefatura',
                'rejected_by_jefatura',
                'payment_completed_by_finances'
            ])
            ->orderBy('id', 'desc')
            ->first();

        return $history ? $history->user : null;
    }
}