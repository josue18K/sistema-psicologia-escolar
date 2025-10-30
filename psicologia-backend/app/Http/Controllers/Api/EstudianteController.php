<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Apoderado;
use App\Http\Requests\StoreEstudianteRequest;
use App\Http\Requests\UpdateEstudianteRequest;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    /**
     * Listar todos los estudiantes
     */
    public function index()
    {
        $estudiantes = Estudiante::with(['tutor:id,name', 'apoderado'])
            ->orderBy('apellidos', 'asc')
            ->get();

        return response()->json([
            'estudiantes' => $estudiantes
        ], 200);
    }

    /**
     * Crear un nuevo estudiante
     */
    public function store(StoreEstudianteRequest $request)
    {
        $validated = $request->validated();

        // Si se debe crear un nuevo apoderado
        $apoderado_id = null;

        if (isset($validated['crear_apoderado']) && $validated['crear_apoderado']) {
            $apoderado = Apoderado::create([
                'nombre' => $validated['apoderado_nombre'],
                'parentesco' => $validated['apoderado_parentesco'],
                'correo' => $validated['apoderado_correo'] ?? null,
                'telefono' => $validated['apoderado_telefono'],
            ]);

            $apoderado_id = $apoderado->id;
        } else {
            $apoderado_id = $validated['apoderado_id'] ?? null;
        }

        $estudiante = Estudiante::create([
            'dni' => $validated['dni'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'edad' => $validated['edad'],
            'grado' => $validated['grado'],
            'seccion' => $validated['seccion'],
            'tutor_id' => $validated['tutor_id'] ?? null,
            'apoderado_id' => $apoderado_id,
        ]);

        return response()->json([
            'message' => 'Estudiante registrado exitosamente',
            'estudiante' => $estudiante->load(['tutor', 'apoderado'])
        ], 201);
    }

    /**
     * Mostrar un estudiante específico
     */
    public function show(string $dni)
    {
        $estudiante = Estudiante::with([
            'tutor:id,name,email',
            'apoderado',
            'diagnosticos.psicologo:id,name',
            'citas'
        ])->findOrFail($dni);

        return response()->json([
            'estudiante' => $estudiante
        ], 200);
    }

    /**
     * Actualizar un estudiante
     */
    public function update(UpdateEstudianteRequest $request, string $dni)
    {
        $estudiante = Estudiante::findOrFail($dni);
        $estudiante->update($request->validated());

        return response()->json([
            'message' => 'Estudiante actualizado exitosamente',
            'estudiante' => $estudiante->load(['tutor', 'apoderado'])
        ], 200);
    }

    /**
     * Eliminar un estudiante
     */
    public function destroy(string $dni)
    {
        $estudiante = Estudiante::findOrFail($dni);
        $estudiante->delete();

        return response()->json([
            'message' => 'Estudiante eliminado exitosamente'
        ], 200);
    }

    /**
     * Buscar estudiantes
     */
    public function search(Request $request)
    {
        $query = Estudiante::with(['tutor:id,name', 'apoderado']);

        if ($request->has('dni')) {
            $query->where('dni', 'like', '%' . $request->dni . '%');
        }

        if ($request->has('nombre')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombres', 'like', '%' . $request->nombre . '%')
                    ->orWhere('apellidos', 'like', '%' . $request->nombre . '%');
            });
        }

        if ($request->has('grado')) {
            $query->where('grado', $request->grado);
        }

        if ($request->has('seccion')) {
            $query->where('seccion', $request->seccion);
        }

        $estudiantes = $query->get();

        return response()->json([
            'estudiantes' => $estudiantes
        ], 200);
    }
}
