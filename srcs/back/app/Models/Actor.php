<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'photo_url',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($actor) {
            if (empty($actor->slug)) {
                $actor->slug = Str::slug($actor->name);
            }
        });

        static::updating(function ($actor) {
            if ($actor->isDirty('name')) {
                $actor->slug = Str::slug($actor->name);
            }
        });
    }

    /**
     * Obtener las películas asociadas con este actor.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_actor');
    }
}
