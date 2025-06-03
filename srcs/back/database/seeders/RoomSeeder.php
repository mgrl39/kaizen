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
                'has_imax' => true,
                'has_vip' => true,
                'opening_hours' => '00:00-23:59',
                'features' => json_encode([
                    'Parking gratuito',
                    'Cafetería',
                    'Proyección 3D',
                    'Salas IMAX',
                    'Salas VIP'
                ])
            ]
        );
        
        // Crear las salas
        $rooms = [
            [
                'name' => 'Sala 1 - Standard',
                'type' => 'standard',
                'rows' => 8,
                'seats_per_row' => 12,
                'price' => 8.00,
                'cinema_id' => $cinema->id
            ],
            [
                'name' => 'Sala 2 - 3D',
                'type' => '3d',
                'rows' => 7,
                'seats_per_row' => 10,
                'price' => 10.00,
                'cinema_id' => $cinema->id
            ],
            [
                'name' => 'Sala 3 - IMAX',
                'type' => 'imax',
                'rows' => 10,
                'seats_per_row' => 15,
                'price' => 12.00,
                'cinema_id' => $cinema->id
            ],
            [
                'name' => 'Sala 4 - VIP',
                'type' => 'vip',
                'rows' => 5,
                'seats_per_row' => 8,
                'price' => 15.00,
                'cinema_id' => $cinema->id
            ]
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
        
        $this->command->info('Se han creado las salas del cine:');
        foreach ($rooms as $room) {
            $totalSeats = $room['rows'] * $room['seats_per_row'];
            $this->command->info("- {$room['name']}: {$room['rows']} filas × {$room['seats_per_row']} asientos = {$totalSeats} asientos totales - Precio: {$room['price']}€");
        }
    }
} 