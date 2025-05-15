<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * @file
 * @brief Contiene la clase principal del kernel HTTP de la aplicación
 */

/**
 * @class Kernel
 * @brief Gestiona el stack de middleware HTTP de la aplicación
 * 
 * Esta clase extiende el kernel HTTP base de Laravel y define
 * los middleware globales, grupos de middleware y alias de middleware
 * que se aplican a las solicitudes HTTP.
 * 
 * @author Laravel
 */
class Kernel extends HttpKernel
{
    /**
     * @var array<int, class-string|string> $middleware
     * @brief Stack global de middleware HTTP de la aplicación
     *
     * Estos middleware se ejecutan durante cada solicitud a la aplicación.
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\CorsMiddleware::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * @var array<string, array<int, class-string|string>> $middlewareGroups
     * @brief Grupos de middleware de la aplicación
     *
     * Los grupos permiten agrupar varios middleware bajo una sola clave para
     * facilitar su asignación a rutas. Por ejemplo, el grupo 'web' se aplica
     * automáticamente a las rutas dentro de routes/web.php
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetLocale::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\ApiRateLimiter::class.':public',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetLocale::class,
        ],
        
        'api.auth' => [
            'auth:api',
            \App\Http\Middleware\ApiRateLimiter::class.':authenticated',
        ],
        
        'api.write' => [
            'auth:api',
            \App\Http\Middleware\ApiRateLimiter::class.':write_operations',
        ],
    ];

    /**
     * @var array<string, class-string|string> $middlewareAliases
     * @brief Aliases de middleware de la aplicación
     *
     * Los aliases se pueden usar en lugar de nombres de clase para asignar
     * convenientemente middleware a rutas y grupos.
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'api.rate' => \App\Http\Middleware\ApiRateLimiter::class,
    ];
}
