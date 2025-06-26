<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;
use App\Models\Category;

class ServiceProviderCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos proveedores y categorías para vincular
        $provider1 = ServiceProvider::find(1); // Asume que tienes un proveedor con ID 1
        $provider2 = ServiceProvider::find(2); // Asume que tienes un proveedor con ID 2

        $gasfiteria = Category::where('name', 'Gasfitería')->first();
        $electricidad = Category::where('name', 'Electricidad')->first();
        $limpieza = Category::where('name', 'Limpieza del Hogar')->first();
        $jardineria = Category::where('name', 'Jardinería')->first();

        // Vincular categorías a proveedores
        if ($provider1 && $gasfiteria && $electricidad) {
            $provider1->categories()->syncWithoutDetaching([$gasfiteria->id, $electricidad->id]);
            $this->command->info('Proveedor 1 asociado a Gasfitería y Electricidad.');
        } else {
            $this->command->warn('No se pudo asociar categorías al Proveedor 1. Asegúrate de que existan.');
        }

        if ($provider2 && $limpieza && $jardineria) {
            $provider2->categories()->syncWithoutDetaching([$limpieza->id, $jardineria->id]);
            $this->command->info('Proveedor 2 asociado a Limpieza del Hogar y Jardinería.');
        } else {
            $this->command->warn('No se pudo asociar categorías al Proveedor 2. Asegúrate de que existan.');
        }

        // Si no existen, puedes crear algunos proveedores y categorías aquí mismo para probar
        // Ejemplo:
        // if (!$provider1) {
        //     $user = \App\Models\User::factory()->create(['role' => 'service_provider']);
        //     $provider1 = ServiceProvider::create(['user_id' => $user->id]);
        //     $this->command->info('Proveedor de prueba 1 creado.');
        // }
    }
}
