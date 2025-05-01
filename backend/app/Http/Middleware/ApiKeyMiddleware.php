<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ApiErrorException;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     * This middleware validates the API key provided in the request header.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Registrar información para diagnóstico
        Log::info('ApiKeyMiddleware: Validando API key', [
            'api_key_header' => $request->header('X-API-KEY'),
            'tiempo' => now()->toDateTimeString(),
            'headers_completos' => $request->headers->all(),
        ]);
        
        // Get API key from request header
        $requestApiKey = $request->header('X-API-KEY');
        
        // Get valid API key from config
        $validApiKey = config('api.key');
        
        Log::info('ApiKeyMiddleware: Verificación de claves de API', [
            'request_key' => $requestApiKey,
            'valid_key' => $validApiKey,
            'env_api_key' => env('API_KEY'),
            'coinciden' => $requestApiKey === $validApiKey
        ]);
        
        // Validate API key
        if (!$requestApiKey || $requestApiKey !== $validApiKey) {
            throw ApiErrorException::invalidApiKey($requestApiKey);
        }
        
        return $next($request);
    }
}
