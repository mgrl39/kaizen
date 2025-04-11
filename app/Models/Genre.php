<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Obtener las películas asociadas a este género.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_genre');
    }
}
