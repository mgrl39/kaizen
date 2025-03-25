<?php

namespace App\DTOs;

class UserDTO
{
    public $id;
    public $username;
    public $email;
    public $birthdate;

    public function __construct($user)
    {
        $this->id = $user->id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->birthdate = $user->birthdate;
        // No exponemos password por seguridad
    }
} 