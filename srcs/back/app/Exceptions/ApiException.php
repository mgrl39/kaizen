<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class ApiException extends Exception
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var mixed
     */
    protected $errors;

    /**
     * Constructor
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = 'Ha ocurrido un error en la API',
        int $statusCode = 400,
        $errors = null,
        Exception $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * Renderizar la excepción como respuesta HTTP
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return ResponseService::error($this->getMessage(), $this->errors, $this->statusCode);
    }

    /**
     * Obtener el código de estado HTTP
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Obtener los errores detallados
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
} 