<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ApiErrorException extends Exception
{
    protected $statusCode;
    protected $errorCode;
    protected $errorData;

    /**
     * Constructor para la excepción personalizada de API
     * 
     * @param string $message Mensaje de error
     * @param int $statusCode Código HTTP de estado
     * @param string $errorCode Código de error interno para la aplicación
     * @param array $errorData Datos adicionales de error
     */
    public function __construct(string $message = "", int $statusCode = 400, string $errorCode = 'ERROR_GENERAL', array $errorData = [])
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode;
        $this->errorData = $errorData;
    }

    /**
     * Convierte la excepción en una respuesta JSON para la API
     * 
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->getMessage(),
                'data' => $this->errorData
            ]
        ], $this->statusCode);
    }

    /**
     * Retorna error por clave API inválida
     */
    public static function invalidApiKey(string $providedKey = null): self
    {
        return new self(
            'Clave API inválida o faltante',
            401,
            'INVALID_API_KEY',
            ['provided_key' => $providedKey]
        );
    }
    
    /**
     * Retorna error por recurso no encontrado
     */
    public static function resourceNotFound(string $resource, $id = null): self
    {
        return new self(
            "El recurso {$resource} no fue encontrado",
            404,
            'RESOURCE_NOT_FOUND',
            ['resource' => $resource, 'id' => $id]
        );
    }
    
    /**
     * Retorna error por parámetros de consulta inválidos
     */
    public static function invalidQueryParameters(array $errors): self
    {
        return new self(
            'Parámetros de consulta inválidos',
            400,
            'INVALID_QUERY_PARAMETERS',
            ['errors' => $errors]
        );
    }
}
