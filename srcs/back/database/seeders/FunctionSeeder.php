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
    private const OPENING_TIME = '10:00';
    private const CLOSING_TIME = '23:00';
    private const CLEANING_TIME = 15;

    /**
     * Verifica si un día es laborable (lunes a viernes)
     */
    private function isBusinessDay(Carbon $date): bool
    {
        return !$date->isWeekend();
    }

    /**
     * Obtiene el siguiente día laborable
     */
    private function getNextBusinessDay(Carbon $date): Carbon
    {
        $nextDay = $date->copy()->addDay();
        while (!$this->isBusinessDay($nextDay)) {
            $nextDay->addDay();
        }
        return $nextDay;
    }

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

        // Generar funciones para los próximos 14 días
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(14);
        $currentDate = $startDate->copy();

        // Mantener un registro del último índice de película para cada sala
        $lastMovieIndices = [];

        while ($currentDate <= $endDate) {
            // Solo generar funciones para días laborables
            if ($this->isBusinessDay($currentDate)) {
                foreach ($rooms as $room) {
                    // Obtener el último índice de película para esta sala, o empezar desde 0
                    $roomKey = "room_{$room->id}";
                    $movieIndex = $lastMovieIndices[$roomKey] ?? 0;

                    // Generar horarios desde las 10:00 hasta las 23:00
                    $currentTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . self::OPENING_TIME);
                    $endTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . self::CLOSING_TIME);

                    while ($currentTime < $endTime) {
                        if ($movieIndex >= $movies->count()) {
                            $movieIndex = 0; // Volver al inicio de la lista de películas
                        }

                        $movie = $movies[$movieIndex];
                        
                        // Calcular fin de la película
                        $movieEndTime = (clone $currentTime)->addMinutes($movie->duration);
                        
                        // Si la película termina después de la hora de cierre, verificar si es razonable
                        if ($movieEndTime->format('H:i') > self::CLOSING_TIME) {
                            // Si la película comienza muy tarde, terminar aquí
                            if ($currentTime->format('H:i') > '22:00') {
                                break;
                            }
                        }

                        // Crear la función
                        $function = Functions::create([
                            'movie_id' => $movie->id,
                            'room_id' => $room->id,
                            'date' => $currentDate->format('Y-m-d'),
                            'time' => $currentTime->format('H:i:s'),
                            'is_3d' => $room->cinema->has_3d && $movieIndex % 2 == 0
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

                        // Avanzar al siguiente horario (duración + tiempo de limpieza)
                        $currentTime->addMinutes($movie->duration + self::CLEANING_TIME);
                        $movieIndex++;
                    }

                    // Guardar el último índice de película para esta sala
                    $lastMovieIndices[$roomKey] = $movieIndex % $movies->count();
                }
            }

            // Avanzar al siguiente día
            $currentDate->addDay();
        }
    }
} 