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
use App\Http\Controllers\WelcomeController;

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
    [WelcomeController::class, 'index']
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

Route::get('movies', [MovieController::class, 'index']);