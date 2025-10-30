<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Activar cuando se implemente autenticación
        // return auth()->check() && auth()->user()->rol === 'TOE';
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|string|max:100|regex:/^[\pL\s]+$/u',
            'email' => [
                'sometimes',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'sometimes|string|min:6',
            'rol' => 'sometimes|in:TOE,PSICOLOGO,TUTOR,DIRECTOR',
            'estado' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo debe contener letras y espacios.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
