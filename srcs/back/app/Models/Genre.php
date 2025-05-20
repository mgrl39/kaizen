<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Genre extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($genre) {
            if (empty($genre->slug)) {
                $genre->slug = Str::slug($genre->name);
            }
        });

        static::updating(function ($genre) {
            if ($genre->isDirty('name')) {
                $genre->slug = Str::slug($genre->name);
            }
        });
    }

    /**
     * Obtener las películas asociadas con este género.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_genre');
    }
}
