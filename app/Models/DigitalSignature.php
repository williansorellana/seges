<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalSignature extends Model
{
    protected $guarded = [];

    protected $casts = [
        'signed_at' => 'datetime',
        'snapshot' => 'array',
        'signed_snapshot' => 'array',
        'signed_snapshots' => 'array',
    ];

    public function signable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}