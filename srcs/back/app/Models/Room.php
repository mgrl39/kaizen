<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'type',
        'rows',
        'seats_per_row',
        'price',
        'cinema_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rows' => 'integer',
        'seats_per_row' => 'integer',
        'price' => 'decimal:2'
    ];

    /**
     * Get the cinema that owns the room.
     */
    public function cinema(): BelongsTo
    {
        return $this->belongsTo(Cinema::class);
    }

    /**
     * Get the functions for this room.
     */
    public function functions(): HasMany
    {
        return $this->hasMany(Functions::class);
    }

    /**
     * Get the seat layout for a specific function
     */
    public function getSeatLayoutForFunction(Functions $function)
    {
        Log::info('Starting seat layout generation', [
            'function_id' => $function->id,
            'room_id' => $this->id,
            'room_config' => [
                'rows' => $this->rows,
                'seats_per_row' => $this->seats_per_row
            ]
        ]);

        // Get occupied seats for this function
        $occupiedSeats = $function->bookings()
            ->where('status', 'confirmed')
            ->with('seats')
            ->get();

        Log::info('Retrieved bookings', [
            'function_id' => $function->id,
            'bookings_count' => $occupiedSeats->count()
        ]);

        // Create map of occupied seats
        $occupiedMap = collect();
        foreach ($occupiedSeats as $booking) {
            foreach ($booking->seats as $seat) {
                $occupiedMap->put("{$seat->row}-{$seat->number}", true);
            }
        }

        Log::info('Created occupied seats map', [
            'function_id' => $function->id,
            'occupied_count' => $occupiedMap->count(),
            'occupied_seats' => $occupiedMap->keys()->toArray()
        ]);

        // Generate complete layout
        $layout = [];
        for ($row = 1; $row <= $this->rows; $row++) {
            $rowSeats = [];
            for ($number = 1; $number <= $this->seats_per_row; $number++) {
                $seatKey = "$row-$number";
                $rowSeats[] = [
                    'id' => $seatKey,
                    'row' => $row,
                    'number' => $number,
                    'status' => $occupiedMap->has($seatKey) ? Seat::STATUS_OCCUPIED : Seat::STATUS_AVAILABLE
                ];
            }
            $layout[] = $rowSeats;
        }

        if (empty($layout)) {
            Log::error('Failed to generate layout', [
                'function_id' => $function->id,
                'room_id' => $this->id,
                'rows' => $this->rows,
                'seats_per_row' => $this->seats_per_row
            ]);
            return null;
        }

        Log::info('Successfully generated layout', [
            'function_id' => $function->id,
            'total_rows' => count($layout),
            'seats_per_row' => count($layout[0] ?? []),
            'total_seats' => $this->rows * $this->seats_per_row,
            'occupied_seats' => $occupiedMap->count()
        ]);

        return $layout;
    }
}
