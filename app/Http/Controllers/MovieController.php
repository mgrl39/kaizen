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
            $response = $this->movieApiController->index();            
            $responseData = json_decode($response->getContent(), true);
            $movies = $responseData['data'] ?? $responseData;
            return view('movies', compact('movies'));
        } catch (\Exception $e) {
            return view('movies', ['error' => 'Error al obtener películas: ' . $e->getMessage() ]);
        }
    }
} 