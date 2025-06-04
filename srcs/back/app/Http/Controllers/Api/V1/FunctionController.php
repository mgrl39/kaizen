<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Functions;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Seat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FunctionController extends Controller
{
    /**
     * Calculate final price for a function
     *
     * @param Functions $function
     * @return float|null
     */
    private function calculatePrice(Functions $function)
    {
        $basePrice = $function->room->price;
        return $function->is_3d ? $basePrice + 2 : $basePrice;
    }

    /**
     * Get screenings for a specific movie
     *
     * @param int $movieId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMovieScreenings($movieId)
    {
        try {
            // Verificar si la película existe
            $movie = Movie::findOrFail($movieId);

            // Obtener las funciones futuras para esta película
            $now = now();
            $screenings = Functions::where('movie_id', $movieId)
                ->where(function($query) use ($now) {
                    $query->whereDate('date', '>', $now->format('Y-m-d'))
                        ->orWhere(function($q) use ($now) {
                            $q->whereDate('date', '=', $now->format('Y-m-d'))
                               ->whereTime('time', '>=', $now->format('H:i'));
                        });
                })
                ->with(['room.cinema']) // Eager loading de relaciones
                ->orderBy('date')
                ->orderBy('time')
                ->get()
                ->map(function ($function) {
                    return [
                        'id' => $function->id,
                        'date' => $function->date->format('Y-m-d'),
                        'time' => $function->time,
                        'is_3d' => $function->is_3d,
                        'price' => $this->calculatePrice($function),
                        'room' => [
                            'id' => $function->room->id,
                            'name' => $function->room->name,
                            'type' => $function->room->type,
                            'cinema' => [
                                'id' => $function->room->cinema->id,
                                'name' => $function->room->cinema->name,
                                'location' => $function->room->cinema->location
                            ]
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $screenings
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Película no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error getting screenings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las proyecciones'
            ], 500);
        }
    }

    /**
     * Get seat layout with availability for a specific function
     *
     * @param int $functionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFunctionSeats($functionId)
    {
        try {
            Log::info('Getting seats for function', ['function_id' => $functionId]);

            // Verificar si la función existe y cargar la sala
            $function = Functions::with(['room', 'bookings.seats', 'movie'])->findOrFail($functionId);
            
            if (!$function->room) {
                throw new \Exception('Room not found for function ' . $functionId);
            }

            // Obtener el layout de asientos usando el método del modelo Room
            $seatLayout = $function->room->getSeatLayoutForFunction($function);

            if (empty($seatLayout)) {
                throw new \Exception('Failed to generate seat layout for function ' . $functionId);
            }

            Log::info('Generated seat layout', [
                'function_id' => $functionId,
                'layout_rows' => count($seatLayout),
                'seats_per_row' => count($seatLayout[0] ?? [])
            ]);

            $response = [
                'success' => true,
                'data' => [
                    'layout' => $seatLayout,
                    'room_info' => [
                        'id' => $function->room->id,
                        'name' => $function->room->name,
                        'rows' => $function->room->rows,
                        'seats_per_row' => $function->room->seats_per_row,
                        'price' => $this->calculatePrice($function)
                    ],
                    'function_info' => [
                        'id' => $function->id,
                        'date' => $function->date,
                        'time' => $function->time,
                        'is_3d' => $function->is_3d,
                        'movie' => [
                            'id' => $function->movie->id,
                            'title' => $function->movie->title,
                            'duration' => $function->movie->duration,
                            'photo_url' => $function->movie->photo_url
                        ]
                    ]
                ],
                'message' => 'Layout de asientos obtenido correctamente'
            ];

            Log::info('Returning seat layout response', [
                'function_id' => $functionId,
                'response_structure' => array_keys($response['data']),
                'layout_size' => [
                    'rows' => count($seatLayout),
                    'seats_per_row' => count($seatLayout[0] ?? [])
                ]
            ]);

            return response()->json($response);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Function not found', ['function_id' => $functionId]);
            return response()->json([
                'success' => false,
                'message' => 'Función no encontrada'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting seats layout', [
                'function_id' => $functionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el layout de asientos: ' . $e->getMessage()
            ], 500);
        }
    }
} 