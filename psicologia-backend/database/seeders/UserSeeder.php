<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario TOE
        User::create([
            'name' => 'Coordinador TOE',
            'email' => 'toe@colegio.com',
            'password' => Hash::make('password123'),
            'rol' => 'TOE',
            'estado' => true,
        ]);

        // Psicólogos
        User::create([
            'name' => 'Dr. Juan Pérez',
            'email' => 'psicologo@colegio.com',
            'password' => Hash::make('password123'),
            'rol' => 'PSICOLOGO',
            'estado' => true,
        ]);

        User::create([
            'name' => 'Dra. María García',
            'email' => 'psicologo2@colegio.com',
            'password' => Hash::make('password123'),
            'rol' => 'PSICOLOGO',
            'estado' => true,
        ]);

        // Director
        User::create([
            'name' => 'Director Escolar',
            'email' => 'director@colegio.com',
            'password' => Hash::make('password123'),
            'rol' => 'DIRECTOR',
            'estado' => true,
        ]);

        // Tutores
        User::create([
            'name' => 'Prof. Carlos Rodríguez',
            'email' => 'tutor1@colegio.com',
            'password' => Hash::make('password123'),
            'rol' => 'TUTOR',
            'estado' => true,
        ]);

        User::create([
            'name' => 'Prof. Ana López',
            'email' => 'tutor2@colegio.com',
            'password' => Hash::make('password123'),
            'rol' => 'TUTOR',
            'estado' => true,
        ]);

        // Usuarios adicionales
        User::factory()->tutor()->count(5)->create();
        User::factory()->psicologo()->count(2)->create();
    }
}
