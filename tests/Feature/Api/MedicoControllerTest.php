<?php

namespace Tests\Feature\Api;

use App\Models\Medico;
use App\Models\User;
use Database\Seeders\CidadeSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MedicoControllerTest extends TestCase
{
    use RefreshDatabase;

    private $token;

    public function setUp(): void
    {
        parent::setUp();
        app(DatabaseSeeder::class)->call(CidadeSeeder::class);

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
    public function test_medicos_index_endpoint(): void
    {
        $medicos = Medico::factory(3)->create();
        $response = $this->getJson('/api/medicos');

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $json) use ($medicos) {
            $json->hasAll(['0.id', '0.nome', '0.especialidade', '0.cidade_id']);

            $json->whereAllType([
                '0.id' => 'integer',
                '0.nome' => 'string',
                '0.especialidade' => 'string',
                '0.cidade_id' => 'integer',
            ]);

            $medico = $medicos->first();

            $json->whereAll([
                '0.id' => $medico->id,
                '0.nome' => $medico->nome,
                '0.especialidade' => $medico->especialidade,
                '0.cidade_id' => $medico->cidade_id,
            ]);
        });
    }

    public function test_medicos_show_endpoint(): void
    {
        $medico = Medico::factory()->createOne();
        $response = $this->getJson('/api/medicos/' . $medico->id);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($medico) {
            $json->hasAll(['id', 'nome', 'especialidade', 'cidade_id', 'created_at', 'updated_at', 'deleted_at']);

            $json->whereAllType([
                'id' => 'integer',
                'nome' => 'string',
                'especialidade' => 'string',
                'cidade_id' => 'integer',
            ]);

            $json->whereAll([
                'id' => $medico->id,
                'nome' => $medico->nome,
                'especialidade' => $medico->especialidade,
                'cidade_id' => $medico->cidade_id,
            ]);
        });
    }

    public function test_medicos_store_endpoint(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();
        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($medico) {
            $json->hasAll(['id', 'nome', 'especialidade', 'cidade_id', 'created_at', 'updated_at']);

            $json->whereAllType([
                'id' => 'integer',
                'nome' => 'string',
                'especialidade' => 'string',
                'cidade_id' => 'integer',
            ]);

            $json->whereAll([
                'nome' => $medico['nome'],
                'especialidade' => $medico['especialidade'],
                'cidade_id' => $medico['cidade_id'],
            ]);
        });
    }

    public function test_medicos_store_endpoint_without_token(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $response = $this->postJson('/api/medicos', $medico);

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_medicos_store_endpoint_nome_required(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $medico['nome'] = null;

        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The nome field is required.',
                'errors' => [
                    'nome' => [
                        'The nome field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_store_endpoint_nome_string(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $medico['nome'] = 123;

        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The nome field must be a string.',
                'errors' => [
                    'nome' => [
                        'The nome field must be a string.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_store_endpoint_especialidade_required(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $medico['especialidade'] = null;

        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The especialidade field is required.',
                'errors' => [
                    'especialidade' => [
                        'The especialidade field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_store_endpoint_especialidade_string(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $medico['especialidade'] = 123;

        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The especialidade field must be a string.',
                'errors' => [
                    'especialidade' => [
                        'The especialidade field must be a string.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_store_endpoint_cidade_required(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $medico['cidade_id'] = null;

        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cidade id field is required.',
                'errors' => [
                    'cidade_id' => [
                        'The cidade id field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_store_endpoint_cidade_integer(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $medico['cidade_id'] = 'asdf';

        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cidade id field must be an integer.',
                'errors' => [
                    'cidade_id' => [
                        'The cidade id field must be an integer.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_store_endpoint_cidade_exists(): void
    {
        $medico = Medico::factory()->makeOne()->toArray();

        $medico['cidade_id'] = 999;

        $response = $this->postJson('/api/medicos', $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The selected cidade id is invalid.',
                'errors' => [
                    'cidade_id' => [
                        'The selected cidade id is invalid.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_update_endpoint(): void
    {
        $medico = Medico::factory()->createOne();
        $response = $this->putJson('/api/medicos/' . $medico->id, $medico->toArray(), [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($medico) {
            $json->hasAll(['id', 'nome', 'especialidade', 'cidade_id', 'created_at', 'updated_at', 'deleted_at']);

            $json->whereAllType([
                'id' => 'integer',
                'nome' => 'string',
                'especialidade' => 'string',
                'cidade_id' => 'integer',
            ]);

            $json->whereAll([
                'id' => $medico->id,
                'nome' => $medico->nome,
                'especialidade' => $medico->especialidade,
                'cidade_id' => $medico->cidade_id,
            ]);
        });
    }

    public function test_medicos_update_endpoint_without_token(): void
    {
        $medico = Medico::factory()->createOne();

        $response = $this->putJson('/api/medicos/' . $medico->id, $medico->toArray());

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_medicos_update_endpoint_nome_required(): void
    {
        $medico = Medico::factory()->createOne();

        $medico = $medico->toArray();

        $medico['nome'] = null;

        $response = $this->putJson('/api/medicos/' . $medico['id'], $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The nome field is required.',
                'errors' => [
                    'nome' => [
                        'The nome field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_update_endpoint_nome_string(): void
    {
        $medico = Medico::factory()->createOne();

        $medico = $medico->toArray();

        $medico['nome'] = 123;

        $response = $this->putJson('/api/medicos/' . $medico['id'], $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The nome field must be a string.',
                'errors' => [
                    'nome' => [
                        'The nome field must be a string.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_update_endpoint_especialidade_required(): void
    {
        $medico = Medico::factory()->createOne();

        $medico = $medico->toArray();

        $medico['especialidade'] = null;

        $response = $this->putJson('/api/medicos/' . $medico['id'], $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The especialidade field is required.',
                'errors' => [
                    'especialidade' => [
                        'The especialidade field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_update_endpoint_especialidade_string(): void
    {
        $medico = Medico::factory()->createOne();

        $medico = $medico->toArray();

        $medico['especialidade'] = 123;

        $response = $this->putJson('/api/medicos/' . $medico['id'], $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The especialidade field must be a string.',
                'errors' => [
                    'especialidade' => [
                        'The especialidade field must be a string.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_update_endpoint_cidade_required(): void
    {
        $medico = Medico::factory()->createOne();

        $medico = $medico->toArray();

        $medico['cidade_id'] = null;

        $response = $this->putJson('/api/medicos/' . $medico['id'], $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cidade id field is required.',
                'errors' => [
                    'cidade_id' => [
                        'The cidade id field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_update_endpoint_cidade_integer(): void
    {
        $medico = Medico::factory()->createOne();

        $medico = $medico->toArray();

        $medico['cidade_id'] = 'asdf';

        $response = $this->putJson('/api/medicos/' . $medico['id'], $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cidade id field must be an integer.',
                'errors' => [
                    'cidade_id' => [
                        'The cidade id field must be an integer.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_update_endpoint_cidade_exists(): void
    {
        $medico = Medico::factory()->createOne();

        $medico = $medico->toArray();

        $medico['cidade_id'] = 999;

        $response = $this->putJson('/api/medicos/' . $medico['id'], $medico, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The selected cidade id is invalid.',
                'errors' => [
                    'cidade_id' => [
                        'The selected cidade id is invalid.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_destroy_endpoint(): void
    {
        $medico = Medico::factory()->createOne();
        $response = $this->deleteJson('/api/medicos/' . $medico->id, [], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(204);
    }

    public function test_medicos_destroy_endpoint_without_token(): void
    {
        $medico = Medico::factory()->createOne();

        $response = $this->deleteJson('/api/medicos/' . $medico->id);

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }
}
