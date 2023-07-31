<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medico>
 */
class MedicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'especialidade' => fake()->randomElement([
                'Clínica Médica',
                'Pediatria',
                'Cirurgia Geral',
                'Ginecologia e Obstetrícia',
                'Anestesiologia',
                'Ortopedia e Traumatologia',
                'Medicina do Trabalho',
                'Cardiologia',
                'Dermatologia',
                'Radiologia e Diagnóstico por Imagem',
                'Medicina de Família e Comunidade',
                'Psiquiatria',
                'Medicina de Tráfego',
                'Administração da saúde',
                'Geriatria',
                'Infectologia',
                'Genética Médica',
                'Medicina do Exercício e do Esporte',
                'Medicina Legal e Perícia Médica',
                'Medicina Física e Reabilitação Fisiatria',
                'Neurocirurgia',
                'Otorrinolaringologia',
                'Medicina Intensiva',
                'Oftalmologia',
                'Cirurgia Cardiovascular',
                'Neurologia',
                'Cirurgia do Aparelho Digestivo',
                'Cirurgia Vascular',
                'Cirurgia Pediátrica',
                'Pneumologia',
                'Nutrologia',
                'Patologia',
                'Hematologia e Hemoterapia',
                'Endocrinologia',
                'Medicina Hiperbárica',
                'Imunologia e Alergologia',
                'Medicina Nuclear',
                'Radioterapia Radio-Oncologia',
                'Cirurgia de Cabeça e Pescoço',
                'Cirurgia Torácica',
                'Urologia',
                'Gastroenterologia',
                'Cirurgia Plástica',
                'Coloproctologia',
                'Oncologia Clínica',
                'Mastologia',
                'Nefrologia',
                'Reumatologia'
            ]),
            'cidade_id' => fake()->numberBetween(1, 26),
        ];
    }
}
