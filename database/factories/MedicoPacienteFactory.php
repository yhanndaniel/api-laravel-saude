<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedicoPacienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'medico_id' => $this->faker->numberBetween(1, 100),
            'paciente_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
