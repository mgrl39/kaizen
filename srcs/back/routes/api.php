<?php

/**
 * @file
 * API Routes
 *
 * Here is where you can register API routes for your application.
 * Routes are loaded by the RouteServiceProvider and assigned to the "api" middleware group.
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\API\{
    ApiInfoController,
    AuthController,
    CinemaController,
    FunctionController,
    GenreController,
    MovieController,
    ProfileController,
    UserController
};
use App\Services\ResponseService;

/*
|--------------------------------------------------------------------------
| API Base Information
|--------------------------------------------------------------------------
*/

// Base API route - provides API information instead of 404
Route::get('/', function () {
    $versions = config('api.versions.supported', ['v1']);
    $current = config('api.versions.current', 'v1');
    
    $versionInfo = [];
    foreach ($versions as $version) {
        $status = 'supported';
        if ($version === $current) {
            $status = 'current';
        } elseif (in_array($version, config('api.versions.deprecated', []))) {
            $status = 'deprecated';
        }
        
        $versionInfo[$version] = [
            'status' => $status,
            'url' => url("/api/{$version}")
        ];
    }
    
    return response()->json([
        'name' => config('app.name') . ' API',
        'status' => 'running',
        'api_root' => url('/api'),
        'versions' => $versionInfo,
        'current_version' => [
            'version' => $current,
            'url' => url("/api/{$current}")
        ],
        'timestamp' => now()->toIso8601String(),
        'message' => 'Welcome to the API. Please use the API version prefix in your requests.'
    ]);
})->name('api.info');

/*
|--------------------------------------------------------------------------
| API Version 1 Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->name('api.v1.')->group(function () {
    
    // API v1 Base Information
    Route::get('/', function () {
        $endpoints = [
            'authentication' => [
                'register' => ['POST', url('/api/v1/register')],
                'login' => ['POST', url('/api/v1/login')],
                'logout' => ['POST', url('/api/v1/logout')],
            ],
            'movies' => [
                'list' => ['GET', url('/api/v1/movies')],
                'detail' => ['GET', url('/api/v1/movies/{movie}')]
            ],
            'cinemas' => [
                'list' => ['GET', url('/api/v1/cinemas')],
                'search' => ['GET', url('/api/v1/cinemas/search')],
                'by_location' => ['GET', url('/api/v1/cinemas/location/{location}')]
            ],
            'genres' => [
                'list' => ['GET', url('/api/v1/genres')],
                'detail' => ['GET', url('/api/v1/genres/{genre}')],
                'movies' => ['GET', url('/api/v1/genres/{genre}/movies')]
            ],
            'profile' => [
                'view' => ['GET', url('/api/v1/profile')],
                'update' => ['PUT', url('/api/v1/profile')],
                'change_password' => ['PUT', url('/api/v1/profile/password')]
            ]
        ];

        return response()->json([
            'name' => config('app.name') . ' API',
            'version' => 'v1',
            'status' => config('api.versions.current') === 'v1' ? 'current' : 'supported',
            'endpoints' => $endpoints,
            'timestamp' => now()->toIso8601String()
        ]);
    })->name('info');
    
    /*
    |--------------------------------------------------------------------------
    | Public Routes - No Authentication Required
    |--------------------------------------------------------------------------
    */
    
    // System Info
    Route::get('endpoints', [ApiInfoController::class, 'listEndpoints'])->name('endpoints');
    
    // Authentication
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    
    // Movies
    Route::group(['prefix' => 'movies', 'as' => 'movies.'], function () {
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/{movie}', [MovieController::class, 'show'])->name('show');
        // Admin-only routes are protected below
    });
    
    // Genres
    Route::group(['prefix' => 'genres', 'as' => 'genres.'], function () {
        Route::get('/', [GenreController::class, 'index'])->name('index');
        Route::get('/{genre}', [GenreController::class, 'show'])->name('show');
        Route::get('/{genre}/movies', [GenreController::class, 'movies'])->name('movies');
    });
    
    // Cinemas
    Route::group(['prefix' => 'cinemas', 'as' => 'cinemas.'], function () {
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        Route::get('/search', [CinemaController::class, 'search'])->name('search');
        Route::get('/location/{location}', [CinemaController::class, 'byLocation'])->name('by-location');
        Route::get('/{cinema}', [CinemaController::class, 'show'])->name('show');
        Route::get('/{cinema}/rooms', [CinemaController::class, 'rooms'])->name('rooms');
        Route::get('/{cinema}/movies', [CinemaController::class, 'movies'])->name('movies');
        // Admin-only routes are protected below
    });
    
    // Users (Public APIs) - Consider if these should be public
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        // Delete is moved to protected area below
    });
    
    /*
    |--------------------------------------------------------------------------
    | Protected Routes - Authentication Required
    |--------------------------------------------------------------------------
    */
    Route::middleware('api.auth')->group(function () {
        // Authentication
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        
        // User Profile
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'changePassword'])->name('change-password');
        });
        
        // User Management (Protected)
        Route::delete('users/{user}', [UserController::class, 'destroy'])
            ->middleware('api.write')
            ->name('users.destroy');
            
        /*
        |--------------------------------------------------------------------------
        | Admin Routes - Admin Role Required
        |--------------------------------------------------------------------------
        */
        Route::middleware(['role:admin'])->group(function () {
            // Movies Management
            Route::group(['prefix' => 'movies', 'as' => 'movies.', 'middleware' => 'api.write'], function () {
                Route::post('/', [MovieController::class, 'store'])->name('store');
                Route::put('/{movie}', [MovieController::class, 'update'])->name('update');
                Route::delete('/{movie}', [MovieController::class, 'destroy'])->name('destroy');
            });
            
            // Cinemas Management
            Route::group(['prefix' => 'cinemas', 'as' => 'cinemas.', 'middleware' => 'api.write'], function () {
                Route::post('/', [CinemaController::class, 'store'])->name('store');
                Route::put('/{cinema}', [CinemaController::class, 'update'])->name('update');
                Route::delete('/{cinema}', [CinemaController::class, 'destroy'])->name('destroy');
            });
        });
    });
});

/*
|--------------------------------------------------------------------------
| V2 API Routes (Template for future implementation)
|--------------------------------------------------------------------------
*/
Route::prefix('v2')->name('api.v2.')->group(function () {
    // Base information for v2
    Route::get('/', function () {
        return response()->json([
            'name' => config('app.name') . ' API',
            'version' => 'v2',
            'status' => config('api.versions.current') === 'v2' ? 'current' : 'supported',
            'message' => 'API v2 - Under development',
            'timestamp' => now()->toIso8601String()
        ]);
    })->name('info');

    // In the future, define v2 routes here
    // You can start by extending v1 functionality with improvements
});

/*
|--------------------------------------------------------------------------
| API General Routes
|--------------------------------------------------------------------------
*/

// Health Check
Route::get('/ping', function () {
    return [
        'status' => 'OK',
        'message' => 'API running',
        'timestamp' => now()->toIso8601String()
    ];
})->name('ping');

// API Fallback - for invalid routes
Route::fallback(function (Request $request) {
    $path = $request->path();
    
    // Check if we're dealing with an API request to a non-existent endpoint
    if (strpos($path, 'api/v') === 0) {
        // Extract version from path
        $parts = explode('/', $path);
        $version = $parts[1] ?? '';
        
        // Check if version exists but endpoint doesn't
        if (in_array($version, config('api.versions.supported', ['v1']))) {
            return ResponseService::error(
                'Endpoint not found',
                [
                    'requested_path' => $path,
                    'available_endpoints' => url("/api/{$version}")
                ],
                404
            );
        }
        
        // Version doesn't exist
        return ResponseService::error(
            'API version not supported', 
            [
                'requested_version' => $version,
                'available_versions' => config('api.versions.supported', ['v1']),
                'current_version' => config('api.versions.current', 'v1'),
                'api_root' => url('/api')
            ],
            404
        );
    }
    
    // For other non-API routes
    return ResponseService::error(
        'Resource not found. This is an API-only server.',
        [
            'available_endpoints' => [
                'api_info' => url('/api'),
                'api_v1' => url('/api/v1'),
                'health_check' => url('/api/ping')
            ]
        ],
        404
    );
})->name('api.fallback');