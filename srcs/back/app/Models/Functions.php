<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Functions extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'movie_id',
        'room_id',
        'date',
        'time'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i'
    ];

    /**
     * Obtener la película asociada a esta función.
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Obtener la sala asociada a esta función.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Obtener los asientos para esta función.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class, 'function_id');
    }
}
