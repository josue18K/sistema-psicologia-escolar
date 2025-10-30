<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apoderado;

class ApoderadoSeeder extends Seeder
{
    public function run(): void
    {
        Apoderado::create([
            'nombre' => 'Rosa María Vilchez',
            'parentesco' => 'Madre',
            'correo' => 'rosa.vilchez@gmail.com',
            'telefono' => '987654321',
        ]);

        Apoderado::create([
            'nombre' => 'Pedro Sánchez Díaz',
            'parentesco' => 'Padre',
            'correo' => 'pedro.sanchez@gmail.com',
            'telefono' => '987654322',
        ]);

        Apoderado::factory()->count(30)->create();
    }
}
