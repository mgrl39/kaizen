<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Cinema;
use Illuminate\Support\Facades\Schema;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar datos existentes
        if (Schema::hasTable('rooms')) {
            Room::truncate();
        }
        
        // Crear un cine por defecto si no existe
        $cinema = Cinema::firstOrCreate(
            ['name' => 'Kaizen Cinema Mini'],
            [
                'location' => 'Madrid',
                'address' => 'Calle del Cine 42',
                'phone' => '+34 912 345 678',
                'email' => 'info@kaizencinema.com',
                'description' => 'Un acogedor cine de barrio con la mejor experiencia.',
                'has_3d' => true,
                'has_imax' => false,
                'has_vip' => false,
                'opening_hours' => '15:00-23:00',
                'features' => json_encode([
                    'Parking gratuito',
                    'Cafetería',
                    'Proyección 3D'
                ])
            ]
        );
        
        // Crear las salas
        $rooms = [
            [
                'name' => 'Sala 1',
                'type' => 'standard',
                'rows' => 6,
                'seats_per_row' => 8,
                'price' => 8.00,
                'cinema_id' => $cinema->id
            ],
            [
                'name' => 'Sala 2 - 3D',
                'type' => 'standard',
                'rows' => 5,
                'seats_per_row' => 7,
                'price' => 10.00,
                'cinema_id' => $cinema->id
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
        
        $this->command->info('Se han creado las salas del cine.');
    }
} 