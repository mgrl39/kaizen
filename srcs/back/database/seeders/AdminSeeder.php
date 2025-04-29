<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Admin Principal',
                'username' => 'admin',
                'email' => 'admin@kaizen.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ],
            [
                'name' => 'Gestor Madrid',
                'username' => 'madrid_admin',
                'email' => 'madrid@kaizen.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ],
            [
                'name' => 'Gestor Barcelona',
                'username' => 'barcelona_admin',
                'email' => 'barcelona@kaizen.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ],
            [
                'name' => 'Gestor Valencia',
                'username' => 'valencia_admin',
                'email' => 'valencia@kaizen.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ],
            [
                'name' => 'Gestor Sevilla',
                'username' => 'sevilla_admin',
                'email' => 'sevilla@kaizen.com',
                'password' => Hash::make('password123'),
                'role' => 'admin'
            ]
        ];

        foreach ($admins as $adminData) {
            User::create($adminData);
        }
    }
} 