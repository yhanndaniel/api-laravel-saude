<?php

namespace Tests\Feature\Api;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PacienteControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private string $cpfRegexPattern = '/^\d{3}\.\d{3}\.\d{3}-\d{2}$/';
    private string $phoneRegexPattern = '/^\(\d{2}\) \d{4,5}-\d{4}$/';
    private string $token;

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

    public function test_paciente_index_endpoint(): void
    {
        $pacientes = Paciente::factory(3)->create();
        $response = $this->getJson('/api/pacientes', [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJson(function (AssertableJson $json) use ($pacientes) {
            $json->hasAll(['0.id', '0.nome', '0.cpf', '0.celular']);

            $json->whereAllType([
                '0.id' => 'integer',
                '0.nome' => 'string',
                '0.cpf' => 'string',
                '0.celular' => 'string',
            ]);

            $paciente = $pacientes->first();

            $json->whereAll([
                '0.id' => $paciente->id,
                '0.nome' => $paciente->nome,
                '0.cpf' => $paciente->cpf,
                '0.celular' => $paciente->celular,
            ]);

            $this->assertMatchesRegularExpression($this->cpfRegexPattern, $paciente->cpf);

            $this->assertMatchesRegularExpression($this->phoneRegexPattern, $paciente->celular);
        });

    }

    public function test_paciente_index_endpoint_without_token(): void
    {
        $response = $this->getJson('/api/pacientes');

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_paciente_show_endpoint(): void
    {
        $paciente = Paciente::factory()->createOne();

        $response = $this->getJson('/api/pacientes/' . $paciente->id, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($paciente) {
            $json->hasAll(['id', 'nome', 'cpf', 'celular', 'created_at', 'updated_at', 'deleted_at']);

            $json->whereAllType([
                'id' => 'integer',
                'nome' => 'string',
                'cpf' => 'string',
                'celular' => 'string',
            ]);

            $json->whereAll([
                'id' => $paciente->id,
                'nome' => $paciente->nome,
                'cpf' => $paciente->cpf,
                'celular' => $paciente->celular,
            ]);

            $this->assertMatchesRegularExpression($this->cpfRegexPattern, $paciente->cpf);

            $this->assertMatchesRegularExpression($this->phoneRegexPattern, $paciente->celular);
        });
    }

    public function test_paciente_show_endpoint_without_token(): void
    {
        $response = $this->getJson('/api/pacientes/1');

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_paciente_store_endpoint(): void
    {
        $request = Paciente::factory()->make();

        $response = $this->postJson('/api/pacientes', $request->toArray(), [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($request, $response) {
            $json->hasAll(['id', 'nome', 'cpf', 'celular', 'created_at', 'updated_at']);

            $json->whereAllType([
                'id' => 'integer',
                'nome' => 'string',
                'cpf' => 'string',
                'celular' => 'string',
            ]);

            $json->whereAll([
                'nome' => $request->nome,
                'cpf' => $request->cpf,
                'celular' => $request->celular,
            ]);

            $this->assertMatchesRegularExpression($this->cpfRegexPattern, $request->cpf);

            $this->assertMatchesRegularExpression($this->phoneRegexPattern, $request->celular);

            //Expected Data base sava data without format
            $pacienteDataDB = DB::select('SELECT * FROM paciente WHERE id = ?', [$response->json('id')])[0];
            $this->assertEquals(formatOnlyNumber($request->cpf), $pacienteDataDB->cpf);
            $this->assertEquals(formatOnlyNumber($request->celular), $pacienteDataDB->celular);
        });
    }

    public function test_paciente_store_endpoint_without_token(): void
    {
        $request = Paciente::factory()->make();

        $response = $this->postJson('/api/pacientes', $request->toArray());

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_paciente_store_endpoint_nome_required(): void
    {
        $request = Paciente::factory()->make(['nome' => null]);

        $response = $this->postJson('/api/pacientes', $request->toArray(), [
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

    public function test_paciente_store_endpoint_nome_string(): void
    {
        $request = Paciente::factory()->make(['nome' => 12354]);

        $response = $this->postJson('/api/pacientes', $request->toArray(), [
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

    public function test_paciente_store_endpoint_cpf_required(): void
    {
        $request = [
            'nome' => $this->faker->name,
            'cpf' => null,
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->postJson('/api/pacientes', $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cpf field is required. (and 1 more error)',
                'errors' => [
                    'cpf' => [
                        'The cpf field is required.',
                    ],
                    'cpfWithoutFormat' => [
                        'The cpf without format field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_store_endpoint_cpf_unique(): void
    {
        $request = [
            'nome' => $this->faker->name,
            'cpf' => $this->faker->cpf,
            'celular' => $this->faker->phoneNumber
        ];

        $paciente1 = Paciente::factory($request)->createOne();

        $response = $this->postJson('/api/pacientes', $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cpf without format has already been taken.',
                'errors' => [
                    'cpfWithoutFormat' => [
                        'The cpf without format has already been taken.',
                    ]
                ]
            ]);
        });


    }

    public function test_paciente_store_endpoint_cpf_format(): void
    {
        $request = [
            'nome' => $this->faker->name,
            'cpf' => '044889741-55',
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->postJson('/api/pacientes', $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'O campo cpf não possui o formato válido de CPF.',
                'errors' => [
                    'cpf' => [
                        'O campo cpf não possui o formato válido de CPF.',
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_store_endpoint_cpf_valid(): void
    {
        $request = [
            'nome' => $this->faker->name,
            'cpf' => '000.000.000-00',
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->postJson('/api/pacientes', $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'O campo cpf não é um CPF válido.',
                'errors' => [
                    'cpf' => [
                        'O campo cpf não é um CPF válido.',
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_store_endpoint_celular_required(): void
    {
        $request = [
            'nome' => $this->faker->name,
            'cpf' => $this->faker->cpf,
            'celular' => null
        ];

        $response = $this->postJson('/api/pacientes', $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The celular field is required.',
                'errors' => [
                    'celular' => [
                        'The celular field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_store_endpoint_celular_format(): void
    {
        $request = [
            'nome' => $this->faker->name,
            'cpf' => $this->faker->cpf,
            'celular' => '00000000000'
        ];

        $response = $this->postJson('/api/pacientes', $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'O campo celular não é um celular com DDD válido.',
                'errors' => [
                    'celular' => [
                        'O campo celular não é um celular com DDD válido.',
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_update_endpoint(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = Paciente::factory()->make();

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request->toArray(), [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($request, $response, $paciente) {
            $json->hasAll(['id', 'nome', 'cpf', 'celular', 'created_at', 'updated_at', 'deleted_at']);

            $json->whereAllType([
                'id' => 'integer',
                'nome' => 'string',
                'cpf' => 'string',
                'celular' => 'string',
            ]);

            $json->whereAll([
                'id' => $paciente->id,
                'nome' => $request->nome,
                'cpf' => $request->cpf,
                'celular' => $request->celular,
            ]);

            $this->assertMatchesRegularExpression($this->cpfRegexPattern, $request->cpf);

            $this->assertMatchesRegularExpression($this->phoneRegexPattern, $request->celular);

            //Expected Data base sava data without format
            $pacienteDataDB = DB::select('SELECT * FROM paciente WHERE id = ?', [$response->json('id')])[0];
            $this->assertEquals(formatOnlyNumber($request->cpf), $pacienteDataDB->cpf);
            $this->assertEquals(formatOnlyNumber($request->celular), $pacienteDataDB->celular);
        });
    }

    public function test_paciente_update_endpoint_without_token(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = Paciente::factory()->make();

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request->toArray());

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_paciente_update_endpoint_nome_required(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = [
            'nome' => null,
            'cpf' => $this->faker->cpf,
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request, [
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

    public function test_paciente_update_endpoint_nome_string(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = [
            'nome' => 12354,
            'cpf' => $this->faker->cpf,
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request, [
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

    public function test_paciente_update_endpoint_cpf_required(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = [
            'nome' => $this->faker->name,
            'cpf' => null,
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cpf field is required. (and 1 more error)',
                'errors' => [
                    'cpf' => [
                        'The cpf field is required.',
                    ],
                    'cpfWithoutFormat' => [
                        'The cpf without format field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_update_endpoint_cpf_unique_owner_cpf(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = [
            'nome' => $this->faker->name,
            'cpf' => $paciente->cpf,
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);

    }

    public function test_paciente_update_endpoint_cpf_unique(): void
    {
        $paciente1 = Paciente::factory()->createOne();
        $paciente2 = Paciente::factory()->createOne();

        $request = [
            'nome' => $this->faker->name,
            'cpf' => $paciente2->cpf,
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente1->id, $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The cpf without format has already been taken.',
                'errors' => [
                    'cpfWithoutFormat' => [
                        'The cpf without format has already been taken.'
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_update_endpoint_cpf_format(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = [
            'nome' => $this->faker->name,
            'cpf' => '044889741-55',
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'O campo cpf não possui o formato válido de CPF.',
                'errors' => [
                    'cpf' => [
                        'O campo cpf não possui o formato válido de CPF.'
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_update_endpoint_cpf_valid(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = [
            'nome' => $this->faker->name,
            'cpf' => '000.000.000-00',
            'celular' => $this->faker->phoneNumber
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'O campo cpf não é um CPF válido.',

                'errors' => [
                    'cpf' => [
                        'O campo cpf não é um CPF válido.',
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_update_endpoint_celular_required(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = [
            'nome' => $this->faker->name,
            'cpf' => $this->faker->cpf,
            'celular' => null
        ];

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request, [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The celular field is required.',
                'errors' => [
                    'celular' => [
                        'The celular field is required.'
                    ]
                ]
            ]);
        });
    }

    public function test_paciente_delete_endpoint(): void
    {
        $paciente = Paciente::factory()->createOne();

        $response = $this->deleteJson('/api/pacientes/' . $paciente->id, [], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(204);
    }

    public function test_paciente_delete_endpoint_without_token(): void
    {
        $paciente = Paciente::factory()->createOne();

        $response = $this->deleteJson('/api/pacientes/' . $paciente->id);

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }


}
