<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
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
            'fecha_cita' => 'sometimes|date',
            'motivo' => 'sometimes|string|max:500',
            'estado' => 'sometimes|in:PENDIENTE,REALIZADA,CANCELADA',
            'correo_enviado' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'estado.in' => 'El estado debe ser PENDIENTE, REALIZADA o CANCELADA.',
        ];
    }
}
