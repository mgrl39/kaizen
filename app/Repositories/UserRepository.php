<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'name' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'birthdate' => $data['birthdate'] ?? now()->format('Y-m-d')
        ]);
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }
} 