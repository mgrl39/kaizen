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
        ];

        foreach ($admins as $adminData) {
            User::create($adminData);
        }
    }
}