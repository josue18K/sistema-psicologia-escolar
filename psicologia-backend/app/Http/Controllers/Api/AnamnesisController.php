<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anamnesis;
use App\Http\Requests\StoreAnamnesisRequest;
use App\Http\Requests\UpdateAnamnesisRequest;
use Illuminate\Http\Request;

class AnamnesisController extends Controller
{
    /**
     * Listar todas las anamnesis
     */
    public function index()
    {
        $anamnesis = Anamnesis::with([
            'estudiante:dni,nombres,apellidos,grado,seccion',
            'psicologo:id,name'
        ])
            ->orderBy('fecha_evaluacion', 'desc')
            ->get();

        return response()->json([
            'anamnesis' => $anamnesis
        ], 200);
    }

    /**
     * Crear una nueva anamnesis
     */
    public function store(StoreAnamnesisRequest $request)
    {
        $anamnesis = Anamnesis::create($request->validated());

        return response()->json([
            'message' => 'Anamnesis registrada exitosamente',
            'anamnesis' => $anamnesis->load(['estudiante', 'psicologo'])
        ], 201);
    }

    /**
     * Mostrar una anamnesis específica
     */
    public function show(string $id)
    {
        $anamnesis = Anamnesis::with([
            'estudiante',
            'psicologo:id,name,email'
        ])->findOrFail($id);

        return response()->json([
            'anamnesis' => $anamnesis
        ], 200);
    }

    /**
     * Actualizar una anamnesis
     */
    public function update(UpdateAnamnesisRequest $request, string $id)
    {
        $anamnesis = Anamnesis::findOrFail($id);
        $anamnesis->update($request->validated());

        return response()->json([
            'message' => 'Anamnesis actualizada exitosamente',
            'anamnesis' => $anamnesis->load(['estudiante', 'psicologo'])
        ], 200);
    }

    /**
     * Eliminar una anamnesis
     */
    public function destroy(string $id)
    {
        $anamnesis = Anamnesis::findOrFail($id);
        $anamnesis->delete();

        return response()->json([
            'message' => 'Anamnesis eliminada exitosamente'
        ], 200);
    }

    /**
     * Obtener anamnesis por estudiante
     */
    public function byEstudiante(string $dni)
    {
        $anamnesis = Anamnesis::with('psicologo:id,name')
            ->where('dni_estudiante', $dni)
            ->orderBy('fecha_evaluacion', 'desc')
            ->get();

        return response()->json([
            'anamnesis' => $anamnesis
        ], 200);
    }

    /**
     * Verificar si un estudiante ya tiene anamnesis
     */
    public function verificarExistencia(string $dni)
    {
        $existe = Anamnesis::where('dni_estudiante', $dni)->exists();
        $anamnesis = null;

        if ($existe) {
            $anamnesis = Anamnesis::with('psicologo:id,name')
                ->where('dni_estudiante', $dni)
                ->latest('fecha_evaluacion')
                ->first();
        }

        return response()->json([
            'existe' => $existe,
            'anamnesis' => $anamnesis
        ], 200);
    }
}
