<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\Apoderado;

class EstudianteSeeder extends Seeder
{
    public function run(): void
    {
        $tutores = User::where('rol', 'TUTOR')->get();
        $apoderados = Apoderado::all();

        Estudiante::create([
            'dni' => '12345678',
            'nombres' => 'Juan Carlos',
            'apellidos' => 'Pérez García',
            'fecha_nacimiento' => '2010-05-15',
            'edad' => 14,
            'grado' => '2do',
            'seccion' => 'A',
            'tutor_id' => $tutores->random()->id ?? null,
            'apoderado_id' => $apoderados->random()->id ?? null,
        ]);

        Estudiante::factory()->count(50)->create();
    }
}
