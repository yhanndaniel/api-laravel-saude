<?php

namespace Tests\Feature\Api;

use App\Models\Cidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CidadeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cidades_index_endpoint(): void
    {
        $cidades = Cidade::factory(3)->create();
        $response = $this->getJson('/api/cidades');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJson(function (AssertableJson $json) use ($cidades) {
            $json->hasAll(['0.id', '0.nome', '0.estado']);

            $json->whereAllType([
                '0.id' => 'integer',
                '0.nome' => 'string',
                '0.estado' => 'string',
            ]);

            $cidade = $cidades->first();

            $json->whereAll([
                '0.id' => $cidade->id,
                '0.nome' => $cidade->nome,
                '0.estado' => $cidade->estado,
            ]);
        });
    }

    public function test_cidades_show_endpoint(): void
    {
        $cidade = Cidade::factory()->createOne();

        $response = $this->getJson('/api/cidades/' . $cidade->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($cidade) {
            $json->hasAll(['id', 'nome', 'estado', 'created_at', 'updated_at', 'deleted_at']);

            $json->whereAllType([
               'id' => 'integer',
               'nome' => 'string',
               'estado' => 'string'
            ]);

            $json->whereAll([
                'id' => $cidade->id,
                'nome' => $cidade->nome,
                'estado' => $cidade->estado,
            ]);
        });
    }

    public function test_cidades_store_endpoint(): void
    {
        $cidade = Cidade::factory()->makeOne()->toArray();

        $response = $this->postJson('/api/cidades', $cidade);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($cidade) {
            $json->hasAll(['id', 'nome', 'estado', 'created_at', 'updated_at']);

            $json->whereAll([
                'nome' => $cidade['nome'],
                'estado' => $cidade['estado'],
            ])->etc();
        });
    }
}
