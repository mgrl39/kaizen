<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'admin_level'
    ];

    /**
     * Los atributos que deben ocultarse en la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Obtener los cines gestionados por este administrador.
     */
    public function cinemas()
    {
        return $this->belongsToMany(Cinema::class, 'manage', 'admin_id', 'cinema_id');
    }
}
