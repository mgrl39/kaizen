<?php

/**
 * @file
 * API Routes
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider and all of them will
 * be assigned to the "api" middleware group. Make something great!
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MovieController;
use App\Http\Controllers\API\CinemaController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\ActorController;
use App\Http\Controllers\API\FunctionController;
use App\Http\Controllers\MovieController as MovieControllerAlias;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas para películas (simplificadas)
Route::apiResource('movies', MovieController::class);
Route::apiResource('cinemas', CinemaController::class);

// Rutas adicionales no autenticadas para películas
Route::get('movies/featured', [MovieController::class, 'featured']);
Route::get('movies/latest', [MovieController::class, 'latest']);
Route::get('movies/by-genre/{genre}', [MovieController::class, 'byGenre']);
Route::get('movies/search', [MovieController::class, 'search']);
Route::get('movies/{movie}/actors', [MovieController::class, 'actors']);
Route::get('movies/{movie}/genres', [MovieController::class, 'genres']);
Route::get('movies/{movie}/screenings', [MovieController::class, 'screenings']);

// Rutas adicionales no autenticadas para cines
Route::get('cinemas/{cinema}/rooms', [CinemaController::class, 'rooms']);
Route::get('cinemas/{cinema}/movies', [CinemaController::class, 'movies']);
Route::get(
    'cinemas/by-location/{location}',
    [CinemaController::class, 'byLocation']
);
Route::get('cinemas/search', [CinemaController::class, 'search']);

// Rutas para géneros (solo lectura)
Route::get('genres', [GenreController::class, 'index']);
Route::get('genres/{genre}', [GenreController::class, 'show']);
Route::get('genres/{genre}/movies', [GenreController::class, 'movies']);

// Rutas para actores (solo lectura)
Route::get('actors', [ActorController::class, 'index']);
Route::get('actors/{actor}', [ActorController::class, 'show']);
Route::get('actors/{actor}/movies', [ActorController::class, 'movies']);

// Rutas para funciones/proyecciones (solo lectura)
Route::get('screenings', [FunctionController::class, 'index']);
Route::get('screenings/today', [FunctionController::class, 'today']);
Route::get('screenings/upcoming', [FunctionController::class, 'upcoming']);
Route::get('screenings/{screening}/seats', [FunctionController::class, 'seats']);

// Rutas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
// no lo entiendooooooooooooooooooooooooo dicen que se tiene que hacer asi...
Route::middleware('auth:api')->group(
    function () {
    }
);
