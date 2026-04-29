<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Estas rutas usan el prefijo /api y el middleware group "api".
| Para pruebas locales con Streamlit, solo `obtener-datos-ventas`
| queda publico. El resto sigue protegido con Sanctum.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Ruta publica de prueba para Streamlit / ML
Route::get('/obtener-datos-ventas', [ApiController::class, 'ventasDatosML']);

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard / Resumen
    Route::get('/dashboard', [ApiController::class, 'dashboard']);

    // Clientes
    Route::get('/clientes', [ApiController::class, 'clientes']);
    Route::get('/clientes/{id}', [ApiController::class, 'clienteShow']);

    // Productos
    Route::get('/productos', [ApiController::class, 'productos']);
    Route::get('/productos/{id}', [ApiController::class, 'productoShow']);
    Route::get('/categorias', [ApiController::class, 'categorias']);
    Route::get('/tallas', [ApiController::class, 'tallas']);

    // Ventas
    Route::get('/ventas', [ApiController::class, 'ventas']);
    Route::get('/ventas/{id}', [ApiController::class, 'ventaShow']);

    // Compras
    Route::get('/compras', [ApiController::class, 'compras']);
    Route::get('/compras/{id}', [ApiController::class, 'compraShow']);

    // Proveedores
    Route::get('/proveedores', [ApiController::class, 'proveedores']);
    Route::get('/proveedores/{id}', [ApiController::class, 'proveedorShow']);

    // Cajas
    Route::get('/cajas', [ApiController::class, 'cajas']);
    Route::get('/cajas/{id}', [ApiController::class, 'cajaShow']);

    // Catalogos
    Route::get('/estados-transaccion', [ApiController::class, 'estadosTransaccion']);
});
