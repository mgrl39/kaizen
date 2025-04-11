<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'booking_id'
    ];

    /**
     * Obtener el usuario que realizó esta reserva.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener los asientos asociados a esta reserva.
     */
    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seat');
    }
}
