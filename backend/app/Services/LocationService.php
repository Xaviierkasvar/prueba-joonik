<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class LocationService
{
    /**
     * URL de imagen predeterminada cuando no hay una disponible
     */
    const DEFAULT_IMAGE = 'https://picsum.photos/800/400?random=1';
    
    /**
     * Verifica si una URL de imagen es accesible
     * 
     * @param string|null $url URL a verificar
     * @param int $timeout Tiempo máximo de espera en segundos
     * @return bool True si la URL es accesible, False en caso contrario
     */
    public function isImageUrlValid(?string $url, int $timeout = 2): bool
    {
        if (empty($url)) {
            return false;
        }
        
        // Optimización para URLs de placeholder.com (sabemos que son siempre válidas)
        if (strpos($url, 'placeholder.com') !== false) {
            return true;
        }
        
        // Optimización para URLs de picsum.photos (sabemos que son siempre válidas)
        if (strpos($url, 'picsum.photos') !== false) {
            return true;
        }
        
        try {
            // Configurar opciones para minimizar tiempo de espera
            $options = [
                'http' => [
                    'method' => 'HEAD',  // Solo pedir headers, no el contenido completo
                    'timeout' => $timeout,
                    'user_agent' => 'Laravel Image Validator',
                    'ignore_errors' => true, // No lanzar una excepción en códigos de error HTTP
                ]
            ];
            
            $context = stream_context_create($options);
            $headers = @get_headers($url, 1, $context);
            
            if ($headers && strpos($headers[0], '200') !== false) {
                // Verificar si el tipo de contenido es una imagen
                if (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'image/') !== false) {
                    return true;
                }
                // Algunos servidores usan un formato diferente para el header
                if (isset($headers['content-type']) && strpos($headers['content-type'], 'image/') !== false) {
                    return true;
                }
                // Si no podemos verificar el tipo de contenido, asumimos que es válido si devuelve 200
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::warning("Error al validar URL de imagen: {$url}", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Asegura que la URL de imagen sea válida o devuelve la predeterminada
     * 
     * @param string|null $url URL a validar
     * @return string URL válida o imagen predeterminada
     */
    public function ensureValidImageUrl(?string $url): string
    {
        if ($this->isImageUrlValid($url)) {
            return $url;
        }
        
        return self::DEFAULT_IMAGE;
    }

    /**
     * Obtiene una lista paginada de ubicaciones con filtros opcionales
     * 
     * @param Request $request Solicitud HTTP con parámetros de filtrado
     * @return array Array con ubicaciones formateadas y metadatos de paginación
     */
    public function getLocations(Request $request): array
    {
        // Configuración de paginación
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'creation_date');
        $sortDirection = $request->input('sort_direction', 'desc');
        $search = $request->input('search');
        
        // Construir la consulta
        $query = Location::query();
        
        // Aplicar búsqueda si existe
        if ($search) {
            // Búsqueda insensible a mayúsculas/minúsculas en PostgreSQL
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            
            // Registrar la búsqueda para diagnóstico
            Log::info('Búsqueda realizada', [
                'término' => $search,
                'término_lowercase' => strtolower($search),
                'parámetros_adicionales' => [
                    'ordenar_por' => $sortBy,
                    'dirección' => $sortDirection,
                    'página' => $request->input('page', 1),
                    'por_página' => $perPage
                ]
            ]);
        }
        
        // Ordenar resultados
        $query->orderBy($sortBy, $sortDirection);
        
        // Paginar resultados
        $locations = $query->paginate($perPage);
        
        // Formatear resultados
        $formattedLocations = $locations->map(function($location) {
            return $this->formatLocation($location);
        });
        
        // Registrar éxito
        Log::info('LocationService: Sedes obtenidas', [
            'count' => $locations->total(),
            'current_page' => $locations->currentPage(),
            'per_page' => $locations->perPage(),
            'total_pages' => $locations->lastPage()
        ]);
        
        // Retornar datos y metadatos
        return [
            'data' => $formattedLocations,
            'meta' => [
                'current_page' => $locations->currentPage(),
                'from' => $locations->firstItem(),
                'last_page' => $locations->lastPage(),
                'per_page' => $locations->perPage(),
                'to' => $locations->lastItem(),
                'total' => $locations->total(),
            ]
        ];
    }
    
    /**
     * Formatea una ubicación para la respuesta API
     * 
     * @param Location $location Ubicación a formatear
     * @return array Ubicación formateada
     */
    public function formatLocation(Location $location): array
    {
        return [
            'code' => $location->code,
            'name' => $location->name,
            'image' => $this->ensureValidImageUrl($location->image),
            'creationDate' => $location->creation_date->format('Y-m-d')
        ];
    }
    
    /**
     * Obtiene una ubicación por su código
     * 
     * @param string $code Código de la ubicación
     * @return Location|null Ubicación encontrada o null si no existe
     */
    public function getLocation(string $code): ?Location
    {
        return Location::where('code', $code)->first();
    }
}
