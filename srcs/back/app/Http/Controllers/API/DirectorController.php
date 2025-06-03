<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Director;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 24);
        $directors = Director::withCount('movies')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $directors->items(),
            'meta' => [
                'current_page' => $directors->currentPage(),
                'last_page' => $directors->lastPage(),
                'per_page' => $directors->perPage(),
                'total' => $directors->total()
            ]
        ]);
    }

    public function show(Director $director)
    {
        $director->loadCount('movies');
        
        return response()->json([
            'success' => true,
            'data' => $director
        ]);
    }

    public function movies(Director $director)
    {
        $movies = $director->movies()
            ->with(['genres'])
            ->orderBy('release_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $movies
        ]);
    }
} 