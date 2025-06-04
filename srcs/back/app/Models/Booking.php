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
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'total_amount'
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

    protected $attributes = [
        'status' => 'pending',
        'payment_status' => 'pending',
        'payment_method' => 'pending'
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
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
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
