<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Standard success response
     *
     * @param mixed $data The data to return
     * @param string $message Informative message
     * @param int $statusCode HTTP code (default: 200)
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Operation completed successfully', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Standard error response
     *
     * @param string $message Error message
     * @param mixed $errors Detailed errors (optional)
     * @param int $statusCode HTTP code (default: 400)
     * @return JsonResponse
     */
    public static function error(string $message = 'An error has occurred', $errors = null, int $statusCode = 400): JsonResponse
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
     * Response with paginated data
     *
     * @param mixed $data Paginated data
     * @param string $message Informative message
     * @return JsonResponse
     */
    public static function paginated($data, string $message = 'Data retrieved successfully'): JsonResponse
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