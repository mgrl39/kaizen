<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Obtener todos los géneros
     */
    public function index()
    {
        $genres = Genre::all();
        return response()->json([
            'success' => true,
            'count' => $genres->count(),
            'data' => $genres,
            'message' => 'Géneros obtenidos correctamente'
        ]);
    }
    
    /**
     * Obtener un género específico
     */
    public function show($id)
    {
        $genre = Genre::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $genre,
            'message' => 'Género encontrado'
        ]);
    }
    
    /**
     * Obtener películas de un género
     */
    public function movies($id)
    {
        $genre = Genre::findOrFail($id);
        $movies = $genre->movies;
        
        return response()->json([
            'success' => true,
            'genre' => $genre->name,
            'count' => $movies->count(),
            'data' => $movies,
            'message' => 'Películas del género obtenidas correctamente'
        ]);
    }
} 