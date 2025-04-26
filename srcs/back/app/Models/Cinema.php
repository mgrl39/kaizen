<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'cinemas';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'location'
    ];

    /**
     * Obtener las salas que pertenecen a este cine.
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Obtener los administradores que gestionan este cine.
     */
    public function admins()
    {
        return $this->belongsToMany(AdminUser::class, 'manage', 'cinema_id', 'admin_id');
    }
}
