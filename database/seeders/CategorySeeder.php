<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // Asegúrate de que el namespace de tu modelo Category sea correcto

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = [
            // Servicios de Reparación y Mantenimiento del Hogar
            ['name' => 'Gasfitería', 'description' => 'Reparación de fugas, instalaciones de grifos, inodoros y sistemas de tuberías.'],
            ['name' => 'Electricidad', 'description' => 'Instalación de luminarias, reparación de circuitos, cableado y problemas eléctricos.'],
            ['name' => 'Albañilería', 'description' => 'Trabajos de construcción, reparación de muros, pavimentación y remodelaciones.'],
            ['name' => 'Pintura', 'description' => 'Pintado de interiores y exteriores, preparación de superficies y retoques.'],
            ['name' => 'Cerrajería', 'description' => 'Instalación y reparación de cerraduras, apertura de puertas y copias de llaves.'],
            ['name' => 'Reparación de Electrodomésticos', 'description' => 'Mantenimiento y reparación de lavadoras, refrigeradores, hornos, etc.'],
            ['name' => 'Calefacción y Aire Acondicionado', 'description' => 'Instalación, mantenimiento y reparación de sistemas de climatización.'],
            ['name' => 'Vidriería', 'description' => 'Instalación y reparación de vidrios, ventanas y espejos.'],
            
            // Servicios de Limpieza y Jardinería
            ['name' => 'Limpieza del Hogar', 'description' => 'Servicios de limpieza general, profunda y post-construcción para residencias.'],
            ['name' => 'Limpieza de Alfombras y Tapicería', 'description' => 'Limpieza especializada para alfombras, sillones y cortinas.'],
            ['name' => 'Jardinería', 'description' => 'Corte de césped, poda, diseño de jardines y mantenimiento de áreas verdes.'],
            ['name' => 'Control de Plagas', 'description' => 'Exterminación y prevención de insectos, roedores y otras plagas.'],
            
            // Instalaciones y Montajes
            ['name' => 'Montaje de Muebles', 'description' => 'Ensamblaje y desarme de muebles de todo tipo.'],
            ['name' => 'Instalación de TV y Sonido', 'description' => 'Instalación y configuración de televisores, sistemas de audio y home cinema.'],
            ['name' => 'Instalación de Pisos', 'description' => 'Colocación de cerámicas, flotantes, laminados y otros tipos de pisos.'],

            // Remodelaciones
            ['name' => 'Remodelación de Cocinas', 'description' => 'Diseño y ejecución de proyectos de remodelación de cocinas.'],
            ['name' => 'Remodelación de Baños', 'description' => 'Diseño y ejecución de proyectos de remodelación de baños.'],
            
            // Otros servicios relacionados con el hogar
            ['name' => 'Mudanzas', 'description' => 'Servicios de transporte y logística para mudanzas residenciales.'],
            ['name' => 'Soporte Informático a Domicilio', 'description' => 'Reparación de computadoras, configuración de redes y asistencia técnica en el hogar.'],
            ['name' => 'Diseño de Interiores', 'description' => 'Asesoría y proyectos para la decoración y optimización de espacios interiores.'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']], // Busca por nombre
                ['description' => $categoryData['description']] // Si no existe, crea con descripción
            );
        }
    }
}
