<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('api/*')) {
            return null; // No redirigir para solicitudes API
        }
        
        return '/'; // Redirigir a la página principal para solicitudes web
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->is('api/*')) {
            abort(response()->json([
                'success' => false,
                'message' => 'No autenticado',
                'errors' => ['auth' => ['Se requiere autenticación para acceder a este recurso']]
            ], 401));
        }

        parent::unauthenticated($request, $guards);
    }
}
