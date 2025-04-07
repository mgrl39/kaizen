<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['cinema_id', 'name', 'capacity'];

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function functions()
    {
        return $this->hasMany(Functions::class);
    }
}
