<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Functions;
use App\Models\Movie;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FunctionController extends Controller
{
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
                    // Calcular el precio base de la sala
                    $basePrice = $function->room->price;
                    
                    // Si es 3D, añadir 2€ al precio base
                    $finalPrice = $function->is_3d ? $basePrice + 2 : $basePrice;

                    return [
                        'id' => $function->id,
                        'date' => $function->date->format('Y-m-d'),
                        'time' => $function->time,
                        'is_3d' => $function->is_3d,
                        'price' => $finalPrice,
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

            \Log::info('Found screenings: ' . $screenings->count());

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
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las proyecciones'
            ], 500);
        }
    }
} 