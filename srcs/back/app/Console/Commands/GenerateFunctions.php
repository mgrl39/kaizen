<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Functions;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Cinema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateFunctions extends Command
{
    private const OPENING_TIME = '10:00';
    private const CLOSING_TIME = '23:00';
    private const CLEANING_TIME = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'functions:generate 
                          {start_date : Fecha de inicio (YYYY-MM-DD)} 
                          {end_date? : Fecha final (YYYY-MM-DD, por defecto una semana desde start_date)}
                          {--cinema_id= : ID del cine (por defecto, todos los cines)}
                          {--force : Forzar la generación incluso si ya existen funciones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera funciones para un rango de fechas, respetando días laborables';

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
     * Obtiene el índice de la última película mostrada en el día laborable anterior
     */
    private function getLastMovieIndex(Room $room, string $date, array $movieIds): int
    {
        if (empty($movieIds)) {
            return 0;
        }

        $previousDate = Carbon::parse($date)->subDay();
        while (!$this->isBusinessDay($previousDate)) {
            $previousDate->subDay();
        }
        
        $lastFunction = Functions::where('room_id', $room->id)
            ->whereDate('date', $previousDate->format('Y-m-d'))
            ->orderBy('time', 'desc')
            ->first();

        if (!$lastFunction) {
            return 0;
        }

        // Encontrar el índice de la última película en el array de movie_ids
        $index = array_search($lastFunction->movie_id, $movieIds);
        
        // Si encontramos el índice, devolver el siguiente. Si no, empezar desde 0
        return $index !== false ? ($index + 1) % count($movieIds) : 0;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = Carbon::parse($this->argument('start_date'));
        $endDate = $this->argument('end_date') 
            ? Carbon::parse($this->argument('end_date'))
            : $startDate->copy()->addDays(7);

        if ($startDate > $endDate) {
            $this->error('La fecha de inicio debe ser anterior a la fecha final');
            return 1;
        }

        // Obtener cines
        $query = Cinema::query();
        if ($this->option('cinema_id')) {
            $query->where('id', $this->option('cinema_id'));
        }
        $cinemas = $query->get();

        if ($cinemas->isEmpty()) {
            $this->error('No se encontraron cines');
            return 1;
        }

        // Obtener todas las películas
        $movies = Movie::where('is_active', true)->get();
        if ($movies->isEmpty()) {
            $this->error('No hay películas disponibles');
            return 1;
        }

        $this->info('Generando funciones...');

        DB::beginTransaction();
        try {
            foreach ($cinemas as $cinema) {
                $rooms = Room::where('cinema_id', $cinema->id)->get();
                if ($rooms->isEmpty()) {
                    $this->warn("El cine {$cinema->name} no tiene salas configuradas");
                    continue;
                }

                $this->info("\nProcesando cine: {$cinema->name}");
                
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    if ($this->isBusinessDay($currentDate)) {
                        // Eliminar funciones existentes si es necesario
                        if ($this->option('force')) {
                            Functions::whereIn('room_id', $rooms->pluck('id'))
                                ->whereDate('date', $currentDate->format('Y-m-d'))
                                ->delete();
                        }

                        // Procesar cada sala
                        $movieIndices = array_keys($movies->toArray());
                        shuffle($movieIndices); // Mezclar aleatoriamente las películas
                        
                        foreach ($rooms as $roomIndex => $room) {
                            $currentTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . self::OPENING_TIME);
                            $movieIndex = ($roomIndex * 3) % count($movieIndices); // Empezar con diferentes películas por sala
                            $usedTimes = []; // Array para trackear las horas usadas

                            // Seguir añadiendo películas mientras haya tiempo
                            while ($currentTime->format('H') < 23) {
                                $movie = $movies[$movieIndices[$movieIndex % count($movieIndices)]];
                                
                                // Calcular un offset aleatorio para la hora (entre 0 y 30 minutos)
                                $timeOffset = rand(0, 2) * 15; // 0, 15 o 30 minutos
                                $proposedTime = $currentTime->copy()->addMinutes($timeOffset);
                                
                                // Si la hora ya está usada o pasamos de las 23:00, saltamos
                                if ($proposedTime->format('H') >= 23 || 
                                    in_array($proposedTime->format('H:i'), $usedTimes)) {
                                    $movieIndex = ($movieIndex + 1) % count($movieIndices);
                                    continue;
                                }

                                $currentTime = $proposedTime;
                                $usedTimes[] = $currentTime->format('H:i');

                                // Crear la función
                                try {
                                    $function = Functions::create([
                                        'movie_id' => $movie->id,
                                        'room_id' => $room->id,
                                        'date' => $currentDate->format('Y-m-d'),
                                        'time' => $currentTime->format('H:i'),
                                        'is_3d' => $cinema->has_3d && rand(0, 1) == 1 // Aleatorizar 3D
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

                                    // Avanzar el tiempo y el índice de película
                                    $currentTime->addMinutes($movie->duration + self::CLEANING_TIME);
                                    $movieIndex = ($movieIndex + rand(1, 3)) % count($movieIndices); // Saltar 1-3 películas para más variedad
                                } catch (\Exception $e) {
                                    // Si hay un error al crear la función, intentamos con la siguiente película
                                    $this->warn("Error al crear función: " . $e->getMessage());
                                    $movieIndex = ($movieIndex + 1) % count($movieIndices);
                                    continue;
                                }
                            }
                        }
                    }
                    $currentDate->addDay();
                }
            }

            DB::commit();
            $this->info('Funciones generadas correctamente');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error al generar funciones: ' . $e->getMessage());
            return 1;
        }
    }
} 