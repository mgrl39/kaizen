<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Movie;
use App\Models\Functions;

/**
 * @class CinemaController
 * @brief Controlador para gestionar operaciones relacionadas con cines
 */
class CinemaController extends Controller
{
    /**
     * @brief Obtiene todos los cines
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @brief Crea un nuevo cine
     * @param Request $request Datos del cine a crear
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @brief Muestra un cine específico con sus salas
     * @param int $id ID del cine
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $cinema = Cinema::with('rooms')->findOrFail($id);
            
            // Transformar los datos para que coincidan con el formato del frontend
            $transformedCinema = [
                'id' => $cinema->id,
                'name' => $cinema->name,
                'location' => $cinema->location,
                'address' => $cinema->address,
                'phone' => $cinema->phone,
                'email' => $cinema->email,
                'description' => $cinema->description,
                'image_url' => $cinema->image_url,
                'has_3d' => $cinema->has_3d,
                'has_imax' => $cinema->has_imax,
                'has_vip' => $cinema->has_vip,
                'rooms_count' => $cinema->rooms->count(),
                'opening_hours' => $cinema->opening_hours,
                'features' => $cinema->features,
                'rooms' => $cinema->rooms->map(function($room) {
                    return [
                        'id' => $room->id,
                        'name' => $room->name,
                        'total_seats' => $room->rows * $room->seats_per_row,
                        'features' => [
                            'type' => $room->type,
                            'rows' => $room->rows,
                            'seats_per_row' => $room->seats_per_row
                        ]
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $transformedCinema,
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

    /**
     * @brief Actualiza un cine existente
     * @param Request $request Datos actualizados del cine
     * @param int $id ID del cine a actualizar
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $cinema = Cinema::findOrFail($id);
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'location' => 'sometimes|string|max:255',
                'address' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'email' => 'sometimes|email|max:255',
                'description' => 'sometimes|string',
                'image_url' => 'sometimes|string|max:255',
                'has_3d' => 'sometimes|boolean',
                'has_imax' => 'sometimes|boolean',
                'has_vip' => 'sometimes|boolean',
                'opening_hours' => 'sometimes|string|max:255',
                'features' => 'sometimes|array'
            ]);

            // Convertir el array de features a JSON si existe
            if (isset($validated['features'])) {
                $validated['features'] = json_encode($validated['features']);
            }

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

    /**
     * @brief Elimina un cine
     * @param int $id ID del cine a eliminar
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @brief Obtiene las salas de un cine
     * @param int $id ID del cine
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @brief Obtiene películas que se proyectan en un cine
     * @param int $id ID del cine
     * @return \Illuminate\Http\JsonResponse
     */
    public function movies($id)
    {
        $cinema = Cinema::findOrFail($id);
        $movieIds = Functions::whereHas('room', function ($query) use ($id) {
                        $query->where('cinema_id', $id);
                    })
                    ->pluck('movie_id')
                    ->unique();
        
        $movies = Movie::whereIn('id', $movieIds)->get();
        
        return response()->json([
            'success' => true,
            'cinema' => $cinema->name,
            'data' => $movies,
            'message' => 'Películas en proyección obtenidas correctamente'
        ]);
    }

    /**
     * @brief Obtiene cines por ubicación
     * @param string $location Ubicación a buscar
     * @return \Illuminate\Http\JsonResponse
     */
    public function byLocation($location)
    {
        $cinemas = Cinema::where('location', 'like', "%{$location}%")->get();
        
        return response()->json([
            'success' => true,
            'location' => $location,
            'data' => $cinemas,
            'message' => 'Cines por ubicación obtenidos correctamente'
        ]);
    }

    /**
     * @brief Busca cines por nombre o ubicación
     * @param Request $request Contiene el parámetro de búsqueda 'q'
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $cinemas = Cinema::where('name', 'like', "%{$query}%")
                       ->orWhere('location', 'like', "%{$query}%")
                       ->get();
        
        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => $cinemas->count(),
            'data' => $cinemas,
            'message' => 'Búsqueda completada'
        ]);
    }
}