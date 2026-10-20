<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas Públicas
Route::post('/login', [AuthController::class, 'login']);
Route::get('/cursos', [CursoController::class, 'index']);

// Rutas Protegidas por Autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil', [UserController::class, 'perfil']);
    Route::put('/cursos/{curso}', [CursoController::class, 'update']); 
    Route::get('/admin/finanzas', [DashboardController::class, 'reporteFinanciero']); 
});
