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
// use App\Http\Controllers\API\ActorController;
use App\Http\Controllers\API\FunctionController;
use App\Http\Controllers\MovieController as MovieControllerAlias;
use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiInfoController;

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

// Rutas API v1
Route::prefix('v1')->group(function () {
    // Rutas para películas
    Route::resource('movies', MovieController::class);
    
    // Rutas para cines
    Route::get('/cinemas', [CinemaController::class, 'index']);
    Route::get('/cinemas/{cinema}', [CinemaController::class, 'show']);
    Route::get('/cinemas/by-location/{location}', [CinemaController::class, 'byLocation']);
    Route::get('/cinemas/search', [CinemaController::class, 'search']);

    // Rutas para géneros (solo lectura)
    Route::get('genres', [GenreController::class, 'index']);
    Route::get('genres/{genre}', [GenreController::class, 'show']);
    Route::get('genres/{genre}/movies', [GenreController::class, 'movies']);

    // Rutas para actores (solo lectura)
    // Route::get('actors', [ActorController::class, 'index']);
    // Route::get('actors/{actor}', [ActorController::class, 'show']);
    // Route::get('actors/{actor}/movies', [ActorController::class, 'movies']);

    // Rutas para funciones/proyecciones (solo lectura)
    Route::get('screenings', [FunctionController::class, 'index']);
    Route::get('screenings/today', [FunctionController::class, 'today']);
    Route::get('screenings/upcoming', [FunctionController::class, 'upcoming']);
    Route::get('screenings/{screening}/seats', [FunctionController::class, 'seats']);

    // Rutas de autenticación
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Rutas protegidas
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        
        // Otras rutas protegidas irían aquí
    });
});

// Esta ruta debe estar DESPUÉS de todas las demás rutas API
Route::fallback(function () {
    $path = request()->path();
    
    // Si la ruta empieza con api/v1 pero no corresponde a un endpoint válido
    if (strpos($path, 'api/v1') === 0) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API endpoint',
            'status' => 404
        ], 404);
    }
    
    // Si la ruta empieza con api/ pero no con api/v1/
    if (strpos($path, 'api') === 0 && strpos($path, 'api/v1') !== 0) {
        return response()->json([
            'success' => false,
            'message' => 'API version not specified or invalid. Please use /api/v1/',
            'status' => 404
        ], 404);
    }
    
    // Para otras rutas no API (opcional, puedes eliminar esta parte)
    return response()->view('errors.404', [], 404);
});

Route::get('v1/endpoints', [ApiInfoController::class, 'listEndpoints']);