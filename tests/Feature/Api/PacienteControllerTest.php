<?php

namespace Tests\Feature\Api;

use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PacienteControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $cpfRegexPattern = '/^\d{3}\.\d{3}\.\d{3}-\d{2}$/';
    private $phoneRegexPattern = '/^\(\d{2}\) \d{4,5}-\d{4}$/';

    public function test_paciente_index_endpoint(): void
    {
        $pacientes = Paciente::factory(3)->create();
        $response = $this->getJson('/api/pacientes');

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

    public function test_paciente_show_endpoint(): void
    {
        $paciente = Paciente::factory()->createOne();

        $response = $this->getJson('/api/pacientes/' . $paciente->id);

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

    public function test_paciente_store_endpoint(): void
    {
        $request = Paciente::factory()->make();

        $response = $this->postJson('/api/pacientes', $request->toArray());

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

    public function test_paciente_update_endpoint(): void
    {
        $paciente = Paciente::factory()->createOne();

        $request = Paciente::factory()->make();

        $response = $this->putJson('/api/pacientes/' . $paciente->id, $request->toArray());

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

    public function test_paciente_delete_endpoint(): void
    {
        $paciente = Paciente::factory()->createOne();

        $response = $this->deleteJson('/api/pacientes/' . $paciente->id);

        $response->assertStatus(204);
    }
}
