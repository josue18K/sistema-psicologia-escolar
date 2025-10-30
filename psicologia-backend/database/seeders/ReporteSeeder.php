<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reporte;

class ReporteSeeder extends Seeder
{
    public function run(): void
    {
        Reporte::factory()->count(10)->create();
    }
}
