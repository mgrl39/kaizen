<?php

/**
 * @file web.php
 * API-only backend server - all routes redirect to API
 */

use Illuminate\Support\Facades\Route;
use App\Services\ResponseService;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Since this is an API-only application, all web routes will redirect
| to API information endpoints or return JSON responses.
|
*/

// Admin Routes
Route::prefix('admin')->group(function () {
    // Authentication Routes
    Route::get('login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminController::class, 'login']);

    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('logout', [AdminController::class, 'logout'])->name('admin.logout');
    });

    // Maintenance Routes
    Route::get('/maintenance', [App\Http\Controllers\Admin\MaintenanceController::class, 'index']);
    Route::post('/maintenance/execute', [App\Http\Controllers\Admin\MaintenanceController::class, 'executeCommand']);
});

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
                'health_check' => url('/api/ping'),
                'admin_login' => url('/admin/login')
            ]
        ],
        404
    );
});