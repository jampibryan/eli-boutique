<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PrediccionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteGraficoController;
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

// Route::post('api/prediccion-ventas', [VentaController::class, 'predecirVentas']);
Route::get('obtener-datos-ventas', [VentaController::class, 'obtenerDatosVentas']);

Route::get('exportar-ventas', [VentaController::class, 'exportarVentasCsv'])->name('exportarCSV');


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

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Caja
Route::get('cajas/reporte', [CajaController::class, 'pdfCajas'])->name('cajas.pdf');
Route::get('cajas', [CajaController::class, 'index'])->name('cajas.index');
Route::post('/caja/abrir', [CajaController::class, 'abrirCaja'])->name('caja.abrir');
Route::post('/caja/cerrar', [CajaController::class, 'cerrarCaja'])->name('caja.cerrar');

// USUARIOS
Route::resource('users', UserController::class);

// CLIENTES
Route::get('api/clientes', [ClienteController::class, 'apiClientes'])->name('clientes.api');
Route::get('clientes/reporte', [ClienteController::class, 'pdfClientes'])->name('clientes.pdf');
Route::resource('clientes', ClienteController::class);

// COLABORADORES
Route::get('colaboradores/reporte', [ColaboradorController::class, 'pdfColaboradores'])->name('colaboradores.pdf');
Route::resource('colaboradores', ColaboradorController::class);

// PROVEEDORES
Route::get('api/proveedores', [ProveedorController::class, 'apiProveedores'])->name('proveedores.api');
Route::get('proveedores/reporte', [ProveedorController::class, 'pdfProveedores'])->name('proveedores.pdf');
Route::resource('proveedores', ProveedorController::class);

// PRODUCTOS
Route::get('productos/reporte', [ProductoController::class, 'pdfProductos'])->name('productos.pdf');
Route::resource('productos', ProductoController::class);


// VENTAS
Route::get('/ventas/{venta}/comprobante', [VentaController::class, 'pdfComprobante'])->name('ventas.comprobante');
Route::get('/ventas/reporte', [VentaController::class, 'pdfVentas'])->name('ventas.pdf');
Route::post('/ventas/{id}/anular', [VentaController::class, 'anularVenta'])->name('ventas.anular');
Route::resource('ventas', VentaController::class);

// COMPRAS
Route::get('/compras/reporte', [CompraController::class, 'pdfCompras'])->name('compras.pdf');
Route::get('/compras/{compra}/orden', [CompraController::class, 'pdfOrdenCompra'])->name('ordenCompras.pdf');

Route::resource('compras', CompraController::class);
Route::post('/compras/{id}/anular', [CompraController::class, 'anularCompra'])->name('compras.anular');
Route::post('/compras/{compra}/recibir', [CompraController::class, 'recibirPedido'])->name('compras.recibir');

// Route::resource('compras.pagos', PagoController::class)->except(['index']);

Route::get('pagos/create/{id}/{type}', [PagoController::class, 'create'])->name('pagos.create');
Route::post('pagos/store/{id}/{type}', [PagoController::class, 'store'])->name('pagos.store');



// Ruta para generar el PDF del gráfico de ventas
Route::get('/reportes/graficos/ventas/pdf', [ReporteGraficoController::class, 'generarPdfVentasDia'])->name('reporte.grafico.ventas.pdf');
Route::get('/reportes/graficos/ventas', [ReporteGraficoController::class, 'ventas'])->name('reporte.grafico.ventas');
Route::get('/reportes/graficos/compras', [ReporteGraficoController::class, 'compras'])->name('reporte.grafico.compras');
// Route::post('/reportes/graficos/ventas/pdf', [ReporteGraficoController::class, 'generarPdfVentasDia'])->name('reporte.grafico.ventas.pdf');


//Prediccion
Route::get('/prediccion', [PrediccionController::class, 'index'])->name('prediccion.index');



Route::get('/abrir-ayuda', function () {
    $ruta = "D:\\X CICLO\\TESIS II\\Semana 13\\Manual de Usuario_Ventas.chm"; // Ruta completa al archivo de ayuda
    // $ruta = "D:\\MOD_VENTAS\\Ayuda_ventas.chm"; // Ruta completa al archivo de ayuda

    if (file_exists($ruta)) {
        // Comando ajustado para abrir el archivo en segundo plano
        pclose(popen('start /B "Ayuda" "' . $ruta . '"', 'r'));
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Archivo no encontrado'], 404);
})->name('abrirAyuda');


// index(): Muestra una lista de productos.
// create(): Muestra el formulario para crear un nuevo producto.
// store(): Guarda un nuevo producto en la base de datos.
// show($id): Muestra un producto específico.
// edit($id): Muestra el formulario para editar un producto existente.
// update(Request $request, $id): Actualiza un producto existente.
// destroy($id): Elimina un producto existente.



// https://blog.hubspot.es/sales/reporte-de-ventas