<?php

namespace Database\Seeders;

use AdminUserSeeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'password' => Hash::make('admin1234'), // Cambia la contraseña luego
            'email' => 'jtrigo.corp@gmail.com',
            'role' => 'admin',
        ]);

        $this->call([
            CategorySeeder::class,
            ServiceProviderCategorySeeder::class,
            AdminUserSeeder::class,
            // ... otros seeders
        ]);

        //$this->call(AdminUserSeeder::class);
    }
}
