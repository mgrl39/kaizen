<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class GenreController extends Controller
{
    /**
     * Obtener todos los géneros
     */
    public function index()
    {
        try {
            // Verificar primero si la tabla existe
            if (!Schema::hasTable('genres')) {
                Log::error('Table "genres" does not exist in the database');
                return ResponseService::error(
                    'This feature is currently unavailable',
                    null,
                    503
                );
            }
            
            $genres = Genre::all();
            return ResponseService::success(
                $genres,
                'Genres retrieved successfully',
                200
            );
        } catch (QueryException $e) {
            Log::error('Database error in GenreController@index: ' . $e->getMessage());
            return ResponseService::error(
                'Unable to retrieve genres at this time',
                null,
                500
            );
        } catch (Exception $e) {
            Log::error('Error in GenreController@index: ' . $e->getMessage());
            return ResponseService::error(
                'An unexpected error occurred',
                null,
                500
            );
        }
    }
    
    /**
     * Obtener un género específico
     */
    public function show($id)
    {
        try {
            // Verificar primero si la tabla existe
            if (!Schema::hasTable('genres')) {
                Log::error('Table "genres" does not exist in the database');
                return ResponseService::error(
                    'This feature is currently unavailable',
                    null,
                    503
                );
            }
            
            $genre = Genre::findOrFail($id);
            return ResponseService::success(
                $genre,
                'Genre found',
                200
            );
        } catch (QueryException $e) {
            Log::error('Database error in GenreController@show: ' . $e->getMessage());
            return ResponseService::error(
                'Unable to retrieve genre at this time',
                null,
                500
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseService::error(
                'Genre not found',
                null,
                404
            );
        } catch (Exception $e) {
            Log::error('Error in GenreController@show: ' . $e->getMessage());
            return ResponseService::error(
                'An unexpected error occurred',
                null,
                500
            );
        }
    }
    
    /**
     * Obtener películas de un género
     */
    public function movies($id)
    {
        try {
            // Verificar primero si la tabla existe
            if (!Schema::hasTable('genres')) {
                Log::error('Table "genres" does not exist in the database');
                return ResponseService::error(
                    'This feature is currently unavailable',
                    null,
                    503
                );
            }
            
            $genre = Genre::findOrFail($id);
            $movies = $genre->movies;
            
            return ResponseService::success(
                [
                    'genre' => $genre->name,
                    'count' => $movies->count(),
                    'movies' => $movies
                ],
                'Movies for this genre retrieved successfully',
                200
            );
        } catch (QueryException $e) {
            Log::error('Database error in GenreController@movies: ' . $e->getMessage());
            return ResponseService::error(
                'Unable to retrieve movies for this genre at this time',
                null,
                500
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseService::error(
                'Genre not found',
                null,
                404
            );
        } catch (Exception $e) {
            Log::error('Error in GenreController@movies: ' . $e->getMessage());
            return ResponseService::error(
                'An unexpected error occurred',
                null,
                500
            );
        }
    }
} 