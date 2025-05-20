<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    /**
     * Servir una imagen desde el almacenamiento
     *
     * @param string $path
     * @return \Illuminate\Http\Response
     */
    public function show($path)
    {
        // Decodificar la ruta en caso de que contenga caracteres especiales
        $path = urldecode($path);
        
        // Log para depuración
        Log::info("Buscando imagen: " . $path);
        
        // Verificar diferentes ubicaciones para la imagen
        $locations = [
            public_path($path),           // En la carpeta public
            public_path('images/' . $path), // En la carpeta public/images
            storage_path('app/public/' . $path), // En storage/app/public
            storage_path('app/' . $path)  // En storage/app
        ];
        
        foreach ($locations as $location) {
            Log::info("Verificando ubicación: " . $location);
            if (File::exists($location)) {
                Log::info("Imagen encontrada en: " . $location);
                return response()->file($location);
            }
        }
        
        // Si no se encuentra la imagen, devolver una imagen por defecto o un 404
        $defaultImage = public_path('img/default-movie.jpg');
        if (File::exists($defaultImage)) {
            Log::info("Usando imagen por defecto: " . $defaultImage);
            return response()->file($defaultImage);
        }
        
        Log::warning("Imagen no encontrada: " . $path);
        return response()->json(['error' => 'Imagen no encontrada'], 404);
    }
}