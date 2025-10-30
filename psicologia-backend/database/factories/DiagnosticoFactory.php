<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagnosticoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dni_estudiante' => Estudiante::inRandomOrder()->first()?->dni ?? '12345678',
            'psicologo_id' => User::where('rol', 'PSICOLOGO')->inRandomOrder()->first()?->id ?? 1,
            'fecha' => fake()->dateTimeBetween('-1 year', 'now'),
            'tipo' => fake()->randomElement([
                'Ansiedad',
                'Depresión',
                'Problemas de conducta',
                'Bajo rendimiento académico',
                'Bullying',
                'Problemas familiares',
                'Trastorno de atención',
                'Problemas de socialización'
            ]),
            'observaciones' => fake()->paragraph(3),
            'recomendaciones' => fake()->paragraph(2),
            'nivel_riesgo' => fake()->randomElement(['BAJO', 'MEDIO', 'ALTO']),
        ];
    }
}
