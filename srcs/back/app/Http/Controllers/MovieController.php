<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\MovieController as MovieApiController;
use Illuminate\Http\Request;

/**
 * @class MovieController
 * @brief Controlador para manejar las vistas relacionadas con películas
 * 
 * Este controlador actúa como intermediario entre la API de películas y las vistas,
 * obteniendo los datos de la API y pasándolos a las vistas correspondientes.
 */
class MovieController extends Controller
{
    /**
     * @var MovieApiController $movieApiController
     * @brief Instancia del controlador de API de películas
     */
    protected $movieApiController;
    
    /**
     * @brief Constructor del controlador
     * @param MovieApiController $movieApiController Instancia inyectada del controlador de API
     */
    public function __construct(MovieApiController $movieApiController)
    {
        $this->movieApiController = $movieApiController;
    }
    
    /**
     * @brief Muestra la lista de películas
     * @return \Illuminate\View\View Vista con la lista de películas o mensaje de error
     * 
     * Este método obtiene las películas de la API y las pasa a la vista 'movies'.
     * En caso de error, muestra un mensaje en la vista.
     */
    public function index()
    {
        try {
            $response = $this->movieApiController->index();            
            $responseData = json_decode($response->getContent(), true);
            $movies = $responseData['data'] ?? $responseData;
            return view('movies', compact('movies'));
        } catch (\Exception $e) {
            return view('movies', ['error' => 'Error al obtener películas: ' . $e->getMessage() ]);
        }
    }
} 