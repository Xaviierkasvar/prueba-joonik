<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar datos existentes
        DB::table('locations')->truncate();
        
        // Crear instancia de Faker en español
        $faker = Faker::create('es_ES');
        
        // Función para generar URL de imagen aleatoria
        $getRandomImageUrl = function() {
            // Generar un ID aleatorio entre 1 y 1000 para picsum.photos
            $randomId = rand(1, 1000);
            return "https://picsum.photos/id/{$randomId}/800/600";
        };
        
        // Ya no usaremos imágenes de Unsplash debido a problemas de disponibilidad
        
        // Imagen predeterminada para sedes sin imagen
        $defaultImage = 'https://picsum.photos/800/600';
        
        // Mantener las sedes originales pero traducidas al español
        $initialLocations = [
            [
                'code' => 'SED001',
                'name' => 'Sede Principal',
                'image' => $getRandomImageUrl(),
                'creation_date' => Carbon::create(2023, 1, 15),
            ],
            [
                'code' => 'SED002',
                'name' => 'Oficina Centro',
                'image' => $getRandomImageUrl(),
                'creation_date' => Carbon::create(2023, 2, 20),
            ],
            [
                'code' => 'SED003',
                'name' => 'Sucursal Oeste',
                'image' => $getRandomImageUrl(),
                'creation_date' => Carbon::create(2023, 3, 10),
            ],
            [
                'code' => 'SED004',
                'name' => 'Campus Este',
                'image' => $getRandomImageUrl(),
                'creation_date' => Carbon::create(2023, 4, 5),
            ],
            [
                'code' => 'SED005',
                'name' => 'Instalación Norte',
                'image' => $getRandomImageUrl(),
                'creation_date' => Carbon::create(2023, 5, 12),
            ],
        ];
        
        // Insertar las sedes iniciales
        foreach ($initialLocations as $locationData) {
            // La validación se hará automáticamente gracias al evento saving del modelo
            Location::create($locationData);
        }
        
        // Sedes específicas con imágenes confiables
        $specificLocations = [
            [
                'code' => 'VLL001',
                'name' => 'Complejo Valladolid',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subMonths(3)
            ],
            [
                'code' => 'VAL002',
                'name' => 'Campus Valencia',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subMonths(2)
            ],
            [
                'code' => 'CDB003',
                'name' => 'Oficina Córdoba',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subMonths(1)
            ],
            [
                'code' => 'MAD004',
                'name' => 'Sede Madrid',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subWeeks(2)
            ],
            [
                'code' => 'BCN005',
                'name' => 'Centro Barcelona',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subWeeks(1)
            ],
            [
                'code' => 'CENT006',
                'name' => 'Oficina Central',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subDays(5)
            ],
            [
                'code' => 'CENT007',
                'name' => 'Centro Operativo',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()
            ]
        ];
        
        // Insertar las sedes específicas con imágenes validadas
        foreach ($specificLocations as $locationData) {
            // La validación se hará automáticamente gracias al evento saving del modelo
            Location::create($locationData);
        }
        
        // Añadir tres sedes adicionales con "cent" en su nombre o código
        $centLocations = [
            [
                'code' => 'CENT008',
                'name' => 'Centro Logístico',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subDays(2)
            ],
            [
                'code' => 'CENT009',
                'name' => 'Centro Comercial',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()->subDays(1)
            ],
            [
                'code' => 'EST010',
                'name' => 'Centro Tecnológico',
                'image' => $getRandomImageUrl(),
                'creation_date' => now()
            ]
        ];
        
        // Insertar las tres sedes adicionales con "cent"
        foreach ($centLocations as $locationData) {
            Location::create($locationData);
        }
        
        // Generar sedes aleatorias adicionales (20 más para llegar a 30 en total)
        for ($i = 0; $i < 20; $i++) {
            $randomLocation = [
                'code' => 'LOC' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'name' => $faker->company,
                'image' => $getRandomImageUrl(),
                'creation_date' => $faker->dateTimeBetween('-1 year', 'now')
            ];
            
            Location::create($randomLocation);
        }
        
        Log::info('Seeder completado: Se crearon 30 sedes con sus respectivas imágenes');
        
        // Log para diagnóstico - registrar las sedes creadas con "cent" en su nombre o código
        $sedesCent = Location::where('name', 'like', '%cent%')
            ->orWhere('code', 'like', '%CENT%')
            ->get()
            ->map(function ($sede) {
                return [
                    'code' => $sede->code,
                    'name' => $sede->name
                ];
            });
        
        Log::info('Seeder ejecutado exitosamente', [
            'total_sedes' => Location::count(),
            'sedes_con_cent' => $sedesCent->count(),
            'sedes_cent' => $sedesCent->toArray()
        ]);
    }
}
