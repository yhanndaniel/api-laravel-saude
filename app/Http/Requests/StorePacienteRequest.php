<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'celular' => $this->celular,
            'cpfWithoutFormat' => formatOnlyNumber($this->cpf),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string'],
            'cpf' => ['required', 'formato_cpf', 'cpf'],
            'celular' => ['required', 'string', 'celular_com_ddd'],
            'cpfWithoutFormat' => ['required', 'unique:paciente,cpf']
        ];
    }
}
