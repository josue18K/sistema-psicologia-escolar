<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiagnosticoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Activar cuando se implemente autenticación
        // return auth()->check() && auth()->user()->rol === 'PSICOLOGO';
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => 'sometimes|date|before_or_equal:today',
            'tipo' => 'sometimes|string|max:100',
            'observaciones' => 'nullable|string|max:2000',
            'recomendaciones' => 'nullable|string|max:2000',
            'nivel_riesgo' => 'sometimes|in:BAJO,MEDIO,ALTO',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'nivel_riesgo.in' => 'El nivel de riesgo debe ser BAJO, MEDIO o ALTO.',
        ];
    }
}
