<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'username' => 'admin',
            'email' => 'admin@kaizen.com',
            'password' => Hash::make('admin123'),
            'name' => 'Admin',
            'role' => 'admin'
        ]);

        // Crear usuario normal
        User::create([
            'username' => 'morad',
            'email' => 'morad@gmail.com',
            'password' => Hash::make('morad123'),
            'name' => 'Morad',
            'role' => 'user'
        ]);

        // Crear usuario de prueba
        User::create([
            'username' => 'test',
            'email' => 'test@kaizen.com',
            'password' => Hash::make('test123'),
            'name' => 'Test User',
            'role' => 'user'
        ]);
    }
} 