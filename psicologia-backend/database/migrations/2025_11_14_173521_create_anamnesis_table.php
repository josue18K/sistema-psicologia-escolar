<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anamnesis', function (Blueprint $table) {
            $table->id();
            $table->string('dni_estudiante', 8);
            $table->foreignId('psicologo_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_evaluacion');

            // DATOS FAMILIARES
            $table->unsignedTinyInteger('numero_hijos')->nullable();
            $table->string('personas_vive_con', 200)->nullable();
            $table->unsignedTinyInteger('lugar_ocupa')->nullable(); // Orden de nacimiento

            // DATOS DEL PADRE
            $table->string('padre_nombre', 100)->nullable();
            $table->unsignedTinyInteger('padre_edad')->nullable();
            $table->string('padre_escolaridad', 100)->nullable();
            $table->string('padre_ocupacion', 100)->nullable();

            // DATOS DE LA MADRE
            $table->string('madre_nombre', 100)->nullable();
            $table->unsignedTinyInteger('madre_edad')->nullable();
            $table->string('madre_escolaridad', 100)->nullable();
            $table->string('madre_ocupacion', 100)->nullable();

            // RELACIONES FAMILIARES (EXCELENTE, BUENA, REGULAR, MALA)
            $table->enum('relacion_madre', ['EXCELENTE', 'BUENA', 'REGULAR', 'MALA'])->nullable();
            $table->enum('relacion_padre', ['EXCELENTE', 'BUENA', 'REGULAR', 'MALA'])->nullable();
            $table->enum('relacion_hermanos', ['EXCELENTE', 'BUENA', 'REGULAR', 'MALA'])->nullable();
            $table->enum('relacion_otros_familiares', ['EXCELENTE', 'BUENA', 'REGULAR', 'MALA'])->nullable();

            // ANTECEDENTES ESCOLARES
            $table->string('inicial_edad_ingreso', 50)->nullable();
            $table->string('primaria_edad_ingreso', 50)->nullable();
            $table->string('secundaria_edad_ingreso', 50)->nullable();

            // ANTECEDENTES PRENATALES
            $table->text('madre_enfermedades_embarazo')->nullable();
            $table->text('madre_medicamentos_embarazo')->nullable();

            // DATOS DEL PARTO
            $table->string('sintomas_perdida', 100)->nullable();
            $table->string('estado_nutricional', 100)->nullable();
            $table->string('estado_emocional', 100)->nullable();
            $table->unsignedTinyInteger('parto_semanas')->nullable();
            $table->decimal('parto_peso', 5, 2)->nullable(); // kg
            $table->decimal('parto_talla', 5, 2)->nullable(); // cm
            $table->unsignedTinyInteger('parto_apgar')->nullable();
            $table->text('tratamientos_parto')->nullable();
            $table->text('enfermedades_importantes')->nullable();

            // DESARROLLO PSICOMOTOR (edades en meses)
            $table->unsignedTinyInteger('edad_controlo_cabeza')->nullable();
            $table->unsignedTinyInteger('edad_se_sento')->nullable();
            $table->unsignedTinyInteger('edad_se_paro')->nullable();
            $table->unsignedTinyInteger('edad_camino')->nullable();
            $table->unsignedTinyInteger('edad_controlo_esfinter')->nullable();
            $table->unsignedTinyInteger('edad_durmio_solo')->nullable();

            // DESARROLLO DEL LENGUAJE (edades en meses)
            $table->unsignedTinyInteger('edad_primeras_palabras')->nullable();
            $table->unsignedTinyInteger('edad_oraciones')->nullable();
            $table->unsignedTinyInteger('edad_comprendio_instrucciones')->nullable();

            // EVALUACIONES ANTERIORES
            $table->boolean('evaluacion_neurologica')->default(false);
            $table->date('evaluacion_neurologica_fecha')->nullable();
            $table->boolean('evaluacion_psicologica')->default(false);
            $table->date('evaluacion_psicologica_fecha')->nullable();
            $table->boolean('evaluacion_psiquiatrica')->default(false);
            $table->date('evaluacion_psiquiatrica_fecha')->nullable();
            $table->boolean('evaluacion_psicopedagogica')->default(false);
            $table->date('evaluacion_psicopedagogica_fecha')->nullable();

            // HÁBITOS
            $table->boolean('problemas_dormir')->default(false);
            $table->unsignedTinyInteger('horas_sueno')->nullable();
            $table->boolean('habitos_estudio')->default(false);
            $table->text('cuales_habitos_estudio')->nullable();
            $table->text('actividades_intereses')->nullable();

            // OTROS
            $table->text('autoestima_tolerancia_frustracion')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('dni_estudiante')->references('dni')->on('estudiantes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anamnesis');
    }
};
