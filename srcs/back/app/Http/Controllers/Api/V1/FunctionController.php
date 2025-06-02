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
            $screenings = Functions::where('movie_id', $movieId)
                ->where('date', '>=', Carbon::today()->format('Y-m-d'))
                ->with(['room.cinema']) // Eager loading de relaciones
                ->orderBy('date')
                ->orderBy('time')
                ->get()
                ->map(function ($function) {
                    return [
                        'id' => $function->id,
                        'date' => $function->date,
                        'time' => $function->time,
                        'is_3d' => $function->is_3d,
                        'price' => $function->price ?? 10.00, // Precio por defecto si no está definido
                        'room' => [
                            'id' => $function->room->id,
                            'name' => $function->room->name,
                            'cinema' => [
                                'id' => $function->room->cinema->id,
                                'name' => $function->room->cinema->name
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
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las proyecciones'
            ], 500);
        }
    }
} 