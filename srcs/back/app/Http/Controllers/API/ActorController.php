<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ActorController extends Controller
{
    /**
     * Obtener todos los actores paginados
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', 50);
            $perPage = min($perPage, 100);

            $query = Actor::select('id', 'name');

            if ($search) {
                $query->where('name', 'ilike', "%{$search}%");
            }

            $actors = $query->orderBy('name', 'asc')
                          ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $actors->items(),
                'meta' => [
                    'current_page' => $actors->currentPage(),
                    'last_page' => $actors->lastPage(),
                    'per_page' => $actors->perPage(),
                    'total' => $actors->total()
                ],
                'message' => 'Actores cargados correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en ActorController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar actores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un actor específico con sus películas
     */
    public function show($slug)
    {
        try {
            $actor = Actor::where('slug', $slug)
                ->with(['movies' => function($query) {
                    $query->select('movies.id', 'title', 'photo_url', 'slug');
                }])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $actor,
                'message' => 'Actor encontrado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en ActorController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el actor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener películas de un actor
     */
    public function movies($slug)
    {
        try {
            // Intentar obtener del caché primero
            $cacheKey = 'actor.movies.' . $slug;
            $data = Cache::remember($cacheKey, 3600, function () use ($slug) {
                $actor = Actor::where('slug', $slug)
                            ->with(['movies' => function($query) {
                                $query->select('movies.id', 'title', 'photo_url', 'slug');
                            }])
                            ->firstOrFail();

                return [
                    'actor' => $actor->name,
                    'movies' => $actor->movies->map(function($movie) {
                        return [
                            'id' => $movie->id,
                            'title' => $movie->title,
                            'photo_url' => $movie->photo_url,
                            'slug' => $movie->slug
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Películas obtenidas correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en ActorController@movies: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las películas: ' . $e->getMessage()
            ], 500);
        }
    }
} 