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
use Symfony\Component\HttpFoundation\Response;

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
        // Global handling for not found routes
        $this->renderable(function (NotFoundHttpException $e, $request) {
            $path = $request->path();
            
            // Force JSON response for any 404 error
            return ResponseService::error(
                'Resource not found',
                [
                    'path' => $path,
                    'available_endpoints' => [
                        'api_info' => url('/api'),
                        'api_v1' => url('/api/v1'),
                        'health_check' => url('/api/ping')
                    ]
                ],
                404
            );
        });
        
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // If request expects JSON or is an API route
        $wantsJson = $request->expectsJson() || 
                    strpos($request->getRequestUri(), '/api/') === 0;
                    
        if ($wantsJson) {
            // Custom ApiException - already has rendering logic
            if ($exception instanceof ApiException) {
                return $exception->render();
            }
            
            // Authentication exception
            if ($exception instanceof AuthenticationException) {
                return ResponseService::error(
                    'Unauthenticated. Please log in.',
                    null,
                    401
                );
            }
            
            // Validation exception
            if ($exception instanceof ValidationException) {
                return ResponseService::error(
                    'Validation error. Please check your data.',
                    $exception->errors(),
                    422
                );
            }
            
            // Method not allowed
            if ($exception instanceof MethodNotAllowedHttpException) {
                return ResponseService::error(
                    'HTTP method not allowed for this route.',
                    null,
                    405
                );
            }
            
            // Standard HTTP exceptions
            if ($this->isHttpException($exception)) {
                $statusCode = $exception->getStatusCode();
                
                // Custom messages based on status code
                $message = match($statusCode) {
                    404 => 'Resource not found',
                    403 => 'Forbidden access',
                    401 => 'Unauthorized access',
                    429 => 'Too many requests',
                    500 => 'Server error',
                    503 => 'Service unavailable',
                    default => 'An error occurred'
                };
                
                return ResponseService::error($message, null, $statusCode);
            }
            
            // For non-HTTP exceptions, return as 500 error
            if (!config('app.debug')) {
                return ResponseService::error('Internal server error', null, 500);
            }
            
            // In debug mode, show details for developers
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
        
        // For normal web requests, convert to JSON in this API case
        if ($exception instanceof NotFoundHttpException) {
            return $this->renderJsonResponse($request, $exception);
        }
        
        return parent::render($request, $exception);
    }
    
    /**
     * Render a JSON response for any request
     */
    protected function renderJsonResponse($request, Throwable $exception): Response
    {
        $statusCode = $this->isHttpException($exception) ? $exception->getStatusCode() : 500;
        
        return ResponseService::error(
            'Resource not found. This server provides API only.',
            [
                'path' => $request->path(),
                'available_endpoints' => [
                    'api_info' => url('/api'),
                    'api_v1' => url('/api/v1'),
                    'health_check' => url('/api/ping')
                ]
            ],
            $statusCode
        );
    }
}
