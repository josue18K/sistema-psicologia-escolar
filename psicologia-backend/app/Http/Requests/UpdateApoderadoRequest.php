<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApoderadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: Activar cuando se implemente autenticación
        // return auth()->check() && in_array(auth()->user()->rol, ['TOE', 'PSICOLOGO']);
        return true;
    }

    public function rules(): array
    {
        $apoderadoId = $this->route('apoderado');

        return [
            'nombre' => 'sometimes|string|max:100|regex:/^[\pL\s]+$/u',
            'parentesco' => 'sometimes|string|in:Madre,Padre,Tutor Legal,Abuelo/a,Tío/a,Otro',
            'correo' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('apoderados', 'correo')->ignore($apoderadoId)
            ],
            'telefono' => 'sometimes|string|max:15|regex:/^[0-9\s]+$/',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.regex' => 'El nombre solo debe contener letras.',
            'parentesco.in' => 'El parentesco seleccionado no es válido.',
            'correo.email' => 'El correo debe ser válido.',
            'correo.unique' => 'Este correo ya está registrado.',
            'telefono.regex' => 'El teléfono solo debe contener números.',
        ];
    }
}
