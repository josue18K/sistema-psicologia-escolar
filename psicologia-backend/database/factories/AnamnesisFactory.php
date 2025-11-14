<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnamnesisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'dni_estudiante' => Estudiante::inRandomOrder()->first()->dni,
            'psicologo_id' => User::where('rol', 'PSICOLOGO')->inRandomOrder()->first()->id,
            'fecha_evaluacion' => fake()->dateTimeBetween('-1 year', 'now'),

            // Datos familiares
            'numero_hijos' => fake()->numberBetween(1, 6),
            'personas_vive_con' => fake()->randomElement(['Ambos padres', 'Solo madre', 'Solo padre', 'Abuelos', 'Tíos']),
            'lugar_ocupa' => fake()->numberBetween(1, 5),

            // Padre
            'padre_nombre' => fake()->name('male'),
            'padre_edad' => fake()->numberBetween(30, 60),
            'padre_escolaridad' => fake()->randomElement(['Primaria', 'Secundaria', 'Técnico', 'Universitario']),
            'padre_ocupacion' => fake()->jobTitle(),

            // Madre
            'madre_nombre' => fake()->name('female'),
            'madre_edad' => fake()->numberBetween(28, 55),
            'madre_escolaridad' => fake()->randomElement(['Primaria', 'Secundaria', 'Técnico', 'Universitario']),
            'madre_ocupacion' => fake()->jobTitle(),

            // Relaciones familiares
            'relacion_madre' => fake()->randomElement(['EXCELENTE', 'BUENA', 'REGULAR', 'MALA']),
            'relacion_padre' => fake()->randomElement(['EXCELENTE', 'BUENA', 'REGULAR', 'MALA']),
            'relacion_hermanos' => fake()->randomElement(['EXCELENTE', 'BUENA', 'REGULAR', 'MALA']),
            'relacion_otros_familiares' => fake()->randomElement(['EXCELENTE', 'BUENA', 'REGULAR', 'MALA']),

            // Antecedentes escolares
            'inicial_edad_ingreso' => '3 años',
            'primaria_edad_ingreso' => '6 años',
            'secundaria_edad_ingreso' => '12 años',

            // Antecedentes prenatales
            'madre_enfermedades_embarazo' => fake()->optional()->sentence(),
            'madre_medicamentos_embarazo' => fake()->optional()->sentence(),

            // Parto
            'sintomas_perdida' => fake()->optional()->word(),
            'estado_nutricional' => fake()->randomElement(['Normal', 'Bajo peso', 'Sobrepeso']),
            'estado_emocional' => fake()->randomElement(['Estable', 'Ansioso', 'Tranquilo']),
            'parto_semanas' => fake()->numberBetween(37, 42),
            'parto_peso' => fake()->randomFloat(2, 2.5, 4.5),
            'parto_talla' => fake()->randomFloat(2, 45, 55),
            'parto_apgar' => fake()->numberBetween(7, 10),
            'tratamientos_parto' => fake()->optional()->sentence(),
            'enfermedades_importantes' => fake()->optional()->sentence(),

            // Desarrollo psicomotor
            'edad_controlo_cabeza' => fake()->numberBetween(2, 5),
            'edad_se_sento' => fake()->numberBetween(5, 8),
            'edad_se_paro' => fake()->numberBetween(8, 12),
            'edad_camino' => fake()->numberBetween(10, 18),
            'edad_controlo_esfinter' => fake()->numberBetween(18, 36),
            'edad_durmio_solo' => fake()->numberBetween(12, 48),

            // Desarrollo del lenguaje
            'edad_primeras_palabras' => fake()->numberBetween(10, 18),
            'edad_oraciones' => fake()->numberBetween(18, 30),
            'edad_comprendio_instrucciones' => fake()->numberBetween(12, 24),

            // Evaluaciones anteriores
            'evaluacion_neurologica' => fake()->boolean(30),
            'evaluacion_neurologica_fecha' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'evaluacion_psicologica' => fake()->boolean(40),
            'evaluacion_psicologica_fecha' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'evaluacion_psiquiatrica' => fake()->boolean(20),
            'evaluacion_psiquiatrica_fecha' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'evaluacion_psicopedagogica' => fake()->boolean(35),
            'evaluacion_psicopedagogica_fecha' => fake()->optional()->dateTimeBetween('-2 years', 'now'),

            // Hábitos
            'problemas_dormir' => fake()->boolean(30),
            'horas_sueno' => fake()->numberBetween(6, 10),
            'habitos_estudio' => fake()->boolean(60),
            'cuales_habitos_estudio' => fake()->optional()->sentence(),
            'actividades_intereses' => fake()->sentence(),

            // Otros
            'autoestima_tolerancia_frustracion' => fake()->paragraph(),
            'observaciones' => fake()->optional()->paragraph(),
        ];
    }
}
