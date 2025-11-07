<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

























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