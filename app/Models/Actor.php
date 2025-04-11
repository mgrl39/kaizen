<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'biography',
        'photo_url'
    ];

    /**
     * Obtener las películas en las que ha participado este actor.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_actor');
    }
}
