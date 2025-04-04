<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Obtener listado de películas
     */
    public function index()
    {
        $movies = Movie::all();
        return response()->json($movies);
    }

    /**
     * Crear una nueva película
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer',
            'synopsis' => 'nullable|string'
        ]);

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
}
