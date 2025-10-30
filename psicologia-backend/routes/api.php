<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EstudianteController;
use App\Http\Controllers\Api\ApoderadoController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\NotificacionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================
// RUTAS PÚBLICAS (Sin autenticación por ahora)
// ============================================

// AUTH
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/register', [AuthController::class, 'register']);

// USUARIOS
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);
Route::patch('/users/{user}/toggle-estado', [UserController::class, 'toggleEstado']);
Route::get('/tutores', [UserController::class, 'tutores']);
Route::get('/psicologos', [UserController::class, 'psicologos']);
Route::post('/cambiar-password', [UserController::class, 'cambiarPassword']);

// ESTUDIANTES
Route::get('/estudiantes', [EstudianteController::class, 'index']);
Route::post('/estudiantes', [EstudianteController::class, 'store']);
Route::get('/estudiantes/{dni}', [EstudianteController::class, 'show']);
Route::put('/estudiantes/{dni}', [EstudianteController::class, 'update']);
Route::delete('/estudiantes/{dni}', [EstudianteController::class, 'destroy']);
Route::get('/estudiantes-search', [EstudianteController::class, 'search']);

// APODERADOS
Route::get('/apoderados', [ApoderadoController::class, 'index']);
Route::post('/apoderados', [ApoderadoController::class, 'store']);
Route::get('/apoderados/{apoderado}', [ApoderadoController::class, 'show']);
Route::put('/apoderados/{apoderado}', [ApoderadoController::class, 'update']);
Route::delete('/apoderados/{apoderado}', [ApoderadoController::class, 'destroy']);

// DIAGNÓSTICOS
Route::get('/diagnosticos', [DiagnosticoController::class, 'index']);
Route::post('/diagnosticos', [DiagnosticoController::class, 'store']);
Route::get('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'show']);
Route::put('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'update']);
Route::delete('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'destroy']);
Route::get('/diagnosticos-estudiante/{dni}', [DiagnosticoController::class, 'byEstudiante']);
Route::get('/diagnosticos-estadisticas', [DiagnosticoController::class, 'estadisticas']);

// CITAS
Route::get('/citas', [CitaController::class, 'index']);
Route::post('/citas', [CitaController::class, 'store']);
Route::get('/citas/{cita}', [CitaController::class, 'show']);
Route::put('/citas/{cita}', [CitaController::class, 'update']);
Route::delete('/citas/{cita}', [CitaController::class, 'destroy']);
Route::get('/citas-pendientes', [CitaController::class, 'pendientes']);
Route::patch('/citas/{cita}/realizada', [CitaController::class, 'marcarRealizada']);

// REPORTES
Route::get('/reportes', [ReporteController::class, 'index']);
Route::post('/reportes', [ReporteController::class, 'store']);
Route::get('/reportes/{reporte}', [ReporteController::class, 'show']);
Route::put('/reportes/{reporte}', [ReporteController::class, 'update']);
Route::delete('/reportes/{reporte}', [ReporteController::class, 'destroy']);
Route::post('/reportes-generar-mensual', [ReporteController::class, 'generarMensual']);
Route::get('/reportes-estadisticas-globales', [ReporteController::class, 'estadisticasGlobales']);

// NOTIFICACIONES
Route::get('/notificaciones', [NotificacionController::class, 'index']);
Route::post('/notificaciones', [NotificacionController::class, 'store']);
Route::get('/notificaciones/{notificacion}', [NotificacionController::class, 'show']);
Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'destroy']);
Route::patch('/notificaciones/{notificacion}/leida', [NotificacionController::class, 'marcarLeida']);
Route::post('/notificaciones-marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas']);
Route::get('/notificaciones-no-leidas', [NotificacionController::class, 'noLeidas']);
Route::get('/notificaciones-enviadas', [NotificacionController::class, 'enviadas']);


/*
============================================
NOTA PARA ACTIVAR AUTENTICACIÓN
============================================

Cuando el docente lo indique, envolver todas las rutas (excepto /login) en:

Route::middleware('auth:sanctum')->group(function () {
    // Todas las rutas protegidas aquí
});

Y descomentar todos los comentarios "TODO" en los controladores
*/
