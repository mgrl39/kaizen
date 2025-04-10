<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MovieController;
use App\Http\Controllers\API\AuthController;
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

// Route::get('/server-status', [ServerStatusController::class, 'status']);

// Rutas para películas
Route::apiResource('/movies', MovieController::class);
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::post('/movies', [MovieController::class, 'store']);
Route::put('/movies/{id}', [MovieController::class, 'update']);
Route::delete('/movies/{id}', [MovieController::class, 'destroy']);

// Rutas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::get('/user', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Rutas protegidas
Route::middleware('auth:api')->group(
    function () {
        // Route::post('logout', [AuthController::class, 'logout']);
        // Route::get('me', [AuthController::class, 'me']);
    }
);
