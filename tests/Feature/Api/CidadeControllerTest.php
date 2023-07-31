<?php

namespace Tests\Feature\Api;

use App\Models\Cidade;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CidadeControllerTest extends TestCase
{
    use RefreshDatabase;

    private $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->token = $this->login();
    }

    private function login(): string
    {
        $user = User::factory()->createOne();

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return $loginResponse->json('access_token');
    }

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

        $response = $this->postJson('/api/cidades', $cidade, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($cidade) {
            $json->hasAll(['id', 'nome', 'estado', 'created_at', 'updated_at']);

            $json->whereAll([
                'nome' => $cidade['nome'],
                'estado' => $cidade['estado'],
            ])->etc();
        });
    }

    public function test_cidades_update_endpoint(): void
    {
        $cidade = Cidade::factory()->createOne()->toArray();

        $newCity = [
            'nome' => 'Nova Cidade',
            'estado' => 'São Paulo',
        ];

        $response = $this->putJson('/api/cidades/' . $cidade['id'], $newCity, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($newCity) {
            $json->hasAll(['id', 'nome', 'estado', 'created_at', 'updated_at']);

            $json->whereAll($newCity)->etc();
        });
    }

    public function test_cidades_destroy_endpoint(): void
    {
        $cidade = Cidade::factory()->createOne();

        $response = $this->deleteJson('/api/cidades/' . $cidade->id, [], [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(204);
    }

    public function test_cidades_medicos_endpoint(): void
    {
        $cidade = Cidade::factory()->createOne();
        $medicos = Medico::factory(4)->create([
            'cidade_id' => $cidade->id
        ]);

        $response = $this->getJson('/api/cidades/'.$cidade->id.'/medicos');

        $response->assertStatus(200);

        $response->assertJsonCount(4);
        $response->assertJson(function (AssertableJson $json) use ($medicos) {
            $json->hasAll(['0.id', '0.nome', '0.especialidade', '0.cidade_id', '0.created_at', '0.updated_at']);

            $json->whereAllType([
                '0.id' => 'integer',
                '0.nome' => 'string',
                '0.especialidade' => 'string',
                '0.cidade_id' => 'integer'
            ]);

            $medico = $medicos->first();

            $json->whereAll([
                '0.id' => $medico->id,
                '0.nome' => $medico->nome,
                '0.especialidade' => $medico->especialidade,
                '0.cidade_id' => $medico->cidade_id
            ]);
        });
    }
}
