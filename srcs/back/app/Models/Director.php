<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Director extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($director) {
            if (empty($director->slug)) {
                $director->slug = Str::slug($director->name);
            }
        });

        static::updating(function ($director) {
            if ($director->isDirty('name')) {
                $director->slug = Str::slug($director->name);
            }
        });
    }

    /**
     * Obtener las películas dirigidas por este director.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_director');
    }
} 