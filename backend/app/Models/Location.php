<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Location extends Model
{
    use HasFactory;
    
    /**
     * URL de imagen predeterminada cuando no hay una disponible
     */
    public const DEFAULT_IMAGE = 'https://picsum.photos/800/400?random=1';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'image',
        'creation_date',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'creation_date' => 'datetime',
    ];
    
    /**
     * Verifica si una URL de imagen es accesible
     * 
     * @param string|null $url URL a verificar
     * @param int $timeout Tiempo máximo de espera en segundos
     * @return bool True si la URL es accesible, False en caso contrario
     */
    public static function isImageUrlValid(?string $url, int $timeout = 2): bool
    {
        if (empty($url)) {
            return false;
        }
        
        // Optimización para URLs de placeholder.com (sabemos que son siempre válidas)
        if (strpos($url, 'placehold.co') !== false) {
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
    public static function ensureValidImageUrl(?string $url): string
    {
        if (self::isImageUrlValid($url)) {
            return $url;
        }
        
        return self::DEFAULT_IMAGE;
    }
    
    /**
     * Evento boot del modelo
     */
    protected static function boot()
    {
        parent::boot();
        
        // Evento antes de guardar un registro
        static::saving(function ($location) {
            // Verificar y corregir la URL de la imagen si es necesario
            if (!self::isImageUrlValid($location->image)) {
                Log::info("Corrigiendo URL de imagen inválida para la sede {$location->code}", [
                    'nombre' => $location->name,
                    'url_original' => $location->image
                ]);
                $location->image = self::DEFAULT_IMAGE;
            }
        });
    }
}
