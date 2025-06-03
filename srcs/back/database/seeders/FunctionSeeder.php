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
    private const CLEANING_TIME = 10; // 10 minutos entre películas

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

        // Generar funciones para los próximos 3 días
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(2); // 3 días en total (0, 1, 2)
        $currentDate = $startDate->copy();

        // Array para trackear qué películas se han programado
        $scheduledMovies = [];

        while ($currentDate <= $endDate) {
            foreach ($rooms as $room) {
                // Inicializar el tiempo de inicio para este día y sala
                $currentTime = Carbon::parse($currentDate->format('Y-m-d') . ' 00:00');
                $dayEnd = $currentTime->copy()->addDay();

                while ($currentTime < $dayEnd) {
                    // Encontrar la siguiente película que podamos programar
                    $selectedMovie = null;
                    $lowestCount = PHP_INT_MAX;

                    foreach ($movies as $movie) {
                        $movieScheduleCount = $scheduledMovies[$movie->id] ?? 0;
                        if ($movieScheduleCount < $lowestCount) {
                            $selectedMovie = $movie;
                            $lowestCount = $movieScheduleCount;
                        }
                    }

                    // Crear la función
                    $function = Functions::create([
                        'movie_id' => $selectedMovie->id,
                        'room_id' => $room->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'time' => $currentTime->format('H:i'),
                        'is_3d' => $room->cinema->has_3d && $room->name === 'Sala 2 - 3D'
                    ]);

                    // Actualizar el contador de películas programadas
                    $scheduledMovies[$selectedMovie->id] = ($scheduledMovies[$selectedMovie->id] ?? 0) + 1;

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

                    // Avanzar el tiempo para la siguiente función
                    $currentTime->addMinutes($selectedMovie->duration + self::CLEANING_TIME);
                }
            }
            // Avanzar al siguiente día
            $currentDate->addDay();
        }

        // Mostrar estadísticas de programación
        $this->command->info('Estadísticas de programación:');
        foreach ($scheduledMovies as $movieId => $count) {
            $movie = $movies->find($movieId);
            $this->command->info("- {$movie->title}: {$count} funciones");
        }
    }
} 