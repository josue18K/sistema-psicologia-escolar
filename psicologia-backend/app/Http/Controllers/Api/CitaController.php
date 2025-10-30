<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /**
     * Listar todas las citas
     */
    public function index(Request $request)
    {
        $query = Cita::with('estudiante:dni,nombres,apellidos,grado,seccion');

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('dni_estudiante')) {
            $query->where('dni_estudiante', $request->dni_estudiante);
        }

        $citas = $query->orderBy('fecha_cita', 'asc')->get();

        return response()->json([
            'citas' => $citas
        ], 200);
    }

    /**
     * Crear una nueva cita
     */
    public function store(StoreCitaRequest $request)
    {
        $cita = Cita::create($request->validated());

        return response()->json([
            'message' => 'Cita registrada exitosamente',
            'cita' => $cita->load('estudiante')
        ], 201);
    }

    /**
     * Mostrar una cita específica
     */
    public function show(string $id)
    {
        $cita = Cita::with('estudiante')->findOrFail($id);

        return response()->json([
            'cita' => $cita
        ], 200);
    }

    /**
     * Actualizar una cita
     */
    public function update(UpdateCitaRequest $request, string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update($request->validated());

        return response()->json([
            'message' => 'Cita actualizada exitosamente',
            'cita' => $cita->load('estudiante')
        ], 200);
    }

    /**
     * Eliminar una cita
     */
    public function destroy(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->delete();

        return response()->json([
            'message' => 'Cita eliminada exitosamente'
        ], 200);
    }

    /**
     * Obtener citas pendientes
     */
    public function pendientes()
    {
        $citas = Cita::with('estudiante')
            ->where('estado', 'PENDIENTE')
            ->where('fecha_cita', '>=', now())
            ->orderBy('fecha_cita', 'asc')
            ->get();

        return response()->json([
            'citas' => $citas
        ], 200);
    }

    /**
     * Marcar cita como realizada
     */
    public function marcarRealizada(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update(['estado' => 'REALIZADA']);

        return response()->json([
            'message' => 'Cita marcada como realizada',
            'cita' => $cita
        ], 200);
    }
}
