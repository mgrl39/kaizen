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
    private const CLEANING_TIME = 15;

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

        $movieIds = $movies->pluck('id')->toArray();

        $this->info('Generando funciones...');
        $progressBar = $this->output->createProgressBar(
            $cinemas->count() * $endDate->diffInDays($startDate) + 1
        );

        DB::beginTransaction();
        try {
            foreach ($cinemas as $cinema) {
                $rooms = Room::where('cinema_id', $cinema->id)->get();
                if ($rooms->isEmpty()) {
                    $this->warn("El cine {$cinema->name} no tiene salas configuradas");
                    continue;
                }

                $this->info("\nProcesando cine: {$cinema->name}");
                
                // Calcular películas por sala
                $moviesPerRoom = ceil($movies->count() / $rooms->count());
                
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    if ($this->isBusinessDay($currentDate)) {
                        // Verificar si ya existen funciones para esta fecha
                        $existingFunctions = Functions::whereIn('room_id', $rooms->pluck('id'))
                            ->whereDate('date', $currentDate->format('Y-m-d'))
                            ->exists();

                        if ($existingFunctions && !$this->option('force')) {
                            $this->warn("Ya existen funciones para {$currentDate->format('Y-m-d')} - Use --force para sobrescribir");
                            $currentDate->addDay();
                            $progressBar->advance();
                            continue;
                        }

                        // Eliminar funciones existentes si es necesario
                        if ($existingFunctions) {
                            Functions::whereIn('room_id', $rooms->pluck('id'))
                                ->whereDate('date', $currentDate->format('Y-m-d'))
                                ->delete();
                        }

                        foreach ($rooms as $index => $room) {
                            // Obtener el subconjunto de películas para esta sala
                            $start = $index * $moviesPerRoom;
                            $roomMovies = $movies->slice($start, $moviesPerRoom);
                            $roomMovieIds = $roomMovies->pluck('id')->toArray();

                            if ($roomMovies->isEmpty()) continue;

                            // Obtener el índice de la última película
                            $movieIndex = $this->getLastMovieIndex($room, $currentDate->format('Y-m-d'), $roomMovieIds);
                            
                            // Generar horarios para el día
                            $currentTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . self::OPENING_TIME);
                            $endTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . self::CLOSING_TIME);

                            while ($currentTime < $endTime) {
                                if ($movieIndex >= $roomMovies->count()) {
                                    $movieIndex = 0;
                                }

                                $movie = $roomMovies[$movieIndex];
                                
                                // Calcular fin de la película
                                $movieEndTime = (clone $currentTime)->addMinutes($movie->duration);
                                
                                // Si la película termina después de la hora de cierre
                                if ($movieEndTime->format('H:i') > self::CLOSING_TIME) {
                                    if ($currentTime->format('H:i') > '22:00') {
                                        break;
                                    }
                                }

                                // Crear la función
                                Functions::create([
                                    'movie_id' => $movie->id,
                                    'room_id' => $room->id,
                                    'date' => $currentDate->format('Y-m-d'),
                                    'time' => $currentTime->format('H:i:s'),
                                    'is_3d' => $cinema->has_3d && $movieIndex % 2 == 0
                                ]);

                                $currentTime->addMinutes($movie->duration + self::CLEANING_TIME);
                                $movieIndex++;
                            }
                        }
                    }

                    $currentDate->addDay();
                    $progressBar->advance();
                }
            }

            DB::commit();
            $progressBar->finish();
            
            $this->newLine();
            $this->info('Funciones generadas correctamente');
            
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error al generar funciones: ' . $e->getMessage());
            return 1;
        }
    }
} 