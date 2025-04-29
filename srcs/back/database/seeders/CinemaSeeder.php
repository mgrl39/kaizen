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
        // Datos de ejemplo de cines en España
        $cinemas = [
            [
                'name' => 'Cinesa La Maquinista',
                'location' => 'Barcelona, Centro Comercial La Maquinista'
            ],
            [
                'name' => 'Yelmo Cines Ideal',
                'location' => 'Madrid, Calle Doctor Cortezo, 6'
            ],
            [
                'name' => 'Kinépolis Ciudad de la Imagen',
                'location' => 'Pozuelo de Alarcón, Madrid'
            ],
            [
                'name' => 'Cines Callao',
                'location' => 'Madrid, Plaza del Callao, 3'
            ],
            [
                'name' => 'Cinesa Diagonal',
                'location' => 'Barcelona, Centro Comercial L\'illa Diagonal'
            ],
            [
                'name' => 'Cines Lys',
                'location' => 'Valencia, Passeig Russafa, 3'
            ],
            [
                'name' => 'Multicines Monopol',
                'location' => 'Las Palmas de Gran Canaria'
            ],
            [
                'name' => 'Cinesur Nervión Plaza',
                'location' => 'Sevilla, Centro Comercial Nervión Plaza'
            ],
            [
                'name' => 'Yelmo Cines Plaza Mayor',
                'location' => 'Málaga, Centro Comercial Plaza Mayor'
            ],
            [
                'name' => 'Cinebox Parque Astur',
                'location' => 'Avilés, Centro Comercial Parque Astur'
            ]
        ];

        foreach ($cinemas as $cinemaData) {
            $cinema = Cinema::create($cinemaData);

            // Asignar algunos administradores aleatoriamente a cada cine
            $admins = User::where('role', 'admin')->inRandomOrder()->take(rand(1, 3))->get();
            foreach ($admins as $admin) {
                $cinema->admins()->attach($admin->id);
            }
        }
    }
} 