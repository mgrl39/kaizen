<?php

Route::get(
    '/contactus',
    function () {
        return view('contactus');
    }
);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// TODO: CHECK THIS FUNCTION.
Artisan::command(
    'inspire',
    function () {
        $this->comment(Inspiring::quote());
    }
)->purpose('Display an inspiring quote');

Artisan::command('app:process-data', function () {
    // Lógica específica de la aplicación que necesita
    // acceso a servicios de Laravel
})->purpose('Procesa datos de la aplicación');
