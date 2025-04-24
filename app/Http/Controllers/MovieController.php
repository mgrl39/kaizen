<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\MovieController as MovieApiController;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $movieApiController;
    
    public function __construct(MovieApiController $movieApiController)
    {
        $this->movieApiController = $movieApiController;
    }
    
    public function index()
    {
        try {
            // Llamar directamente al método index del controlador API
            $response = $this->movieApiController->index();
            
            // Convertir la respuesta JSON a un array
            $responseData = json_decode($response->getContent(), true);
            
            // Ver si los datos están en la clave 'data' o directamente en la raíz
            $movies = $responseData['data'] ?? $responseData;
            
            return view('movies', compact('movies'));
        } catch (\Exception $e) {
            return view('movies', [
                'error' => 'Error al obtener películas: ' . $e->getMessage()
            ]);
        }
    }
} 