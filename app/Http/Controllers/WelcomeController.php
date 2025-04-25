<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WelcomeController extends Controller
{
    public function index()
    {
        try {
            // Obtener películas de la API
            $response = Http::get(config('app.url') . '/api/v1/movies');
            
            $featuredMovies = [];
            
            if ($response->successful()) {
                // Obtener todas las películas
                $allMovies = $response->json('data') ?? $response->json();
                
                // Seleccionar 3 películas aleatorias
                if (is_array($allMovies) && count($allMovies) > 0) {
                    // Si hay menos de 3 películas, usar todas
                    if (count($allMovies) <= 3) {
                        $featuredMovies = $allMovies;
                    } else {
                        // Seleccionar 3 índices aleatorios
                        $keys = array_rand($allMovies, 3);
                        
                        // Recopilar las 3 películas aleatorias
                        foreach ($keys as $key) {
                            $featuredMovies[] = $allMovies[$key];
                        }
                    }
                }
            }
            
            return view('welcome', [
                'featuredMovies' => $featuredMovies
            ]);
            
        } catch (\Exception $e) {
            // En caso de error, pasar un array vacío
            return view('welcome', [
                'featuredMovies' => []
            ]);
        }
    }
} 