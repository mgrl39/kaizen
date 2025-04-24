<?php

/**
 * @file web.php
 * This file Provides the web
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\SimpleEndpointController;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get(
    '/',
    function () {
        return view('welcome');
    }
);
Route::get(
    '/register',
    function () {
        return view('register');
    }
);

Route::get(
    '/login',
    function () {
        return view('login');
    }
);

// Ruta para URLs
Route::get('/urls', [SimpleEndpointController::class, 'index']);
Route::get(
    'lang/{locale}',
    [LanguageController::class, 'switchLang']
)->name('language');

// TODO CAMBIAR EL CONTACT US
Route::resource('cinema', CinemaController::class);

Route::get(
    'movies',
    function () {
        return view('movies');
    }
);

// Añade esto en routes/web.php (NO en api.php) para evitar cualquier middleware o filtro
Route::get('api/v1/movies', function() {
    // Datos mockeados
    $movies = [
        ['id' => 1, 'title' => 'Dune', 'year' => 2021],
        ['id' => 2, 'title' => 'Oppenheimer', 'year' => 2023],
    ];
    
    return response()->json($movies);
});
