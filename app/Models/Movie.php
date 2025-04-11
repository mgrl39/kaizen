<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'synopsis',
        'duration',
        'rating',
        'release_date',
        'photo_url'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'release_date' => 'date',
        'duration' => 'integer'
    ];

    /**
     * Obtener los géneros asociados con esta película.
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    /**
     * Obtener los actores asociados con esta película.
     */
    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'movie_actor');
    }

    /**
     * Obtener las funciones programadas para esta película.
     */
    public function functions()
    {
        return $this->hasMany(Functions::class);
    }
}
