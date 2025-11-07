<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');












































// ESTUDIANTE

Route::get('/estudiantes',[EstudianteController::class,'index']);
Route::get('/estudiantes',[EstudianteController::class,'store']);
Route::get('/estudiantes/{dni}', [EstudianteController::class, 'show']);
Route::put('/estudiantes/{dni}', [EstudianteController::class, 'update']);
Route::delete('/estudiantes/{dni}', [EstudianteController::class, 'destroy']);
Route::get('/estudiante-search',[EstudianteController::class, 'search']);
