<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('users', UserController::class);

Route::resource('colaboradores', ColaboradorController::class);
Route::resource('clientes', ClienteController::class);
Route::resource('productos', ProductoController::class);
Route::resource('proveedores', ProveedorController::class);

// VENTAS
Route::resource('ventas', VentaController::class);
Route::post('/ventas/{id}/anular', [VentaController::class, 'anularVenta'])->name('ventas.anular');

// Route::resource('ventas.pagos', PagoController::class)->except(['index']);

// COMPRAS
Route::resource('compras', CompraController::class);
Route::post('/compras/{id}/anular', [CompraController::class, 'anularCompra'])->name('compras.anular');
Route::post('/compras/{compra}/recibir', [CompraController::class, 'recibirPedido'])->name('compras.recibir');

// Route::resource('compras.pagos', PagoController::class)->except(['index']);

Route::get('pagos/create/{id}/{type}', [PagoController::class, 'create'])->name('pagos.create');
Route::post('pagos/store/{id}/{type}', [PagoController::class, 'store'])->name('pagos.store');





// index(): Muestra una lista de productos.
// create(): Muestra el formulario para crear un nuevo producto.
// store(): Guarda un nuevo producto en la base de datos.
// show($id): Muestra un producto específico.
// edit($id): Muestra el formulario para editar un producto existente.
// update(Request $request, $id): Actualiza un producto existente.
// destroy($id): Elimina un producto existente.
