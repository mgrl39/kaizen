<?php

/**
 * @file web.php
 * This file Provides the web
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\SimpleEndpointController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MovieController;

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

// Root route with API info
Route::get('/', function () {
    return response()->json([
        'message' => 'API Server',
        'api_version' => 'v1',
        'documentation' => '/api/v1/endpoints'
    ]);
});

// API routes for frontend consumption
Route::get('/urls', [SimpleEndpointController::class, 'index']);
Route::get(
    'lang/{locale}',
    [LanguageController::class, 'switchLang']
)->name('language');

Route::resource('cinema', CinemaController::class);
Route::get('movies', [MovieController::class, 'index']);