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
    | Versiones API
    |--------------------------------------------------------------------------
    |
    | Configuración de versiones disponibles de la API
    |
    */
    'versions' => [
        'current' => 'v1',
        'deprecated' => [],      // Versiones obsoletas pero aún disponibles
        'supported' => ['v1'],   // Versiones soportadas
    ],

    /*
    |--------------------------------------------------------------------------
    | Formatos de Respuesta
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