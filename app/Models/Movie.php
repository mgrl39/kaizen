<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'synopsis', 
        'duration', 
        'rating', 
        'release_date', 
        'photo_url'
    ];
    
    // Relaciones
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
    
    public function actors()
    {
        return $this->belongsToMany(Actor::class);
    }
}
