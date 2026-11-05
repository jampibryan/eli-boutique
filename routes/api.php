<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileController;
use App\Http\Middleware\MobileReadOnly;

// Contrato móvil: sesión por instalación, lecturas paginadas y permisos del usuario.
Route::prefix('mobile/v1')->group(function () {
    Route::post('auth/login', [MobileController::class, 'login'])->middleware('throttle:6,1');
    Route::middleware(['auth:sanctum', MobileReadOnly::class])->group(function () {
        Route::get('auth/me', [MobileController::class, 'me']);
        Route::post('auth/logout', [MobileController::class, 'logout']);
        Route::get('dashboard', [MobileController::class, 'dashboard']);
        Route::get('graficos', [MobileController::class, 'charts']);
        Route::get('{module}', [MobileController::class, 'index'])->where('module', 'clientes|productos|ventas|compras|proveedores|cajas');
        Route::get('{module}/{id}', [MobileController::class, 'show'])->where('module', 'clientes|productos|ventas|compras|proveedores|cajas')->whereNumber('id');
    });
});

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

// Rutas publicas de solo lectura para consumidores ML (Streamlit)
Route::get('/obtener-datos-ventas', [ApiController::class, 'ventasDatosML']);

// Contrato analitico versionado, de solo lectura y sin datos personales.
Route::get('/analitica/v1/snapshot', [AnalyticsController::class, 'snapshot'])
    ->middleware(['auth:sanctum', 'permission:ver reportes gráficos']);

// Rutas protegidas con Sanctum
Route::middleware(['auth:sanctum', \App\Http\Middleware\ApiReadPermission::class])->group(function () {
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
