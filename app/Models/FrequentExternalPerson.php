<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FrequentExternalPerson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'frequent_external_persons';

    protected $fillable = [
        'name',
        'rut',
        'position',
        'department',
    ];

    protected $dates = ['deleted_at'];
}
