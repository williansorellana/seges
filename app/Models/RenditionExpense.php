<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RenditionExpense extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'is_valid' => 'boolean',
    ];

    public function rendition()
    {
        return $this->belongsTo(\App\Models\Rendition::class);
    }
}