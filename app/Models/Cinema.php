<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;

    // Especificamos el nombre exacto de la tabla que ya existe
    protected $table = 'cinema';

    // Especificamos las columnas que podemos llenar
    protected $fillable = ['name', 'location'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function managers()
    {
        return $this->belongsToMany(AdminUser::class, 'manage', 'cinema_id', 'admin_id');
    }
}
