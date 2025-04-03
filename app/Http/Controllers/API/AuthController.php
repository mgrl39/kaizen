<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        // Método desactivado
        return response()->json(['message' => 'Registro desactivado'], 403);
    }

    public function login(Request $request)
    {
        // Método desactivado
        return response()->json(['message' => 'Inicio de sesión desactivado'], 403);
    }

    public function logout()
    {
        // Método desactivado
        return response()->json(['message' => 'Cierre de sesión desactivado'], 403);
    }

    public function me()
    {
        // Método desactivado
        return response()->json(['message' => 'Información de usuario desactivada'], 403);
    }
} 