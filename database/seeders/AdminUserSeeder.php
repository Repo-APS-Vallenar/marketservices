<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'jtrigo.corp@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin1234'), // Cambia la contraseña luego
                'role' => 'admin',
            ]
        );
    }
} 