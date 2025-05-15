<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ResponseService;

class ApiRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $type
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $type = 'public'): Response
    {
        $key = $this->resolveRequestSignature($request, $type);
        
        $config = config("api.rate_limiting.$type");
        $attempts = $config['attempts'] ?? 60;
        $period = $config['period'] ?? 60;
        
        if (RateLimiter::tooManyAttempts($key, $attempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return ResponseService::error(
                'API rate limit exceeded. Please try again in ' . $seconds . ' seconds.',
                null, 
                429
            );
        }
        
        RateLimiter::hit($key, $period * 60);
        
        $response = $next($request);
        
        // Agregar headers con información del rate limit
        $response->headers->add([
            'X-RateLimit-Limit' => $attempts,
            'X-RateLimit-Remaining' => $attempts - RateLimiter::attempts($key),
            'X-RateLimit-Reset' => RateLimiter::availableIn($key)
        ]);
        
        return $response;
    }
    
    /**
     * Resolver la firma para identificar la solicitud.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type
     * @return string
     */
    protected function resolveRequestSignature(Request $request, string $type): string
    {
        // Si es una petición autenticada, usar el ID de usuario
        $userPart = $request->user() 
            ? $request->user()->id 
            : $request->ip();
        
        // Incluir método HTTP para distinguir entre operaciones
        $methodPart = $request->method();
        
        // Añadir un identificador del endpoint para distinguir diferentes endpoints
        $routePart = Str::slug($request->path());
        
        return "api:$type:$userPart:$methodPart:$routePart";
    }
} 