<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//RUTAS AUTH

Route::post('/login',[AuthController::class, 'login']);
Route::post('logout',[AuthController::class, 'logout']);
Route::get('/me',[AuthController::class,'me']);
Route::post('/register',[Authcontroller::class, 'register']);