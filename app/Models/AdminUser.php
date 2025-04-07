<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'email', 'password', 'admin_level'];
    protected $table = 'admin_user';

    public function cinemas()
    {
        return $this->belongsToMany(Cinema::class, 'manage', 'admin_id', 'cinema_id');
    }
}
