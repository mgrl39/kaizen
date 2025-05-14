<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Services\ResponseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Arr;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Si la solicitud espera JSON o es una ruta API
        $wantsJson = $request->expectsJson() || 
                    strpos($request->getRequestUri(), '/api/') === 0;
                    
        if ($wantsJson) {
            // ApiException personalizada - ya tiene lógica de renderizado
            if ($exception instanceof ApiException) {
                return $exception->render();
            }
            
            // Excepción de autenticación
            if ($exception instanceof AuthenticationException) {
                return ResponseService::error(
                    'No autenticado. Por favor inicie sesión.',
                    null,
                    401
                );
            }
            
            // Excepción de validación
            if ($exception instanceof ValidationException) {
                return ResponseService::error(
                    'Error de validación. Por favor revise los datos enviados.',
                    $exception->errors(),
                    422
                );
            }
            
            // Ruta no encontrada
            if ($exception instanceof NotFoundHttpException) {
                return ResponseService::error(
                    'Recurso no encontrado.',
                    null,
                    404
                );
            }
            
            // Método no permitido
            if ($exception instanceof MethodNotAllowedHttpException) {
                return ResponseService::error(
                    'Método HTTP no permitido para esta ruta.',
                    null,
                    405
                );
            }
            
            // Excepciones HTTP estándar
            if ($this->isHttpException($exception)) {
                $statusCode = $exception->getStatusCode();
                
                // Custom messages based on status code
                $message = match($statusCode) {
                    404 => 'Recurso no encontrado',
                    403 => 'Acceso prohibido',
                    401 => 'Acceso no autorizado',
                    429 => 'Demasiadas solicitudes',
                    500 => 'Error del servidor',
                    503 => 'Servicio no disponible',
                    default => 'Ha ocurrido un error'
                };
                
                return ResponseService::error($message, null, $statusCode);
            }
            
            // Para excepciones no HTTP, devolver como error 500
            if (!config('app.debug')) {
                return ResponseService::error('Error interno del servidor', null, 500);
            }
            
            // En modo debug, mostrar detalles para desarrolladores
            return ResponseService::error(
                $exception->getMessage(),
                [
                    'exception' => get_class($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => collect($exception->getTrace())->map(function ($trace) {
                        return Arr::except($trace, ['args']);
                    })->all(),
                ],
                500
            );
        }
        
        return parent::render($request, $exception);
    }
}
