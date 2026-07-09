<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLock extends Model
{
    protected $guarded = [];

    protected $casts = [
        'locked_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function lockable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar bloqueos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
