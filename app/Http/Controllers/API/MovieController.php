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
     * @return Illuminate\Http\JsonResponse json
     */
    public function index()
    {
        $movies = Movie::all();
        return response()->json([
            'success' => true,
            'count' => $movies->count(),
            'data' => $movies,
            'message' => $movies->isEmpty() ? 
                'No hay películas registradas.' : 
                'Películas cargadas correctamente'
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
     */
    public function show($id)
    {
        $movie = Movie::findOrFail($id);
        return response()->json($movie);
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
     * Buscar películas
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $movies = Movie::where('title', 'like', "%{$query}%")
                      ->orWhere('synopsis', 'like', "%{$query}%")
                      ->get();
        
        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => $movies->count(),
            'data' => $movies,
            'message' => 'Búsqueda completada'
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
