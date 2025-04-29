<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;

/**
 * Class MovieController
 *
 * Controlador que menaje las operaciones relacionadas con las peliculas
 */
class MovieController extends Controller
{
    /**
     * Obtener listado de películas
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse json
     */
    public function index(Request $request)
    {
        $query = Movie::query();

        // Filtrar por nombre si se proporciona
        if ($request->has('name')) {
            $searchTerm = $request->name;
            $searchWords = array_filter(explode(' ', trim($searchTerm)));
            
            $query->where(function($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->orWhere('title', 'ILIKE', '%' . $word . '%')
                      ->orWhere('synopsis', 'ILIKE', '%' . $word . '%');
                }
            });
        }

        $movies = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $movies,
            'message' => 'Películas obtenidas correctamente'
        ]);
    }

    /**
     * Crear una nueva película
     *
     * @param Request request la request
     */
    public function store(Request $request)
    {
        $request->validate(
            [
            'title' => 'required|string|max:255',
            'duration' => 'required|integer',
            'synopsis' => 'nullable|string'
        ]
        );

        $movie = Movie::create($request->all());
        return response()->json($movie, 201);
    }

    /**
     * Mostrar una película específica
     * 
     * @param string $slug Slug de la película
     * @return Illuminate\Http\JsonResponse json
     */
    public function show($slug)
    {
        try {
            $movie = Movie::where('slug', $slug)->firstOrFail();
            return response()->json([
                'success' => true,
                'data' => $movie,
                'message' => 'Película obtenida correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la película',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Versión 2 del endpoint de detalle de película
     * Incluye información adicional como géneros, proyecciones disponibles, etc.
     * 
     * @param string $slug Slug de la película
     * @return Illuminate\Http\JsonResponse json
     */
    public function showV2($slug)
    {
        try {
            $movie = Movie::with(['genres', 'functions.room.cinema'])
                         ->where('slug', $slug)
                         ->firstOrFail();
            
            // Formatear proyecciones para agruparlas por cine
            $screenings = [];
            foreach ($movie->functions as $function) {
                $cinemaId = $function->room->cinema->id;
                
                if (!isset($screenings[$cinemaId])) {
                    $screenings[$cinemaId] = [
                        'cinema' => [
                            'id' => $function->room->cinema->id,
                            'name' => $function->room->cinema->name,
                            'address' => $function->room->cinema->address,
                        ],
                        'dates' => []
                    ];
                }
                
                $date = $function->date->format('Y-m-d');
                if (!isset($screenings[$cinemaId]['dates'][$date])) {
                    $screenings[$cinemaId]['dates'][$date] = [];
                }
                
                $screenings[$cinemaId]['dates'][$date][] = [
                    'id' => $function->id,
                    'time' => $function->time,
                    'room' => $function->room->name,
                    'price' => $function->price,
                    'available_seats' => $function->available_seats
                ];
            }
            
            // Convertir a formato de array indexado para el JSON
            $formattedScreenings = [];
            foreach ($screenings as $cinema) {
                $formattedDates = [];
                foreach ($cinema['dates'] as $date => $times) {
                    $formattedDates[] = [
                        'date' => $date,
                        'times' => $times
                    ];
                }
                $cinema['dates'] = $formattedDates;
                $formattedScreenings[] = $cinema;
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'synopsis' => $movie->synopsis,
                    'duration' => $movie->duration,
                    'rating' => $movie->rating,
                    'release_date' => $movie->release_date ? $movie->release_date->format('Y-m-d') : null,
                    'photo_url' => $movie->photo_url,
                    'slug' => $movie->slug,
                    'genres' => $movie->genres->pluck('name'),
                    'screenings' => $formattedScreenings
                ],
                'message' => 'Información detallada de película obtenida correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la película',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Actualizar una película
     */
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'duration' => 'sometimes|integer',
            'synopsis' => 'nullable|string'
        ]);

        $movie->update($request->all());
        return response()->json($movie);
    }

    /**
     * Eliminar una película
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();
        return response()->json(null, 204);
    }

    /**
     * Obtener películas destacadas
     */
    public function featured()
    {
        $movies = Movie::inRandomOrder()->limit(5)->get();
        return response()->json([
            'success' => true,
            'data' => $movies,
            'message' => 'Películas destacadas obtenidas correctamente'
        ]);
    }

    /**
     * Obtener películas más recientes
     */
    public function latest()
    {
        $movies = Movie::orderBy('release_date', 'desc')->limit(10)->get();
        return response()->json([
            'success' => true,
            'data' => $movies,
            'message' => 'Últimas películas obtenidas correctamente'
        ]);
    }

    /**
     * Obtener películas por género
     */
    public function byGenre($genreId)
    {
        $genre = Genre::findOrFail($genreId);
        $movies = $genre->movies;
        
        return response()->json([
            'success' => true,
            'genre' => $genre->name,
            'data' => $movies,
            'message' => "Películas de género {$genre->name} obtenidas correctamente"
        ]);
    }

    /**
     * Buscar películas por varios criterios
     *
     * @param Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Movie::query();

        // Búsqueda por título
        if ($request->has('title')) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        // Búsqueda por duración
        if ($request->has('duration')) {
            $query->where('duration', '=', $request->duration);
        }

        // Búsqueda por rating
        if ($request->has('rating')) {
            $query->where('rating', '=', $request->rating);
        }

        // Búsqueda por fecha de estreno
        if ($request->has('release_date')) {
            $query->whereDate('release_date', '=', $request->release_date);
        }

        $movies = $query->get();

        return response()->json([
            'success' => true,
            'data' => $movies,
            'message' => 'Búsqueda realizada correctamente'
        ]);
    }

    /**
     * Obtener actores de una película
     */
    public function actors($id)
    {
        $movie = Movie::findOrFail($id);
        $actors = $movie->actors;
        
        return response()->json([
            'success' => true,
            'movie' => $movie->title,
            'data' => $actors,
            'message' => 'Actores obtenidos correctamente'
        ]);
    }

    /**
     * Obtener géneros de una película
     */
    public function genres($id)
    {
        $movie = Movie::findOrFail($id);
        $genres = $movie->genres;
        
        return response()->json([
            'success' => true,
            'movie' => $movie->title,
            'data' => $genres,
            'message' => 'Géneros obtenidos correctamente'
        ]);
    }

    /**
     * Obtener proyecciones de una película
     */
    public function screenings($id)
    {
        $movie = Movie::findOrFail($id);
        $screenings = $movie->functions()
                           ->with(['room.cinema'])
                           ->orderBy('date')
                           ->orderBy('time')
                           ->get();
        
        return response()->json([
            'success' => true,
            'movie' => $movie->title,
            'data' => $screenings,
            'message' => 'Proyecciones obtenidas correctamente'
        ]);
    }
}
