<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnamnesisRequest extends FormRequest
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
            'dni_estudiante' => 'required|string|size:8|exists:estudiantes,dni',
            'psicologo_id' => 'required|exists:users,id',
            'fecha_evaluacion' => 'required|date|before_or_equal:today',

            // Datos familiares
            'numero_hijos' => 'nullable|integer|min:1|max:20',
            'personas_vive_con' => 'nullable|string|max:200',
            'lugar_ocupa' => 'nullable|integer|min:1|max:20',

            // Padre
            'padre_nombre' => 'nullable|string|max:100',
            'padre_edad' => 'nullable|integer|min:18|max:100',
            'padre_escolaridad' => 'nullable|string|max:100',
            'padre_ocupacion' => 'nullable|string|max:100',

            // Madre
            'madre_nombre' => 'nullable|string|max:100',
            'madre_edad' => 'nullable|integer|min:18|max:100',
            'madre_escolaridad' => 'nullable|string|max:100',
            'madre_ocupacion' => 'nullable|string|max:100',

            // Relaciones familiares
            'relacion_madre' => 'nullable|in:EXCELENTE,BUENA,REGULAR,MALA',
            'relacion_padre' => 'nullable|in:EXCELENTE,BUENA,REGULAR,MALA',
            'relacion_hermanos' => 'nullable|in:EXCELENTE,BUENA,REGULAR,MALA',
            'relacion_otros_familiares' => 'nullable|in:EXCELENTE,BUENA,REGULAR,MALA',

            // Antecedentes escolares
            'inicial_edad_ingreso' => 'nullable|string|max:50',
            'primaria_edad_ingreso' => 'nullable|string|max:50',
            'secundaria_edad_ingreso' => 'nullable|string|max:50',

            // Antecedentes prenatales
            'madre_enfermedades_embarazo' => 'nullable|string|max:1000',
            'madre_medicamentos_embarazo' => 'nullable|string|max:1000',

            // Parto
            'sintomas_perdida' => 'nullable|string|max:100',
            'estado_nutricional' => 'nullable|string|max:100',
            'estado_emocional' => 'nullable|string|max:100',
            'parto_semanas' => 'nullable|integer|min:20|max:45',
            'parto_peso' => 'nullable|numeric|min:0.5|max:10',
            'parto_talla' => 'nullable|numeric|min:20|max:70',
            'parto_apgar' => 'nullable|integer|min:0|max:10',
            'tratamientos_parto' => 'nullable|string|max:1000',
            'enfermedades_importantes' => 'nullable|string|max:1000',

            // Desarrollo psicomotor (en meses)
            'edad_controlo_cabeza' => 'nullable|integer|min:0|max:24',
            'edad_se_sento' => 'nullable|integer|min:0|max:24',
            'edad_se_paro' => 'nullable|integer|min:0|max:36',
            'edad_camino' => 'nullable|integer|min:0|max:36',
            'edad_controlo_esfinter' => 'nullable|integer|min:0|max:60',
            'edad_durmio_solo' => 'nullable|integer|min:0|max:120',

            // Desarrollo del lenguaje (en meses)
            'edad_primeras_palabras' => 'nullable|integer|min:0|max:36',
            'edad_oraciones' => 'nullable|integer|min:0|max:60',
            'edad_comprendio_instrucciones' => 'nullable|integer|min:0|max:60',

            // Evaluaciones anteriores
            'evaluacion_neurologica' => 'nullable|boolean',
            'evaluacion_neurologica_fecha' => 'nullable|date|before_or_equal:today',
            'evaluacion_psicologica' => 'nullable|boolean',
            'evaluacion_psicologica_fecha' => 'nullable|date|before_or_equal:today',
            'evaluacion_psiquiatrica' => 'nullable|boolean',
            'evaluacion_psiquiatrica_fecha' => 'nullable|date|before_or_equal:today',
            'evaluacion_psicopedagogica' => 'nullable|boolean',
            'evaluacion_psicopedagogica_fecha' => 'nullable|date|before_or_equal:today',

            // Hábitos
            'problemas_dormir' => 'nullable|boolean',
            'horas_sueno' => 'nullable|integer|min:0|max:24',
            'habitos_estudio' => 'nullable|boolean',
            'cuales_habitos_estudio' => 'nullable|string|max:1000',
            'actividades_intereses' => 'nullable|string|max:1000',

            // Otros
            'autoestima_tolerancia_frustracion' => 'nullable|string|max:2000',
            'observaciones' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'dni_estudiante.required' => 'Debe seleccionar un estudiante.',
            'dni_estudiante.exists' => 'El estudiante seleccionado no existe.',
            'psicologo_id.required' => 'El psicólogo es obligatorio.',
            'fecha_evaluacion.required' => 'La fecha de evaluación es obligatoria.',
            'fecha_evaluacion.before_or_equal' => 'La fecha de evaluación no puede ser futura.',
            'parto_apgar.max' => 'El puntaje Apgar máximo es 10.',
            'parto_semanas.min' => 'Las semanas de gestación deben ser al menos 20.',
        ];
    }
}
