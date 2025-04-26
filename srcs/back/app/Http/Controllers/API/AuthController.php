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
        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Versión simplificada sin JWT para pruebas
            // Log de los datos que se van a usar (sin la contraseña)
            Log::info('Intentando registrar usuario (simplificado)', [
                'username' => $request->name,
                'email' => $request->email
            ]);
            
            // Crear usuario directamente
            $user = User::create([
                'username' => $request->name,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birthdate' => now()->format('Y-m-d')
            ]);
            
            // Log del usuario creado
            Log::info('Usuario creado correctamente', ['user_id' => $user->id]);
                
            // Retornar solo los datos necesarios del usuario (sin password)
            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email
            ];

            // Crear la respuesta sin token
            $response = [
                'success' => true,
                'message' => 'Usuario registrado correctamente (versión sin token)',
                'user' => $userData
            ];

            // Registrar la respuesta en el log para debugging
            Log::info('Respuesta de registro exitosa (simplificado)');

            // Usar código 200
            return response()->json($response, 200);
            
        } catch (QueryException $qe) {
            // Error específico de base de datos
            Log::error('Error de base de datos al registrar usuario: ' . $qe->getMessage(), [
                'exception' => $qe,
                'sql' => $qe->getSql() ?? 'No SQL disponible',
                'bindings' => $qe->getBindings() ?? 'No bindings disponibles',
                'code' => $qe->getCode(),
                'trace' => $qe->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error de base de datos al registrar usuario: ' . $qe->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Error general
            Log::error('Error en registro (simplificado): ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario: ' . $e->getMessage(),
                'details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'class' => get_class($e)
                ]
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string', // Puede ser username o email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Log de los datos de inicio de sesión (sin la contraseña)
            Log::info('Intentando iniciar sesión', [
                'identifier' => $request->identifier
            ]);
            
            // Buscar usuario por nombre de usuario o email
            $user = User::where('username', $request->identifier)
                ->orWhere('email', $request->identifier)
                ->first();
            
            // Verificar si el usuario existe
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }
            
            // Verificar contraseña
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }
            
            // Usuario y contraseña válidos
            // Retornar datos del usuario (sin password)
            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email
            ];
            
            // Intentar generar token JWT
            try {
                $token = JWTAuth::fromUser($user);
                
                // Respuesta con token
                return response()->json([
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso',
                    'user' => $userData,
                    'token' => $token
                ], 200);
            } catch (\Exception $jwtError) {
                // Error al generar token, pero login exitoso
                Log::warning('Error al generar token JWT: ' . $jwtError->getMessage());
                
                // Respuesta sin token
                return response()->json([
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso (sin token)',
                    'user' => $userData
                ], 200);
            }
        } catch (\Exception $e) {
            // Error general
            Log::error('Error en login: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión: ' . $e->getMessage(),
                'details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'class' => get_class($e)
                ]
            ], 500);
        }
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