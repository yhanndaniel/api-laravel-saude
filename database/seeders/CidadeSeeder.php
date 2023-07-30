<?php

namespace Database\Seeders;

use App\Models\Cidade;
use Illuminate\Database\Seeder;

class CidadeSeeder extends Seeder
{
    public function run(): void
    {
        Cidade::factory()->create([
            'nome' => 'Rio Branco',
            'estado' => 'Acre',
        ]);

        Cidade::factory()->create([
            'nome' => 'Maceió',
            'estado' => 'Alagoas',
        ]);

        Cidade::factory()->create([
            'nome' => 'Macapá',
            'estado' => 'Amapá',
        ]);

        Cidade::factory()->create([
            'nome' => 'Manaus',
            'estado' => 'Amazonas',
        ]);

        Cidade::factory()->create([
            'nome' => 'Salvador',
            'estado' => 'Bahia',
        ]);

        Cidade::factory()->create([
            'nome' => 'Fortaleza',
            'estado' => 'Ceará',
        ]);

        Cidade::factory()->create([
            'nome' => 'Brasília',
            'estado' => 'Distrito Federal',
        ]);

        Cidade::factory()->create([
            'nome' => 'Vitória',
            'estado' => 'Espírito Santo',
        ]);

        Cidade::factory()->create([
            'nome' => 'Goiânia',
            'estado' => 'Goiás',
        ]);

        Cidade::factory()->create([
            'nome' => 'São Luís',
            'estado' => 'Maranhão',
        ]);

        Cidade::factory()->create([
            'nome' => 'Cuiabá',
            'estado' => 'Mato Grosso',
        ]);

        Cidade::factory()->create([
            'nome' => 'Belo Horizonte',
            'estado' => 'Minas Gerais',
        ]);

        Cidade::factory()->create([
            'nome' => 'Belém',
            'estado' => 'Pará',
        ]);

        Cidade::factory()->create([
            'nome' => 'João Pessoa',
            'estado' => 'Paraíba',
        ]);

        Cidade::factory()->create([
            'nome' => 'Curitiba',
            'estado' => 'Paraná',
        ]);

        Cidade::factory()->create([
            'nome' => 'Recife',
            'estado' => 'Pernambuco',
        ]);

        Cidade::factory()->create([
            'nome' => 'Teresina',
            'estado' => 'Piauí',
        ]);

        Cidade::factory()->create([
            'nome' => 'Natal',
            'estado' => 'Rio Grande do Norte',
        ]);

        Cidade::factory()->create([
            'nome' => 'Porto Alegre',
            'estado' => 'Rio Grande do Sul',
        ]);

        Cidade::factory()->create([
            'nome' => 'Rio de Janeiro',
            'estado' => 'Rio de Janeiro',
        ]);

        Cidade::factory()->create([
            'nome' => 'Porto Velho',
            'estado' => 'Rondônia',
        ]);

        Cidade::factory()->create([
            'nome' => 'Boa vista',
            'estado' => 'Roraima',
        ]);

        Cidade::factory()->create([
            'nome' => 'Florianópolis',
            'estado' => 'Santa Catarina',
        ]);

        Cidade::factory()->create([
            'nome' => 'São Paulo',
            'estado' => 'São Paulo',
        ]);

        Cidade::factory()->create([
            'nome' => 'Aracaju',
            'estado' => 'Sergipe',
        ]);

        Cidade::factory()->create([
            'nome' => 'Palmas',
            'estado' => 'Tocantins',
        ]);
    }
}
