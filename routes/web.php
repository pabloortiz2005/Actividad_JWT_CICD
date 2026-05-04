<?php

use App\Http\Controllers\DirectorController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Ruta de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Rutas de Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de Perfil de Usuario 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Ruta pública para obtener el token de acceso
Route::post('login', [AuthController::class, 'login']);

/**
 * Rutas protegidas por JWT.
 * Solo los usuarios con un token válido pueden acceder a estos recursos.
 */
Route::middleware('auth:api')->group(function () {
    
    // CRUD completo de Directores
    Route::resource('directores', DirectorController::class);
    
    // CRUD completo de Películas
    Route::resource('peliculas', PeliculaController::class);
    
    // Ruta para cerrar sesión en la API
    Route::post('logout', [AuthController::class, 'logout']);
});

require __DIR__.'/auth.php';