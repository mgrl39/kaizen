<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'photo_url',
        'slug'
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movie) {
            $movie->slug = Str::slug($movie->title);
        });

        static::updating(function ($movie) {
            if ($movie->isDirty('title')) {
                $movie->slug = Str::slug($movie->title);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

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
