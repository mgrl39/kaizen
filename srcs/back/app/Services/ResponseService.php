<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Respuesta exitosa estándar
     *
     * @param mixed $data Los datos a devolver
     * @param string $message Mensaje informativo
     * @param int $statusCode Código HTTP (default: 200)
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Operación completada con éxito', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Respuesta de error estándar
     *
     * @param string $message Mensaje de error
     * @param mixed $errors Errores detallados (opcional)
     * @param int $statusCode Código HTTP (default: 400)
     * @return JsonResponse
     */
    public static function error(string $message = 'Ha ocurrido un error', $errors = null, int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Respuesta con datos paginados
     *
     * @param mixed $data Datos paginados
     * @param string $message Mensaje informativo
     * @return JsonResponse
     */
    public static function paginated($data, string $message = 'Datos recuperados con éxito'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'path' => $data->path(),
                'per_page' => $data->perPage(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ],
            'links' => [
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
                'prev' => $data->previousPageUrl(),
                'next' => $data->nextPageUrl(),
            ]
        ]);
    }
} 