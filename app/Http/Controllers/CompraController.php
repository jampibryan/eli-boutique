<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CompraController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar compras', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    public function apiCompras()
    {
        // Cargar las compras con sus detalles y pagos
        $compras = Compra::with(['proveedor', 'detalles', 'pago'])->get();
        return view('Compra.index', compact('compras'));
    }




    public function pdfCompras()
    {
        $compras = Compra::whereNotNull('id')->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Compra.reporte', compact('compras')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Compras.pdf'); //Abre una pestaña
    }

    public function pdfOrdenCompra(Compra $compra)
    {
        // Cargar las relaciones del proveedor y los detalles de la compra
        $compra->load(['proveedor', 'detalles.producto']); 

        // Obtener el colaborador específico (por ejemplo, el que tiene id = 1)
        $colaborador = Colaborador::find(1); // Cambia 'Proveedor' por tu modelo de Colaborador si es diferente

        // Pasar tanto la compra como el proveedor a la vista usando compact
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Compra.orden', compact('compra', 'colaborador'))); // Solo 'compra' ya incluye el proveedor cargado

        return $pdf->stream('Orden de compra - ' . $compra->codigoCompra . '.pdf');
    }


    public function index()
    {
        // Cargar las compras con sus detalles y pagos
        $compras = Compra::with(['proveedor', 'detalles', 'pago'])->get();
        return view('Compra.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cargar todos los proveedores
        $proveedores = Proveedor::all();
        $productos = Producto::all();
        return view('Compra.create', compact('proveedores', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Validar la solicitud
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // Crear una nueva compra
        $compra = Compra::create([
            'proveedor_id' => $request->proveedor_id,
        ]);

        // Registrar los productos en la compra
        foreach ($request->productos as $productoData) {
            $productoDetalle = new CompraDetalle();
            $productoDetalle->compra_id = $compra->id;
            $productoDetalle->producto_id = $productoData['id'];
            $productoDetalle->cantidad = $productoData['cantidad'];
            // No se almacena precio_unitario ni subtotal
            $productoDetalle->save();
        }

        // Redirigir al índice de compras con un mensaje de éxito
        return redirect()->route('compras.index')->with('success', 'Compra registrada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Compra $compra)
    {
        // Obtener la compra y los proveedores/productos para la edición
        $proveedores = Proveedor::all();
        $productos = Producto::all();

        // Pasar la información a la vista de edición
        return view('Compra.edit', compact('compra', 'proveedores', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Compra $compra)
    {
        // Validar la solicitud
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        // Actualizar la información de la compra
        $compra->proveedor_id = $request->proveedor_id;
        $compra->save();

        // Eliminar los detalles existentes
        CompraDetalle::where('compra_id', $compra->id)->delete();

        // Volver a registrar los productos
        foreach ($request->productos as $productoData) {
            $productoDetalle = new CompraDetalle();
            $productoDetalle->compra_id = $compra->id;
            $productoDetalle->producto_id = $productoData['id'];
            $productoDetalle->cantidad = $productoData['cantidad'];
            $productoDetalle->save();
        }

        // Redirigir al índice de compras con un mensaje de éxito
        return redirect()->route('compras.index')->with('success', 'Compra actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compra $compra)
    {
        $compra->delete();
        return redirect()->route('compras.index')->with('success', 'Compra eliminada con éxito.');
    }

    public function recibirPedido($compraId)
    {
        // Encuentra la compra por su ID
        $compra = Compra::findOrFail($compraId);

        // Obtén el estado "Recibido"
        $estadoRecibido = EstadoTransaccion::where('descripcionET', 'Recibido')->first();

        if ($estadoRecibido) {
            // Cambia el estado de la compra a "Recibido"
            $compra->estado_transaccion_id = $estadoRecibido->id;
            $compra->save(); // Guarda los cambios
        }

        // Itera sobre los detalles de la compra para actualizar el stock de cada producto
        foreach ($compra->detalles as $detalle) {
            $producto = $detalle->producto;
            // Aumenta el stock del producto con la cantidad comprada
            $producto->stockP += $detalle->cantidad;
            $producto->save(); // Guarda los cambios en el producto
        }

        // Redirige a la página de compras con un mensaje de éxito
        return redirect()->route('compras.index')->with('success', 'Stock actualizado correctamente. Pedido recibido.');
    }

    public function anularCompra($compraId)
    {
        $compra = Compra::findOrFail($compraId); // Encuentra la venta por su ID
        if ($compra->estadoTransaccion->descripcionET !== 'Anulado') {
            $compra->anular(); // Llama a la función anular en el modelo Compra
            return redirect()->route('compras.index')->with('success', 'Compra anulada correctamente.');
        } else {
            return redirect()->route('compras.index')->with('error', 'La compra ya está anulada.');
        }
    }
}

