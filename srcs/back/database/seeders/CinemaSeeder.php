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
            [
                'name' => 'Yelmo Cines Ideal',
                'location' => 'Madrid',
                'address' => 'Calle del Doctor Cortezo, 6, 28012',
                'phone' => '+34 902 22 09 22',
                'image_url' => 'https://source.unsplash.com/random/500x300/?cinema,2',
                'rooms_count' => 8,
                'has_3d' => true,
                'has_imax' => false,
                'has_vip' => true,
            ],
            [
                'name' => 'Cines Callao',
                'location' => 'Madrid',
                'address' => 'Plaza del Callao, 3, 28013',
                'phone' => '+34 915 31 95 00',
                'image_url' => 'https://source.unsplash.com/random/500x300/?cinema,3',
                'rooms_count' => 5,
                'has_3d' => false,
                'has_imax' => false,
                'has_vip' => true,
            ],
            [
                'name' => 'Cinesa Diagonal',
                'location' => 'Barcelona',
                'address' => 'Av. Diagonal, 3, 08019',
                'phone' => '+34 931 22 33 96',
                'image_url' => 'https://source.unsplash.com/random/500x300/?cinema,4',
                'rooms_count' => 10,
                'has_3d' => true,
                'has_imax' => true,
                'has_vip' => true,
            ],
            [
                'name' => 'Cines Aragonia',
                'location' => 'Zaragoza',
                'address' => 'Avda. Juan Carlos I, 44, 50009',
                'phone' => '+34 976 56 15 83',
                'image_url' => 'https://source.unsplash.com/random/500x300/?cinema,5',
                'rooms_count' => 7,
                'has_3d' => true,
                'has_imax' => false,
                'has_vip' => false,
            ],
            [
                'name' => 'OCine Aqua',
                'location' => 'Valencia',
                'address' => 'C.C. Aqua Multiespacio, Calle Menorca, 19, 46023',
                'phone' => '+34 963 44 66 02',
                'image_url' => 'https://source.unsplash.com/random/500x300/?cinema,6',
                'rooms_count' => 9,
                'has_3d' => true,
                'has_imax' => false,
                'has_vip' => true,
            ],
        ];

        foreach ($cinemas as $cinema) {
            Cinema::create($cinema);
        }
    }
} 