<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

/**
 * @file
 * @brief Contiene el controlador para mostrar los endpoints disponibles
 */

/**
 * @class SimpleEndpointController
 * @brief Controlador para mostrar los endpoints disponibles en la aplicación
 * 
 * Este controlador obtiene la lista de endpoints disponibles a través de
 * una solicitud a la API y los muestra en una vista simple.
 */
class SimpleEndpointController extends Controller
{
    /**
     * @brief Muestra la lista de endpoints disponibles
     * @return \Illuminate\View\View Vista con la lista de rutas
     * 
     * Este método obtiene los endpoints haciendo una solicitud al
     * endpoint API '/api/v1/endpoints' y pasa los datos a la vista.
     */
    public function index()
    {
        try {
            $response = Http::get(config('app.url') . '/api/v1/endpoints');
            
            $routes = [];
            if ($response->successful()) {
                $routes = $response->json('data') ?? $response->json();
            }
            
            return view('simple-endpoints', compact('routes'));
        } catch (\Exception $e) {
            // En caso de error, mostrar lista vacía
            return view('simple-endpoints', ['routes' => []]);
        }
    }
} 