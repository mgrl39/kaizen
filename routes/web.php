<?php

/**
 * @file web.php
 * This file Provides the web
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\SimpleEndpointController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
// Ruta para URLs
Route::get('/urls', [SimpleEndpointController::class, 'index']);
Route::get(
    'lang/{locale}',
    [LanguageController::class, 'switchLang']
)->name('language');

// TODO CAMBIAR EL CONTACT US
Route::resource('cinema', CinemaController::class);
