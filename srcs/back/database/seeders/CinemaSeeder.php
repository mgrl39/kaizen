<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cinema;
use App\Models\Room;
use Illuminate\Support\Facades\Schema;

class CinemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si las tablas existen
        if (!Schema::hasTable('cinemas')) {
            $this->command->error('La tabla "cinemas" no existe. Ejecuta las migraciones primero con: php artisan migrate');
            return;
        }

        // Limpiar datos existentes
        Cinema::truncate();
        if (Schema::hasTable('rooms')) {
            Room::truncate();
        }
        
        // Crear nuestro único cine en La Maquinista
        $cinema = Cinema::create([
            'name' => 'Kaizen Cinema',
            'location' => 'Barcelona',
            'address' => 'Centro Comercial La Maquinista, Passeig de Potosí, 2, 08030 Barcelona',
            'phone' => '+34 933 608 920',
            'email' => 'info@kaizencinema.com',
            'description' => 'Bienvenidos a Kaizen Cinema, tu destino cinematográfico premium en La Maquinista. Disfruta de la mejor experiencia en cine con tecnología de última generación, sonido envolvente y máximo confort. Nuestras modernas instalaciones están diseñadas para ofrecerte una experiencia única en cada visita.',
            'image_url' => 'https://source.unsplash.com/random/1920x1080/?cinema,theater,auditorium',
            'has_3d' => true,
            'has_imax' => true,
            'has_vip' => true,
            'rooms_count' => 5,
            'opening_hours' => '10:00 - 00:00',
            'features' => json_encode([
                'Parking gratuito',
                'Cafetería',
                'Zona infantil',
                'Acceso para discapacitados',
                'Restaurante',
                'Zona de juegos',
                'WiFi gratuito'
            ])
        ]);

        // Crear las salas del cine
        if (Schema::hasTable('rooms')) {
            $rooms = [
                [
                    'name' => 'Sala 1 - IMAX',
                    'capacity' => 250,
                    'features' => json_encode(['IMAX', 'Dolby Atmos', '3D', 'Pantalla gigante']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 2 - VIP Premium',
                    'capacity' => 80,
                    'features' => json_encode(['VIP', 'Butacas reclinables', 'Servicio a butaca', 'Menú gourmet']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 3 - Dolby Cinema',
                    'capacity' => 180,
                    'features' => json_encode(['3D', 'Dolby Atmos', 'Dolby Vision', 'Butacas premium']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 4 - 3D Experience',
                    'capacity' => 160,
                    'features' => json_encode(['3D', 'Sonido envolvente', 'Pantalla curva']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 5 - Standard Plus',
                    'capacity' => 140,
                    'features' => json_encode(['Pantalla gigante', 'Sonido digital', 'Butacas confort']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 6 - Family Room',
                    'capacity' => 120,
                    'features' => json_encode(['Zona infantil', 'Asientos familiares', 'Sonido moderado']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 7 - VIP Couples',
                    'capacity' => 60,
                    'features' => json_encode(['Asientos dobles', 'Servicio premium', 'Ambiente íntimo']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 8 - ScreenX 270°',
                    'capacity' => 200,
                    'features' => json_encode(['Pantalla 270°', '3D', 'Sonido inmersivo']),
                    'cinema_id' => $cinema->id
                ],
                [
                    'name' => 'Sala 9 - 4DX',
                    'capacity' => 150,
                    'features' => json_encode(['4DX', '3D', 'Efectos especiales', 'Movimiento']),
                    'cinema_id' => $cinema->id
                ]
            ];

            foreach ($rooms as $room) {
                Room::create($room);
            }
        }
        
        $this->command->info('Se ha creado el cine Kaizen Cinema en La Maquinista con todas sus salas.');
    }
} 