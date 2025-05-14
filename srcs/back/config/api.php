<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configuración de límites de tasa para diferentes rutas API
    |
    */
    'rate_limiting' => [
        // Límites para rutas públicas
        'public' => [
            'attempts' => 60,    // Número de intentos
            'period' => 60,      // Periodo en minutos
        ],
        
        // Límites para rutas autenticadas
        'authenticated' => [
            'attempts' => 120,   // Más alto para usuarios autenticados
            'period' => 60,
        ],
        
        // Límites para operaciones de escritura
        'write_operations' => [
            'attempts' => 30,
            'period' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Versions
    |--------------------------------------------------------------------------
    |
    | Configuration for available API versions
    |
    */
    'versions' => [
        'current' => 'v1',               // Current active version
        'supported' => ['v1', 'v2'],     // All supported versions
        'deprecated' => [],              // Deprecated versions (still available but will be removed)
        'development' => ['v2'],         // Versions under development (may not be fully functional)
        
        // Version-specific configurations
        'v1' => [
            'status' => 'stable',        // Version status: stable, beta, etc.
            'release_date' => '2023-06-15',
            'sunset_date' => null,       // Date when this version will be deprecated
            'default_format' => 'json',
        ],
        'v2' => [
            'status' => 'development',
            'release_date' => null,      // Not yet released
            'sunset_date' => null,
            'default_format' => 'json',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Formats
    |--------------------------------------------------------------------------
    |
    | Configuraciones para el formato de respuestas API
    |
    */
    'response' => [
        'include_timestamp' => true,  // Incluir timestamp en respuestas
        'default_page_size' => 15,    // Tamaño default para paginación
        'max_page_size' => 100,       // Tamaño máximo para paginación
    ],
]; 