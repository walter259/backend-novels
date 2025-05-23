<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles si no existen
        if (Role::count() === 0) {
            Role::create(['name' => 'Usuario']);
            Role::create(['name' => 'Moderador']);
            Role::create(['name' => 'Admin']);
        }

        // Crear un administrador inicial si no existe
        if (!User::where('role_id', 3)->exists()) {
            User::create([
                'user' => 'admin',
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role_id' => 3, // Admin
            ]);
        }
    }
}