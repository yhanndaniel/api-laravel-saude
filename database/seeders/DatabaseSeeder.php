<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Christian Ramires',
            'email' => 'christian.ramires@example.com',
        ]);

        $this->call([
            CidadeSeeder::class,
            MedicoSeeder::class,
            PacienteSeeder::class,
            MedicoPacienteSeeder::class
        ]);
    }
}
