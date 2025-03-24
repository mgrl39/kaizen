<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

class SimpleEndpointController extends Controller
{
    public function index()
    {
        $routes = [];
        
        foreach (Route::getRoutes() as $route) {
            $routes[] = [
                'method' => implode('|', $route->methods()),
                'url' => $route->uri()
            ];
        }
        
        return view('simple-endpoints', compact('routes'));
    }
} 