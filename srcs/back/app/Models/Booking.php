<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'function_id',
        'uuid',
        'booking_code',
        'total_price',
        'seats',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'user_id'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'seats' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Obtener el usuario que realizó esta reserva.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la función asociada a esta reserva.
     */
    public function function(): BelongsTo
    {
        return $this->belongsTo(Functions::class, 'function_id');
    }

    /**
     * Obtener los asientos asociados a esta reserva.
     */
    public function seats(): BelongsToMany
    {
        return $this->belongsToMany(Seat::class, 'booking_seats')
            ->withTimestamps()
            ->withPivot(['price']);
    }

    /**
     * Generar un código único para la reserva
     */
    public static function generateBookingCode()
    {
        $prefix = 'KZN';
        $timestamp = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return $prefix . $timestamp . $random;
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
