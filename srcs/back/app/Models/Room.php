<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'cinema_id',
        'name',
        'capacity',
        'features'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'json'
    ];

    /**
     * Obtener el cine al que pertenece esta sala.
     */
    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    /**
     * Obtener las funciones programadas en esta sala.
     */
    public function functions()
    {
        return $this->hasMany(Functions::class);
    }

    /**
     * Obtener los asientos de esta sala.
     */
    public function seats()
    {
        return $this->hasManyThrough(Seat::class, Functions::class);
    }
}
