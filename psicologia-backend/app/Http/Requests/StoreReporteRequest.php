<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReporteRequest extends FormRequest
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
            'psicologo_id' => 'required|exists:users,id',
            'mes' => 'required|string|max:20',
            'total_estudiantes' => 'required|integer|min:0',
            'casos_criticos' => 'required|integer|min:0',
            'observaciones' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'psicologo_id.required' => 'El psicólogo es obligatorio.',
            'mes.required' => 'El mes es obligatorio.',
            'total_estudiantes.required' => 'El total de estudiantes es obligatorio.',
            'total_estudiantes.min' => 'El total de estudiantes debe ser mayor o igual a 0.',
            'casos_criticos.required' => 'Los casos críticos son obligatorios.',
            'casos_criticos.min' => 'Los casos críticos deben ser mayor o igual a 0.',
        ];
    }
}
