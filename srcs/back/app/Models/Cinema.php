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
        'location',
        'address',
        'phone',
        'email',
        'description',
        'image_url',
        'has_3d',
        'has_imax',
        'has_vip',
        'opening_hours',
        'features'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'has_3d' => 'boolean',
        'has_imax' => 'boolean',
        'has_vip' => 'boolean',
        'features' => 'json'
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
