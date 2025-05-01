<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ApiErrorException;
use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    /**
     * Servicio de ubicaciones
     */
    protected $locationService;
    
    /**
     * Constructor
     * 
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Devuelve una lista paginada de sedes
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Registrar información para diagnóstico
        Log::info('LocationController@index: Solicitud recibida', [
            'api_key' => $request->header('X-API-KEY'),
            'tiempo' => now()->toDateTimeString(),
            'params' => $request->all(),
            'search_param' => $request->input('search'),
            'url_completa' => $request->fullUrl()
        ]);
        
        try {
            // Validación de parámetros
            try {
                $validated = $request->validate([
                    'page' => 'nullable|integer|min:1',
                    'per_page' => 'nullable|integer|min:1|max:100',
                    'sort_by' => 'nullable|string|in:code,name,creation_date',
                    'sort_direction' => 'nullable|string|in:asc,desc',
                    'search' => 'nullable|string|max:255',
                ]);
            } catch (ValidationException $e) {
                throw ApiErrorException::invalidQueryParameters($e->errors());
            }
            
            // Obtener ubicaciones a través del servicio
            $result = $this->locationService->getLocations($request);
            
            // Registrar éxito
            Log::info('LocationController@index: Sedes obtenidas', [
                'count' => $result['meta']['total'],
                'current_page' => $result['meta']['current_page'],
                'per_page' => $result['meta']['per_page'],
                'total_pages' => $result['meta']['last_page']
            ]);
            
            // Respuesta con metadata de paginación
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'meta' => $result['meta']
            ]);
        } catch (ApiErrorException $e) {
            // Estas excepciones ya tienen el formato correcto
            throw $e;
        } catch (\Exception $e) {
            // Registrar error
            Log::error('LocationController@index: Error al obtener sedes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Respuesta de error genérica
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Error interno del servidor al obtener las sedes',
                    'data' => [
                        'debug' => $e->getMessage()
                    ]
                ]
            ], 500);
        }
    }

    /**
     * Busca una sede por su código
     * 
     * @param string $code
     * @return JsonResponse
     */
    public function show(string $code): JsonResponse
    {
        try {
            $location = $this->locationService->getLocation($code);
            
            if (!$location) {
                throw ApiErrorException::resourceNotFound('sede', $code);
            }
            
            return response()->json([
                'success' => true,
                'data' => $this->locationService->formatLocation($location)
            ]);
        } catch (ApiErrorException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Registrar error
            Log::error('LocationController@show: Error al obtener sede', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'code' => $code
            ]);
            
            // Respuesta de error genérica
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Error interno del servidor al obtener la sede',
                    'data' => [
                        'debug' => $e->getMessage()
                    ]
                ]
            ], 500);
        }
    }
}
