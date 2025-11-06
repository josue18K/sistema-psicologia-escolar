<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use App\Models\Diagnostico;
use App\Http\Requests\StoreReporteRequest;
use App\Http\Requests\UpdateReporteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Listar todos los reportes
     */
    public function index()
    {
        $reportes = Reporte::with('psicologo:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'reportes' => $reportes
        ], 200);
    }

    /**
     * Crear un nuevo reporte
     */
    public function store(StoreReporteRequest $request)
    {
        $reporte = Reporte::create($request->validated());

        return response()->json([
            'message' => 'Reporte creado exitosamente',
            'reporte' => $reporte->load('psicologo')
        ], 201);
    }

    /**
     * Mostrar un reporte específico
     */
    public function show(string $id)
    {
        $reporte = Reporte::with('psicologo')->findOrFail($id);

        return response()->json([
            'reporte' => $reporte
        ], 200);
    }

    /**
     * Actualizar un reporte
     */
    public function update(UpdateReporteRequest $request, string $id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->update($request->validated());

        return response()->json([
            'message' => 'Reporte actualizado exitosamente',
            'reporte' => $reporte->load('psicologo')
        ], 200);
    }

    /**
     * Eliminar un reporte
     */
    public function destroy(string $id)
    {
        $reporte = Reporte::findOrFail($id);
        $reporte->delete();

        return response()->json([
            'message' => 'Reporte eliminado exitosamente'
        ], 200);
    }

    /**
     * Generar reporte automático del mes actual
     */
    public function generarMensual(Request $request)
    {
        $request->validate([
            'psicologo_id' => 'required|exists:users,id',
        ]);

        $mesActual = now()->format('F Y');
        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        $diagnosticosMes = Diagnostico::where('psicologo_id', $request->psicologo_id)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->get();

        $totalEstudiantes = $diagnosticosMes->unique('dni_estudiante')->count();
        $casosCriticos = $diagnosticosMes->where('nivel_riesgo', 'ALTO')->count();

        $reporte = Reporte::create([
            'psicologo_id' => $request->psicologo_id,
            'mes' => $mesActual,
            'total_estudiantes' => $totalEstudiantes,
            'casos_criticos' => $casosCriticos,
            'observaciones' => 'Reporte generado automáticamente',
        ]);

        return response()->json([
            'message' => 'Reporte mensual generado exitosamente',
            'reporte' => $reporte->load('psicologo')
        ], 201);
    }

    /**
     * Estadísticas globales para el Director
     */
    public function estadisticasGlobales()
    {
        $totalEstudiantes = DB::table('estudiantes')->count();
        $totalDiagnosticos = DB::table('diagnosticos')->count();
        $casosCriticos = DB::table('diagnosticos')->where('nivel_riesgo', 'ALTO')->count();
        $citasPendientes = DB::table('citas')->where('estado', 'PENDIENTE')->count();

        $diagnosticosPorMes = DB::table('diagnosticos')
            ->select(DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'), DB::raw('COUNT(*) as total'))
            ->where('fecha', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        $porNivelRiesgo = DB::table('diagnosticos')
            ->select('nivel_riesgo', DB::raw('COUNT(*) as total'))
            ->groupBy('nivel_riesgo')
            ->get();

        $porGrado = DB::table('diagnosticos')
            ->join('estudiantes', 'diagnosticos.dni_estudiante', '=', 'estudiantes.dni')
            ->select('estudiantes.grado', DB::raw('COUNT(*) as total'))
            ->groupBy('estudiantes.grado')
            ->get();

        return response()->json([
            'resumen' => [
                'total_estudiantes' => $totalEstudiantes,
                'total_diagnosticos' => $totalDiagnosticos,
                'casos_criticos' => $casosCriticos,
                'citas_pendientes' => $citasPendientes,
            ],
            'graficas' => [
                'diagnosticos_por_mes' => $diagnosticosPorMes,
                'por_nivel_riesgo' => $porNivelRiesgo,
                'por_grado' => $porGrado,
            ]
        ], 200);
    }
}