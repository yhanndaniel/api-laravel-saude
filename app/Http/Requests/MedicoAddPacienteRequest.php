<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicoAddPacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'medico_id' => ['required', 'integer', 'exists:medico,id'],
            'paciente_id' => ['required', 'integer', 'exists:paciente,id'],
        ];
    }
}
