<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    /**
     * Get all rooms with detailed information
     */
    public function index()
    {
        try {
            $rooms = Room::with('cinema')->get()->map(function($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'type' => $room->type,
                    'cinema' => [
                        'id' => $room->cinema->id,
                        'name' => $room->cinema->name
                    ],
                    'layout' => [
                        'rows' => $room->rows,
                        'seats_per_row' => $room->seats_per_row,
                        'total_seats' => $room->rows * $room->seats_per_row
                    ],
                    'price' => $room->price,
                    'features' => [
                        'is_imax' => $room->type === 'imax',
                        'is_vip' => $room->type === 'vip',
                        'is_3d' => $room->cinema->has_3d
                    ]
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $rooms,
                'message' => $rooms->isEmpty() ? 
                    'No hay salas disponibles.' : 
                    'Salas cargadas correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en RoomController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las salas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific room with its current seat status
     */
    public function show($id)
    {
        try {
            $room = Room::with(['cinema', 'movieFunctions.seats'])->findOrFail($id);
            
            $response = [
                'id' => $room->id,
                'name' => $room->name,
                'type' => $room->type,
                'cinema' => [
                    'id' => $room->cinema->id,
                    'name' => $room->cinema->name,
                    'location' => $room->cinema->location
                ],
                'layout' => [
                    'rows' => $room->rows,
                    'seats_per_row' => $room->seats_per_row,
                    'total_seats' => $room->rows * $room->seats_per_row
                ],
                'price' => $room->price,
                'features' => [
                    'is_imax' => $room->type === 'imax',
                    'is_vip' => $room->type === 'vip',
                    'is_3d' => $room->cinema->has_3d
                ],
                'current_functions' => $room->movieFunctions->map(function($function) {
                    return [
                        'id' => $function->id,
                        'movie' => $function->movie->title,
                        'date' => $function->date,
                        'time' => $function->time,
                        'available_seats' => $function->seats->where('status', 'available')->count(),
                        'total_seats' => $function->seats->count()
                    ];
                })
            ];
            
            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Sala cargada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en RoomController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la sala: ' . $e->getMessage()
            ], 500);
        }
    }
} 