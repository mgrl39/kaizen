<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Functions;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Cinema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FunctionSeeder extends Seeder
{
    private const OPENING_TIME = '15:00';
    private const CLOSING_TIME = '23:00';
    private const CLEANING_TIME = 15;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar funciones existentes
        Functions::truncate();

        // Obtener películas y salas
        $movies = Movie::all();
        $rooms = Room::all();

        if ($movies->isEmpty() || $rooms->isEmpty()) {
            return;
        }

        // Generar funciones para los próximos 7 días
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(7);
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
                foreach ($rooms as $room) {
                // Solo 3 funciones por día en cada sala
                $functionTimes = ['15:30', '18:00', '20:30'];
                
                foreach ($functionTimes as $index => $time) {
                    // Usar películas de forma rotativa
                    $movie = $movies[$index % $movies->count()];

                        // Crear la función
                        $function = Functions::create([
                            'movie_id' => $movie->id,
                            'room_id' => $room->id,
                            'date' => $currentDate->format('Y-m-d'),
                        'time' => $time,
                        'is_3d' => $room->cinema->has_3d && $room->name === 'Sala 2 - 3D'
                        ]);

                        // Crear asientos para esta función
                        $seatNumber = 1;
                        for ($row = 0; $row < $room->rows; $row++) {
                            for ($col = 0; $col < $room->seats_per_row; $col++) {
                                $function->seats()->create([
                                    'number' => $seatNumber,
                                    'row' => chr(65 + $row),
                                    'status' => 'available',
                                    'price' => $room->price + ($function->is_3d ? 2 : 0)
                                ]);
                                $seatNumber++;
                            }
                        }
                }
            }

            // Avanzar al siguiente día
            $currentDate->addDay();
        }
    }
} 