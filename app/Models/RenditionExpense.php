<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RenditionExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        $guard = function (self $expense) {
            abort_if($expense->rendition()->where('status', 'rejected')->exists(), 403, 'Los comprobantes del intento rechazado son inalterables.');
        };
        static::saving($guard);
        static::deleting($guard);
    }

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'authorized_amount' => 'decimal:2',
        'lodging_excess_amount' => 'decimal:2',
        'is_valid' => 'boolean',
    ];

    public function rendition()
    {
        return $this->belongsTo(\App\Models\Rendition::class);
    }
}
