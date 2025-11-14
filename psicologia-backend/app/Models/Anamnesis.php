<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anamnesis extends Model
{
    use HasFactory;

    protected $table = 'anamnesis';

    protected $fillable = [
        'dni_estudiante',
        'psicologo_id',
        'fecha_evaluacion',
        // Datos familiares
        'numero_hijos',
        'personas_vive_con',
        'lugar_ocupa',
        // Padre
        'padre_nombre',
        'padre_edad',
        'padre_escolaridad',
        'padre_ocupacion',
        // Madre
        'madre_nombre',
        'madre_edad',
        'madre_escolaridad',
        'madre_ocupacion',
        // Relaciones familiares
        'relacion_madre',
        'relacion_padre',
        'relacion_hermanos',
        'relacion_otros_familiares',
        // Antecedentes escolares
        'inicial_edad_ingreso',
        'primaria_edad_ingreso',
        'secundaria_edad_ingreso',
        // Antecedentes prenatales
        'madre_enfermedades_embarazo',
        'madre_medicamentos_embarazo',
        // Parto
        'sintomas_perdida',
        'estado_nutricional',
        'estado_emocional',
        'parto_semanas',
        'parto_peso',
        'parto_talla',
        'parto_apgar',
        'tratamientos_parto',
        'enfermedades_importantes',
        // Desarrollo psicomotor
        'edad_controlo_cabeza',
        'edad_se_sento',
        'edad_se_paro',
        'edad_camino',
        'edad_controlo_esfinter',
        'edad_durmio_solo',
        // Desarrollo del lenguaje
        'edad_primeras_palabras',
        'edad_oraciones',
        'edad_comprendio_instrucciones',
        // Evaluaciones anteriores
        'evaluacion_neurologica',
        'evaluacion_neurologica_fecha',
        'evaluacion_psicologica',
        'evaluacion_psicologica_fecha',
        'evaluacion_psiquiatrica',
        'evaluacion_psiquiatrica_fecha',
        'evaluacion_psicopedagogica',
        'evaluacion_psicopedagogica_fecha',
        // Hábitos
        'problemas_dormir',
        'horas_sueno',
        'habitos_estudio',
        'cuales_habitos_estudio',
        'actividades_intereses',
        // Otros
        'autoestima_tolerancia_frustracion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_evaluacion' => 'date',
        'evaluacion_neurologica' => 'boolean',
        'evaluacion_neurologica_fecha' => 'date',
        'evaluacion_psicologica' => 'boolean',
        'evaluacion_psicologica_fecha' => 'date',
        'evaluacion_psiquiatrica' => 'boolean',
        'evaluacion_psiquiatrica_fecha' => 'date',
        'evaluacion_psicopedagogica' => 'boolean',
        'evaluacion_psicopedagogica_fecha' => 'date',
        'problemas_dormir' => 'boolean',
        'habitos_estudio' => 'boolean',
        'parto_peso' => 'decimal:2',
        'parto_talla' => 'decimal:2',
    ];

    // Relaciones
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'dni_estudiante', 'dni');
    }

    public function psicologo()
    {
        return $this->belongsTo(User::class, 'psicologo_id');
    }
}
