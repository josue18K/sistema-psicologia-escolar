<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Activar cuando se implemente autenticación
        // return auth()->check();
        return true;
    }

    public function rules(): array
    {
        return [
            'destinatario_id' => 'required|exists:users,id',
            'mensaje' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'destinatario_id.required' => 'El destinatario es obligatorio.',
            'destinatario_id.exists' => 'El destinatario seleccionado no existe.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.max' => 'El mensaje no puede exceder 500 caracteres.',
        ];
    }
}
