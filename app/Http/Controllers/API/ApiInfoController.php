<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class ApiInfoController extends Controller
{
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