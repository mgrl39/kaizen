<?php

/**
 * @file web.php
 * API-only backend server - all routes redirect to API
 */

use Illuminate\Support\Facades\Route;
use App\Services\ResponseService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Since this is an API-only application, all web routes will redirect
| to API information endpoints or return JSON responses.
|
*/

// Root route - redirect to API
Route::get('/', function () {
    return redirect('/api');
});

// Catch-all route for any non-API route
Route::fallback(function () {
    return ResponseService::error(
        'Resource not found. This server provides API only.',
        [
            'available_endpoints' => [
                'api_info' => url('/api'),
                'api_v1' => url('/api/v1'),
                'health_check' => url('/api/ping')
            ]
        ],
        404
    );
});