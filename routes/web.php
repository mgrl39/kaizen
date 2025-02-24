<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cinema;
use App\Http\Controllers\CinemaController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::resource('cinema', CinemaController::class);
Route::resource('movies', MovieController::class);
Route::resource('functions', FunctionsController::class);
Route::resource('bookings', BookingController::class);
Route::resource('seats', SeatController::class);
Route::resource('admins', AdminController::class);
Route::resource('manages', ManageController::class);
Route::resource('users', UserController::class);
