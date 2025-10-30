<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apoderado;
use App\Http\Requests\StoreApoderadoRequest;
use App\Http\Requests\UpdateApoderadoRequest;

class ApoderadoController extends Controller
{
    /**
     * Listar todos los apoderados
     */
    public function index()
    {
        $apoderados = Apoderado::with('estudiantes:dni,nombres,apellidos,grado,seccion,apoderado_id')
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json([
            'apoderados' => $apoderados
        ], 200);
    }

    /**
     * Crear un nuevo apoderado
     */
    public function store(StoreApoderadoRequest $request)
    {
        $apoderado = Apoderado::create($request->validated());

        return response()->json([
            'message' => 'Apoderado registrado exitosamente',
            'apoderado' => $apoderado
        ], 201);
    }

    /**
     * Mostrar un apoderado específico
     */
    public function show(string $id)
    {
        $apoderado = Apoderado::with('estudiantes')->findOrFail($id);

        return response()->json([
            'apoderado' => $apoderado
        ], 200);
    }

    /**
     * Actualizar un apoderado
     */
    public function update(UpdateApoderadoRequest $request, string $id)
    {
        $apoderado = Apoderado::findOrFail($id);
        $apoderado->update($request->validated());

        return response()->json([
            'message' => 'Apoderado actualizado exitosamente',
            'apoderado' => $apoderado
        ], 200);
    }

    /**
     * Eliminar un apoderado
     */
    public function destroy(string $id)
    {
        $apoderado = Apoderado::findOrFail($id);

        if ($apoderado->estudiantes()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar. El apoderado tiene estudiantes asignados.'
            ], 400);
        }

        $apoderado->delete();

        return response()->json([
            'message' => 'Apoderado eliminado exitosamente'
        ], 200);
    }
}
