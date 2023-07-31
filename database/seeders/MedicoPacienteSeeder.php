<?php

namespace Database\Seeders;

use App\Models\MedicoPaciente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicoPacienteSeeder extends Seeder
{
    public function run(): void
    {
        MedicoPaciente::factory()->count(200)->create();
    }
}
