<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'function_id',
        'number',
        'seat_row',
        'status'
    ];

    /**
     * Los valores permitidos para el estado del asiento.
     *
     * @var array
     */
    public const STATUSES = [
        'available' => 'Disponible',
        'reserved' => 'Reservado',
        'occupied' => 'Ocupado'
    ];

    /**
     * Obtener la función asociada a este asiento.
     */
    public function function()
    {
        return $this->belongsTo(Functions::class, 'function_id');
    }

    /**
     * Obtener las reservas asociadas a este asiento.
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_seat');
    }
}
