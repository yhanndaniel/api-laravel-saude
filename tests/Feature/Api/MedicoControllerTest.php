<?php

namespace Tests\Feature\Api;

use App\Models\Medico;
use App\Models\Paciente;
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

    public function test_medicos_pacientes_endpoint(): void
    {
        $medico = Medico::factory()->createOne();
        $pacientes = Paciente::factory(3)->create();

        foreach ($pacientes as $paciente) {
            $medico->pacientes()->attach($paciente->id);
        }

        $response = $this->getJson('/api/medicos/' . $medico->id . '/pacientes', [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $json) use ($pacientes) {
            $json->hasAll(['0.id', '0.nome', '0.cpf', '0.celular','0.created_at', '0.updated_at', '0.deleted_at']);

            $json->whereAll([
                '0.id' => $pacientes[0]->id,
                '0.nome' => $pacientes[0]->nome,
                '0.cpf' => $pacientes[0]->cpf,
                '0.celular' => $pacientes[0]->celular,
                '0.created_at' => $pacientes[0]->created_at->jsonSerialize(),
                '0.updated_at' => $pacientes[0]->updated_at->jsonSerialize(),
            ]);
        });

    }

    public function test_medicos_add_pacientes_endpoint(): void
    {
        $medico = Medico::factory()->createOne();
        $paciente = Paciente::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes', [
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($medico, $paciente) {
            $json->hasAll(['id', 'nome', 'especialidade', 'cidade_id', 'created_at', 'updated_at', 'deleted_at', 'pacientes']);

            $json->whereAll([
                'id' => $medico->id,
                'nome' => $medico->nome,
                'especialidade' => $medico->especialidade,
                'cidade_id' => $medico->cidade_id,
                'created_at' => $medico->created_at->jsonSerialize(),
                'updated_at' => $medico->updated_at->jsonSerialize(),
                'deleted_at' => $medico->deleted_at,
                'pacientes' => [
                    0 => [
                        'id' => $paciente->id,
                        'nome' => $paciente->nome,
                        'cpf' => $paciente->cpf,
                        'celular' => $paciente->celular,
                        'created_at' => $paciente->created_at->jsonSerialize(),
                        'updated_at' => $paciente->updated_at->jsonSerialize(),
                        'deleted_at' => $paciente->deleted_at,
                        'pivot' => [
                            'medico_id' => $medico->id,
                            'paciente_id' => $paciente->id,
                            'created_at' => $paciente->created_at->jsonSerialize(),
                            'updated_at' => $paciente->updated_at->jsonSerialize(),
                        ]
                    ]
                ]
            ])->etc();
        });

    }

    public function test_medicos_add_pacientes_endpoint_without_token(): void
    {
        $medico = Medico::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes');

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_medicos_add_pacientes_endpoint_medico_id_required(): void
    {
        $medico = Medico::factory()->createOne();

        $paciente = Paciente::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes', [
            'paciente_id' => $paciente->id,
            'medico_id' => null
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The medico id field is required.',
                'errors' => [
                    'medico_id' => [
                        'The medico id field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_add_pacientes_endpoint_medico_id_integer(): void
    {
        $medico = Medico::factory()->createOne();
        $paciente = Paciente::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes', [
            'medico_id' => 'asdf',
            'paciente_id' => $paciente->id,
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The medico id field must be an integer.',
                'errors' => [
                    'medico_id' => [
                        'The medico id field must be an integer.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_add_pacientes_endpoint_medico_id_exists(): void
    {
        $medico = Medico::factory()->createOne();
        $paciente = Paciente::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes', [
            'medico_id' => 999,
            'paciente_id' => $paciente->id,
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The selected medico id is invalid.',
                'errors' => [
                    'medico_id' => [
                        'The selected medico id is invalid.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_add_pacientes_endpoint_paciente_id_required(): void
    {
        $medico = Medico::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes', [
            'medico_id' => $medico->id,
            'paciente_id' => null,
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The paciente id field is required.',
                'errors' => [
                    'paciente_id' => [
                        'The paciente id field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_add_pacientes_endpoint_paciente_id_integer(): void
    {
        $medico = Medico::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes', [
            'medico_id' => $medico->id,
            'paciente_id' => 'asdf',
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The paciente id field must be an integer.',
                'errors' => [
                    'paciente_id' => [
                        'The paciente id field must be an integer.',
                    ]
                ]
            ]);
        });
    }

    public function test_medicos_add_pacientes_endpoint_paciente_id_exists(): void
    {
        $medico = Medico::factory()->createOne();

        $response = $this->postJson('/api/medicos/' . $medico->id . '/pacientes', [
            'medico_id' => $medico->id,
            'paciente_id' => 999,
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The selected paciente id is invalid.',
                'errors' => [
                    'paciente_id' => [
                        'The selected paciente id is invalid.',
                    ]
                ]
            ]);
        });
    }
}
