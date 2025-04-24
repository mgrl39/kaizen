<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiInfoController extends Controller
{
    public function listEndpoints()
    {
        $routes = collect(Route::getRoutes());
        
        // Agrupar por categoría basado en el URI
        $groupedRoutes = [];
        
        foreach ($routes as $route) {
            if (strpos($route->uri(), 'api/v1') === 0) {
                $uri = $route->uri();
                
                // Extraer categoría de la URI (ejemplo: api/v1/movies -> movies)
                $parts = explode('/', $uri);
                $category = isset($parts[2]) ? $parts[2] : 'other';
                
                // Manejar caso especial para endpoints con parámetros
                if (strpos($category, '{') !== false) {
                    $category = $parts[1]; // Usamos el segmento anterior
                }
                
                if (!isset($groupedRoutes[$category])) {
                    $groupedRoutes[$category] = [];
                }
                
                $groupedRoutes[$category][] = [
                    'method' => implode('|', $route->methods()),
                    'uri' => $route->uri(),
                    'name' => $route->getName() ?: 'unnamed',
                ];
            }
        }
        
        return response()->json([
            'api_version' => 'v1',
            'total_endpoints' => count($routes->filter(function($route) {
                return strpos($route->uri(), 'api/v1') === 0;
            })),
            'endpoints_by_category' => $groupedRoutes
        ]);
    }
} 