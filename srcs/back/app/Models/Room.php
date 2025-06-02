<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Obtener el cine al que pertenece esta sala.
     */
    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    /**
     * Get the movie functions for the room.
     */
    public function movieFunctions()
    {
        return $this->hasMany(MovieFunction::class);
    }

    /**
     * Obtener los asientos de esta sala.
     */
    public function seats()
    {
        return $this->hasManyThrough(Seat::class, Functions::class);
    }
}
