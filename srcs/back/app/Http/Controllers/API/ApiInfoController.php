<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

/**
 * @class ApiInfoController
 * @brief Controlador para obtener información sobre los endpoints de la API
 * 
 * Este controlador proporciona métodos para listar y categorizar los endpoints disponibles
 * en la versión actual de la API.
 */
class ApiInfoController extends Controller
{
    /**
     * @brief Lista todos los endpoints disponibles de la API agrupados por categoría
     * @return \Illuminate\Http\JsonResponse
     * 
     * Este método obtiene todas las rutas de la API que comienzan con 'api/v1',
     * las agrupa por categoría (segundo segmento de la URI) y devuelve un JSON
     * con la información estructurada de los endpoints.
     * 
     * La respuesta incluye:
     * - Versión de la API
     * - Número total de endpoints
     * - Endpoints agrupados por categoría con sus métodos HTTP, URIs y nombres
     */
    public function listEndpoints()
    {
        $routes = collect(Route::getRoutes())
            ->filter(fn($route) => strpos($route->uri(), 'api/v1') === 0);

        $groupedRoutes = [];
        foreach ($routes as $route) {
            $parts = explode('/', $route->uri());
            $category = isset($parts[2]) && !str_contains($parts[2], '{') ? $parts[2] : $parts[1];
            
            $groupedRoutes[$category][] = [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName() ?: 'unnamed',
            ];
        }

        return response()->json([
            'api_version' => 'v1',
            'total_endpoints' => count($routes),
            'endpoints_by_category' => $groupedRoutes
        ]);
    }
}