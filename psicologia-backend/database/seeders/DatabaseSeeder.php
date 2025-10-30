<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ApoderadoSeeder::class,
            EstudianteSeeder::class,
            DiagnosticoSeeder::class,
            CitaSeeder::class,
            ReporteSeeder::class,
            NotificacionSeeder::class,
        ]);
    }
}
