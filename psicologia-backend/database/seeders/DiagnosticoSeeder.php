<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Diagnostico;

class DiagnosticoSeeder extends Seeder
{
    public function run(): void
    {
        Diagnostico::factory()->count(40)->create();
    }
}
