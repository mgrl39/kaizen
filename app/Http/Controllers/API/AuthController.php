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
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'birthdate' => 'required|date'
        ]);

        $result = $this->authService->register($validated);

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token']
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        try {
            $result = $this->authService->login(
                $validated['email'],
                $validated['password']
            );

            return response()->json([
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }
    }

    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function me()
    {
        $user = $this->authService->getAuthenticatedUser();
        return new UserResource($user);
    }
} 