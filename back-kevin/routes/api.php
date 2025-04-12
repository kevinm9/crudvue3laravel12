
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Ruta para obtener el usuario autenticado (común en APIs)
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Rutas CRUD para los usuarios
Route::apiResource('users', UserController::class); // <--- Añade esta línea