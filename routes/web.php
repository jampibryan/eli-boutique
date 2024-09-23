<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
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

Route::resource('colaboradores', ColaboradorController::class);
Route::resource('clientes', ClienteController::class);
Route::resource('productos', ProductoController::class);
Route::resource('proveedores', ProveedorController::class);


// index(): Muestra una lista de productos.
// create(): Muestra el formulario para crear un nuevo producto.
// store(): Guarda un nuevo producto en la base de datos.
// show($id): Muestra un producto específico.
// edit($id): Muestra el formulario para editar un producto existente.
// update(Request $request, $id): Actualiza un producto existente.
// destroy($id): Elimina un producto existente.


Route::resource('ventas', VentaController::class);
