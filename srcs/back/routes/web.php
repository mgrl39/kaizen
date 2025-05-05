<?php

/**
 * @file web.php
 * This file Provides the web
 */

use Illuminate\Support\Facades\Route;
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
    return ['message' => 'API Backend - Use /api endpoints'];
});