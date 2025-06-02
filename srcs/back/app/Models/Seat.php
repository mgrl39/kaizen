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
        'row',
        'status',
        'price'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2'
    ];

    /**
     * Los estados posibles de un asiento
     */
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_OCCUPIED = 'occupied';

    /**
     * Obtener la función a la que pertenece este asiento.
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
        return $this->belongsToMany(Booking::class, 'booking_seats')
            ->withTimestamps()
            ->withPivot(['price']);
    }
}
