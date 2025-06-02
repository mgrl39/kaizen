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
        'function_id',
        'booking_code',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'customer_name',
        'customer_email',
        'customer_phone'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Los estados posibles de una reserva
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pendiente',
        self::STATUS_CONFIRMED => 'Confirmada',
        self::STATUS_CANCELLED => 'Cancelada',
        self::STATUS_COMPLETED => 'Completada'
    ];

    /**
     * Los estados posibles del pago
     */
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_COMPLETED = 'completed';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    /**
     * Obtener el usuario que realizó esta reserva.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener la función asociada a esta reserva.
     */
    public function function()
    {
        return $this->belongsTo(Functions::class);
    }

    /**
     * Obtener los asientos asociados a esta reserva.
     */
    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seats')
            ->withPivot('price')
            ->withTimestamps();
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
}
