<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create user with essential fields
            $userData = [
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'name' => $request->name ?? $request->username,
                'role' => 'user'
            ];
            
            Log::info('Attempting to create user with data: ' . json_encode(array_diff_key($userData, ['password' => ''])));
            
            $user = User::create($userData);
            
            Log::info('User created successfully with ID: ' . $user->id);
            
            try {
                // Generate token immediately after registration
                $token = JWTAuth::fromUser($user);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful',
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'name' => $user->name
                    ]
                ], 201);
            } catch (\Exception $tokenEx) {
                Log::error('Token generation error: ' . $tokenEx->getMessage());
                
                // Si falla la generación del token, el usuario ya está creado
                // Devolver respuesta exitosa sin token
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful but token generation failed',
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'name' => $user->name
                    ]
                ], 201);
            }
        } catch (QueryException $e) {
            Log::error('Database error during registration: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: Database error',
                'details' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password' => 'required|string',
            'is_admin' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Find user by username or email
            $user = User::where('username', $request->identifier)
                ->orWhere('email', $request->identifier)
                ->first();
            
            // Check if user exists and password is correct
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials: User not found'
                ], 401);
            }
            
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials: Password is incorrect'
                ], 401);
            }

            // Check admin access if requested
            if ($request->is_admin && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: Admin privileges required'
                ], 403);
            }
            
            // Generate JWT token
            $token = JWTAuth::fromUser($user);
            
            if (!$token) {
                throw new \Exception("Failed to generate token");
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role
                ]
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            // Get token and invalidate it
            $token = JWTAuth::getToken();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'No token provided'
                ], 401);
            }
            
            JWTAuth::invalidate($token);
            
            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyToken()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role
                ]
            ]);
            
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired'
            ], 401);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid'
            ], 401);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is missing'
            ], 401);
        }
    }
} 