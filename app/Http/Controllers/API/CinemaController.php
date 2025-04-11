<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CinemaController extends Controller
{
    public function index()
    {
        try {
            $cinemas = Cinema::all();
            return response()->json([
                'success' => true,
                'count' => $cinemas->count(),
                'data' => $cinemas,
                'message' => $cinemas->isEmpty() ? 
                    'No hay cines registrados.' : 
                    'Cines cargados correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en CinemaController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar cines: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255'
            ]);

            $cinema = Cinema::create($validated);
            return response()->json([
                'success' => true,
                'data' => $cinema,
                'message' => 'Cine creado correctamente'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error en CinemaController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cine: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cinema = Cinema::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $cinema,
                'message' => 'Cine encontrado'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en CinemaController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar cine: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cinema = Cinema::findOrFail($id);
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'location' => 'sometimes|string|max:255'
            ]);

            $cinema->update($validated);
            return response()->json([
                'success' => true,
                'data' => $cinema,
                'message' => 'Cine actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en CinemaController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cine: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cinema = Cinema::findOrFail($id);
            $cinema->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cine eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en CinemaController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cine: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rooms($id)
    {
        try {
            $cinema = Cinema::findOrFail($id);
            $rooms = $cinema->rooms;
            
            return response()->json([
                'success' => true,
                'count' => $rooms->count(),
                'data' => $rooms,
                'message' => $rooms->isEmpty() ? 
                    'No hay salas registradas para este cine.' : 
                    'Salas cargadas correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en CinemaController@rooms: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar salas: ' . $e->getMessage()
            ], 500);
        }
    }
}

