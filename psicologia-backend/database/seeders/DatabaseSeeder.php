<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,           // 1. Primero usuarios (psicólogos, tutores, etc.)
            ApoderadoSeeder::class,      // 2. Apoderados (sin dependencias)
            EstudianteSeeder::class,     // 3. Estudiantes (depende de User y Apoderado)
            DiagnosticoSeeder::class,    // 4. Diagnósticos (depende de Estudiante y User)
            AnamnesisSeeder::class,      // 5. Anamnesis (depende de Estudiante y User) ✅ AGREGAR AQUÍ
            CitaSeeder::class,           // 6. Citas (depende de Estudiante)
            ReporteSeeder::class,        // 7. Reportes (depende de User)
            NotificacionSeeder::class,   // 8. Notificaciones (depende de User)
        ]);
    }
}
