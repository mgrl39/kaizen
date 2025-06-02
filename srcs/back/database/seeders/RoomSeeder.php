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
            ['name' => 'Kaizen Cinema Principal'],
            [
                'location' => 'Madrid',
                'address' => 'Calle Principal 123',
                'phone' => '+34 912 345 678',
                'email' => 'info@kaizencinema.com',
                'description' => 'El primer y principal cine de la cadena Kaizen Cinema.',
                'has_3d' => true,
                'has_imax' => true,
                'has_vip' => true,
                'opening_hours' => '10:00-00:00',
                'features' => json_encode([
                    'Parking gratuito',
                    'Cafetería',
                    'Zona VIP',
                    'Sala IMAX',
                    'Proyección 3D'
                ])
            ]
        );
        
        // Crear las salas
        $rooms = [
            [
                'name' => 'Sala 1 - IMAX',
                'type' => 'imax',
                'rows' => 15,
                'seats_per_row' => 20,
                'price' => 12.00,
                'cinema_id' => $cinema->id
            ],
            [
                'name' => 'Sala 2 - VIP',
                'type' => 'vip',
                'rows' => 8,
                'seats_per_row' => 12,
                'price' => 15.00,
                'cinema_id' => $cinema->id
            ],
            [
                'name' => 'Sala 3 - Standard',
                'type' => 'standard',
                'rows' => 12,
                'seats_per_row' => 15,
                'price' => 8.00,
                'cinema_id' => $cinema->id
            ],
            [
                'name' => 'Sala 4 - 3D',
                'type' => 'standard',
                'rows' => 10,
                'seats_per_row' => 15,
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