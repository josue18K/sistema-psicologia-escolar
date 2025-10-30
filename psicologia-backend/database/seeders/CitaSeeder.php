<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        Cita::factory()->count(25)->create();
    }
}
