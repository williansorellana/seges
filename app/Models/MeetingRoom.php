<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingRoom extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'capacity',
        'location',
        'image_path',
        'status',
    ];

    /**
 * Obtiene las reservas asociadas a la sala.
 */
    public function reservations()
    {
        return $this->hasMany(RoomReservation::class);
    }
}