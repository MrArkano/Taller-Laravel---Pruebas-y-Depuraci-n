<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CitaController as ApiCitaController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
});

// MediCitas
Route::post('/login', [TokenController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/citas', [ApiCitaController::class, 'index']);
    Route::post('/citas', [ApiCitaController::class, 'store']);
    Route::get('/citas/{cita}', [ApiCitaController::class, 'show']);
    Route::patch('/citas/{cita}', [ApiCitaController::class, 'update']);
});
