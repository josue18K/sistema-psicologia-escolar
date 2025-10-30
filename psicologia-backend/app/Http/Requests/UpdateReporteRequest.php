<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReporteRequest extends FormRequest
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
            'mes' => 'sometimes|string|max:20',
            'total_estudiantes' => 'sometimes|integer|min:0',
            'casos_criticos' => 'sometimes|integer|min:0',
            'observaciones' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'total_estudiantes.min' => 'El total de estudiantes debe ser mayor o igual a 0.',
            'casos_criticos.min' => 'Los casos críticos deben ser mayor o igual a 0.',
        ];
    }
}
