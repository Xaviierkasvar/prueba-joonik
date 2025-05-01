<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LocationController;
use App\Http\Middleware\ApiKeyMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas protegidas con middleware de clave API
Route::middleware([ApiKeyMiddleware::class])->group(function () {
    // Obtener todas las sedes con paginación
    Route::get('/locations', [LocationController::class, 'index']);
    
    // Obtener una sede específica por su código
    Route::get('/locations/{code}', [LocationController::class, 'show']);
});
