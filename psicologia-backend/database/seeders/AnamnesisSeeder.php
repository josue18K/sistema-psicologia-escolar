<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anamnesis;

class AnamnesisSeeder extends Seeder
{
    public function run(): void
    {
        Anamnesis::factory()->count(15)->create();
    }
}
