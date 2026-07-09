<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomReservationGuest extends Model
{
    protected $fillable = [
        'room_reservation_id',
        'name',
        'email',
    ];

    public function roomReservation()
    {
        return $this->belongsTo(RoomReservation::class);
    }
}