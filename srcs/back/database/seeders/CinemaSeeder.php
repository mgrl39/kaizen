<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cinema;
use App\Models\User;

class CinemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cinemas = [
            [
                'name' => 'Cinesa La Maquinista',
                'location' => 'Barcelona',
                'address' => 'C.C. La Maquinista, Calle Potosí, 2, 08030',
                'phone' => '+34 932 25 17 88',
                'image_url' => 'https://source.unsplash.com/random/500x300/?cinema,1',
                'rooms_count' => 12,
                'has_3d' => true,
                'has_imax' => true,
                'has_vip' => true,
            ],
        ];

        foreach ($cinemas as $cinema) {
            Cinema::create($cinema);
        }
    }
}