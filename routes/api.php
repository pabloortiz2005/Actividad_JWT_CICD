<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Ruta pública para obtener el token (Será: /api/login)
Route::post('login', [AuthController::class, 'login']);

/**
 * Rutas protegidas por JWT.
 */


    Route::middleware('auth:api')->group(function () {
    
    // CRUD completo de Directores (Será: /api/directores)
    Route::resource('directores', DirectorController::class);
    
    // CRUD completo de Películas (Será: /api/peliculas)
    Route::resource('peliculas', PeliculaController::class);
    
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Cerrar sesión
    Route::post('logout', [AuthController::class, 'logout']);
});