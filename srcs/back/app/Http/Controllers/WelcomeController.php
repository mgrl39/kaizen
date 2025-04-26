<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @file
 * @brief Contiene el controlador para la página de bienvenida
 */

/**
 * @class WelcomeController
 * @brief Controlador que gestiona la página principal de bienvenida
 * 
 * Este controlador se encarga de obtener los datos necesarios para
 * mostrar la página de inicio, incluyendo películas destacadas
 * obtenidas de la API interna.
 */
class WelcomeController extends Controller
{
    public function index()
    {
        try {
            $response = Http::get(config('app.url') . '/api/v1/movies');
            
            $featuredMovies = [];
            if ($response->successful()) {
                $allMovies = $response->json('data') ?? $response->json();
                
                if (is_array($allMovies) && count($allMovies) > 0) {
                    if (count($allMovies) <= 3) $featuredMovies = $allMovies;
                    else {
                        $keys = array_rand($allMovies, 3);
                        foreach ($keys as $key) $featuredMovies[] = $allMovies[$key];
                    }
                }
            }
            
            return view('welcome', [
                'featuredMovies' => $featuredMovies
            ]);
            
        } catch (\Exception $e) {
            return view('welcome', [
                'featuredMovies' => []
            ]);
        }
    }
} 