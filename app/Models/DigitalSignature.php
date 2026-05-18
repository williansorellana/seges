<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalSignature extends Model
{
    protected $guarded = [];

    protected $casts = [
        'snapshot' => 'array',

        'signed_at' => 'datetime',
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