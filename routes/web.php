<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ProductoController;
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


// Route::get('/producto', [ProductoController::class, 'index'])->name('producto.index');

// Route::group(['prefix' => 'productos', 'as' => 'productos.'], function () {
    //     Route::get('/', [ProductoController::class, 'index'])->name('index');
    //     Route::get('/{id}', [ProductoController::class, 'show'])->name('show');
    //     Route::post('/', [ProductoController::class, 'store'])->name('store');
    // });
    
// Route::get('/cliente', [ClienteController::class, 'index'])->name('cliente.index');
// Route::get('/colaborador', [ColaboradorController::class, 'index'])->name('colaborador.index');




Route::resource('colaboradores', ColaboradorController::class);

// // Mostrar todos los colaboradores
// Route::get('colaboradores', [ColaboradorController::class, 'index'])->name('colaboradores.index');

// // Mostrar el formulario para crear un nuevo colaborador
// Route::get('colaboradores/create', [ColaboradorController::class, 'create'])->name('colaboradores.create');

// // Almacenar un nuevo colaborador
// Route::post('colaboradores', [ColaboradorController::class, 'store'])->name('colaboradores.store');

// // Mostrar el formulario para editar un colaborador existente
// Route::get('colaboradores/{colaborador}/edit', [ColaboradorController::class, 'edit'])->name('colaboradores.edit');

// // Actualizar un colaborador existente
// Route::put('colaboradores/{colaborador}', [ColaboradorController::class, 'update'])->name('colaboradores.update');

// // Eliminar un colaborador existente
// Route::delete('colaboradores/{colaborador}', [ColaboradorController::class, 'destroy'])->name('colaboradores.destroy');


Route::resource('clientes', ClienteController::class);
Route::resource('productos', ProductoController::class);



// index(): Muestra una lista de productos.
// create(): Muestra el formulario para crear un nuevo producto.
// store(): Guarda un nuevo producto en la base de datos.
// show($id): Muestra un producto específico.
// edit($id): Muestra el formulario para editar un producto existente.
// update(Request $request, $id): Actualiza un producto existente.
// destroy($id): Elimina un producto existente.