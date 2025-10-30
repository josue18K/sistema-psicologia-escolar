<?php

namespace Database\Factories;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dni_estudiante' => Estudiante::inRandomOrder()->first()?->dni ?? '12345678',
            'fecha_cita' => fake()->dateTimeBetween('now', '+2 months'),
            'motivo' => fake()->sentence(10),
            'estado' => fake()->randomElement(['PENDIENTE', 'REALIZADA', 'CANCELADA']),
            'correo_enviado' => fake()->boolean(70),
        ];
    }
}
