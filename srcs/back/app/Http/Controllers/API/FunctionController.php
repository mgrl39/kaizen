<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Functions;
use App\Models\Room;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FunctionController extends Controller
{
    private const OPENING_TIME = '10:00';
    private const CLOSING_TIME = '00:00';
    private const CLEANING_TIME = 15; // 15 minutos entre sesiones para limpieza

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
     * Obtiene el día laborable anterior
     */
    private function getPreviousBusinessDay(Carbon $date): Carbon
    {
        $previousDay = $date->copy()->subDay();
        while (!$this->isBusinessDay($previousDay)) {
            $previousDay->subDay();
        }
        return $previousDay;
    }

    /**
     * Obtener todas las funciones
     */
    public function index(Request $request)
    {
        try {
            $query = Functions::with(['movie', 'room.cinema']);

            // Filtrar por fecha
            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }

            // Filtrar por sala
            if ($request->has('room_id')) {
                $query->where('room_id', $request->room_id);
            }

            // Filtrar por película
            if ($request->has('movie_id')) {
                $query->where('movie_id', $request->movie_id);
            }

            $functions = $query->orderBy('date')
                             ->orderBy('time')
                             ->get();

            return response()->json([
                'success' => true,
                'data' => $functions,
                'message' => 'Funciones obtenidas correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en FunctionController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener funciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el índice de la última película mostrada en el día laborable anterior
     */
    private function getLastMovieIndex(Room $room, string $date, array $movieIds): int
    {
        $previousBusinessDay = $this->getPreviousBusinessDay(Carbon::parse($date));
        
        $lastFunction = Functions::where('room_id', $room->id)
            ->whereDate('date', $previousBusinessDay->format('Y-m-d'))
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
     * Generar horarios automáticamente para una sala y fecha específica
     */
    public function generateSchedule(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'date' => 'required|date|after_or_equal:today',
                'movie_ids' => 'required|array',
                'movie_ids.*' => 'exists:movies,id'
            ]);

            $room = Room::findOrFail($request->room_id);
            $date = Carbon::parse($request->date);

            // Verificar si es día laborable
            if (!$this->isBusinessDay($date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden generar funciones para fines de semana',
                    'next_business_day' => $this->getNextBusinessDay($date)->format('Y-m-d')
                ], 400);
            }

            $movies = Movie::whereIn('id', $request->movie_ids)->get();
            $movieIds = $request->movie_ids;

            // Comenzar transacción
            DB::beginTransaction();

            // Eliminar funciones existentes para esta sala y fecha
            Functions::where('room_id', $room->id)
                    ->whereDate('date', $date->format('Y-m-d'))
                    ->delete();

            $currentTime = Carbon::parse($date->format('Y-m-d') . ' ' . self::OPENING_TIME);
            $closingTime = Carbon::parse($date->format('Y-m-d') . ' ' . self::CLOSING_TIME);
            
            // Obtener el índice de la última película del día laborable anterior
            $movieIndex = $this->getLastMovieIndex($room, $date->format('Y-m-d'), $movieIds);
            $scheduledFunctions = [];

            while ($currentTime < $closingTime) {
                if ($movieIndex >= $movies->count()) {
                    $movieIndex = 0; // Volver al inicio de la lista de películas
                }

                $movie = $movies[$movieIndex];
                
                // Calcular fin de la película
                $endTime = (clone $currentTime)->addMinutes($movie->duration);
                
                // Si la película termina después de la hora de cierre, verificar si es razonable
                if ($endTime->format('H:i') > self::CLOSING_TIME) {
                    // Si la película comienza muy tarde, terminar aquí
                    if ($currentTime->format('H:i') > '22:00') {
                        break;
                    }
                }

                // Crear la función
                $function = Functions::create([
                    'movie_id' => $movie->id,
                    'room_id' => $room->id,
                    'date' => $date->format('Y-m-d'),
                    'time' => $currentTime->format('H:i:s'),
                    'is_3d' => $room->cinema->has_3d && $movieIndex % 2 == 0 // Alternar 3D si está disponible
                ]);

                $scheduledFunctions[] = [
                    'id' => $function->id,
                    'movie' => $movie->title,
                    'start_time' => $currentTime->format('H:i'),
                    'end_time' => $endTime->format('H:i'),
                    'is_3d' => $function->is_3d,
                    'sequence_number' => $movieIndex + 1 // Agregar número de secuencia
                ];

                // Avanzar al siguiente horario (duración + tiempo de limpieza)
                $currentTime = $endTime->addMinutes(self::CLEANING_TIME);
                $movieIndex++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'room' => $room->name,
                    'date' => $date->format('Y-m-d'),
                    'is_business_day' => true,
                    'next_business_day' => $this->getNextBusinessDay($date)->format('Y-m-d'),
                    'next_movie_index' => $movieIndex % $movies->count(),
                    'functions' => $scheduledFunctions
                ],
                'message' => 'Horarios generados correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en FunctionController@generateSchedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar horarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar horarios para múltiples salas
     */
    public function generateMultiRoomSchedule(Request $request)
    {
        try {
            $request->validate([
                'cinema_id' => 'required|exists:cinemas,id',
                'date' => 'required|date|after_or_equal:today',
                'movie_ids' => 'required|array',
                'movie_ids.*' => 'exists:movies,id'
            ]);

            $date = Carbon::parse($request->date);

            // Verificar si es día laborable
            if (!$this->isBusinessDay($date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pueden generar funciones para fines de semana',
                    'next_business_day' => $this->getNextBusinessDay($date)->format('Y-m-d')
                ], 400);
            }

            $rooms = Room::where('cinema_id', $request->cinema_id)->get();
            $movies = Movie::whereIn('id', $request->movie_ids)->get();

            if ($rooms->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay salas disponibles en este cine'
                ], 400);
            }

            // Comenzar transacción
            DB::beginTransaction();

            // Eliminar funciones existentes para este cine y fecha
            Functions::whereIn('room_id', $rooms->pluck('id'))
                    ->whereDate('date', $date->format('Y-m-d'))
                    ->delete();

            $scheduleByRoom = [];
            $moviesPerRoom = ceil($movies->count() / $rooms->count());

            foreach ($rooms as $index => $room) {
                // Obtener el subconjunto de películas para esta sala
                $start = $index * $moviesPerRoom;
                $roomMovies = $movies->slice($start, $moviesPerRoom);

                if ($roomMovies->isEmpty()) {
                    continue;
                }

                // Generar horarios para esta sala
                $response = $this->generateSchedule(new Request([
                    'room_id' => $room->id,
                    'date' => $date->format('Y-m-d'),
                    'movie_ids' => $roomMovies->pluck('id')->toArray()
                ]));

                if ($response->getStatusCode() === 200) {
                    $scheduleByRoom[$room->name] = $response->original['data']['functions'];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date->format('Y-m-d'),
                    'is_business_day' => true,
                    'next_business_day' => $this->getNextBusinessDay($date)->format('Y-m-d'),
                    'schedules' => $scheduleByRoom
                ],
                'message' => 'Horarios generados correctamente para todas las salas'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en FunctionController@generateMultiRoomSchedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar horarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener los asientos de una función específica
     */
    public function getSeats($id)
    {
        try {
            $function = Functions::findOrFail($id);
            
            // Obtener la sala para saber su configuración
            $room = $function->room;
            
            // Obtener los asientos ocupados
            $occupiedSeats = $function->seats()
                ->where('status', 'occupied')
                ->pluck('number')
                ->toArray();
            
            // Generar matriz de asientos
            $seats = [];
            for ($row = 0; $row < $room->rows; $row++) {
                $rowSeats = [];
                for ($seatNum = 1; $seatNum <= $room->seats_per_row; $seatNum++) {
                    $seatNumber = ($row * $room->seats_per_row) + $seatNum;
                    $rowSeats[] = [
                        'id' => $seatNumber,
                        'number' => $seatNumber,
                        'row' => chr(65 + $row), // Convertir 0,1,2... a A,B,C...
                        'is_occupied' => in_array($seatNumber, $occupiedSeats)
                    ];
                }
                $seats[] = $rowSeats;
            }
            
            return response()->json([
                'success' => true,
                'data' => $seats,
                'message' => 'Asientos obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en FunctionController@getSeats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los asientos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una función específica con sus datos relacionados
     */
    public function show($id)
    {
        try {
            $function = Functions::with(['movie', 'room.cinema'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $function->id,
                    'date' => $function->date,
                    'time' => $function->time,
                    'is_3d' => $function->is_3d,
                    'price' => $function->price,
                    'movie' => [
                        'id' => $function->movie->id,
                        'title' => $function->movie->title,
                        'duration' => $function->movie->duration,
                        'photo_url' => $function->movie->photo_url
                    ],
                    'room' => [
                        'id' => $function->room->id,
                        'name' => $function->room->name,
                        'rows' => $function->room->rows,
                        'seats_per_row' => $function->room->seats_per_row,
                        'cinema' => [
                            'id' => $function->room->cinema->id,
                            'name' => $function->room->cinema->name
                        ]
                    ]
                ],
                'message' => 'Función encontrada correctamente'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en FunctionController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la función: ' . $e->getMessage()
            ], 500);
        }
    }
}
