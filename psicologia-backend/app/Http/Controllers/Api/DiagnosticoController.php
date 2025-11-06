<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diagnostico;
use App\Http\Requests\StoreDiagnosticoRequest;
use App\Http\Requests\UpdateDiagnosticoRequest;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    /**
     * Listar todos los diagnósticos
     */
    public function index(Request $request)
    {
        $query = Diagnostico::with([
            'estudiante:dni,nombres,apellidos,grado,seccion',
            'psicologo:id,name'
        ]);

        if ($request->has('dni_estudiante')) {
            $query->where('dni_estudiante', $request->dni_estudiante);
        }

        if ($request->has('nivel_riesgo')) {
            $query->where('nivel_riesgo', $request->nivel_riesgo);
        }

        $diagnosticos = $query->orderBy('fecha', 'desc')->get();

        return response()->json([
            'diagnosticos' => $diagnosticos
        ], 200);
    }

    /**
     * Crear un nuevo diagnóstico
     */
    public function store(StoreDiagnosticoRequest $request)
    {
        $diagnostico = Diagnostico::create($request->validated());

        return response()->json([
            'message' => 'Diagnóstico registrado exitosamente',
            'diagnostico' => $diagnostico->load(['estudiante', 'psicologo'])
        ], 201);
    }

    /**
     * Mostrar un diagnóstico específico
     */
    public function show(string $id)
    {
        $diagnostico = Diagnostico::with([
            'estudiante',
            'psicologo:id,name,email'
        ])->findOrFail($id);

        return response()->json([
            'diagnostico' => $diagnostico
        ], 200);
    }

    /**
     * Actualizar un diagnóstico
     */
    public function update(UpdateDiagnosticoRequest $request, string $id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $diagnostico->update($request->validated());

        return response()->json([
            'message' => 'Diagnóstico actualizado exitosamente',
            'diagnostico' => $diagnostico->load(['estudiante', 'psicologo'])
        ], 200);
    }

    /**
     * Eliminar un diagnóstico
     */
    public function destroy(string $id)
    {
        $diagnostico = Diagnostico::findOrFail($id);
        $diagnostico->delete();

        return response()->json([
            'message' => 'Diagnóstico eliminado exitosamente'
        ], 200);
    }

    /**
     * Obtener diagnósticos por estudiante
     */
    public function byEstudiante(string $dni)
    {
        $diagnosticos = Diagnostico::with('psicologo:id,name')
            ->where('dni_estudiante', $dni)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json([
            'diagnosticos' => $diagnosticos
        ], 200);
    }

    /**
     * Estadísticas de diagnósticos
     */
    public function estadisticas()
    {
        $total = Diagnostico::count();
        $porNivel = Diagnostico::selectRaw('nivel_riesgo, COUNT(*) as total')
            ->groupBy('nivel_riesgo')
            ->get();

        $porTipo = Diagnostico::selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'total' => $total,
            'por_nivel_riesgo' => $porNivel,
            'por_tipo' => $porTipo
        ], 200);
    }
}