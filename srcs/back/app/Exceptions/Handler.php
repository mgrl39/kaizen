<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'status' => $statusCode
            ], $statusCode);
        }
        
        // For non-HTTP exceptions, return as 500 error
        if (!config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'status' => 500
            ], 500);
        }
        
        return parent::render($request, $exception);
    }
}
