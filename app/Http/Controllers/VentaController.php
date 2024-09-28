<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EstadoVenta;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::with(['cliente', 'estadoVenta', 'detalles', 'pagos'])->get();
        return view('venta.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::all();
        $estadoVentas = EstadoVenta::all();
        $productos = Producto::all();
        return view('venta.create', compact('clientes', 'estadoVentas', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'cliente_id' => 'required',
            'productos' => 'required|array',
            'subTotal' => 'required|numeric',
            'IGV' => 'required|numeric',
            'montoTotal' => 'required|numeric',
        ]);
    
        // Crear una nueva venta
        $venta = new Venta();
        $venta->cliente_id = $request->cliente_id;
        $venta->subTotal = $request->subTotal;
        $venta->IGV = $request->IGV;
        $venta->montoTotal = $request->montoTotal;
        $venta->save();
    
        // Registrar los productos en la venta
        foreach ($request->productos as $productoData) {
            // Usa el modelo correcto para el detalle de la venta
            $productoDetalle = new VentaDetalle(); // Asegúrate de que este modelo exista
            $productoDetalle->venta_id = $venta->id;
            $productoDetalle->producto_id = $productoData['id'];
            $productoDetalle->cantidad = $productoData['cantidad'];
            
            // Asegúrate de que el precio_unitario se esté pasando desde el formulario
            $productoSeleccionado = Producto::find($productoData['id']);
            $productoDetalle->precio_unitario = $productoSeleccionado->precioP; // Asignar el precio unitario
            $productoDetalle->subtotal = $productoData['cantidad'] * $productoSeleccionado->precioP; // Calcular subtotal
        
            $productoDetalle->save();
        }
    
        // Redirigir al formulario de pago, pasando el ID de la venta
        return redirect()->route('ventas.pagos.create', $venta->id)->with('success', 'Venta registrada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        $venta->load('detalles.producto'); // Cargar los detalles con sus productos
        return view('venta.show', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        $clientes = Cliente::all();
        $estadoVentas = EstadoVenta::all();
        $productos = Producto::all();
        return view('venta.edit', compact('venta', 'clientes', 'estadoVentas', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'codigoVenta' => 'required|string|max:20|unique:ventas,codigoVenta,' . $venta->id,
            'cliente_id' => 'required|exists:clientes,id',
            'estado_venta_id' => 'required|exists:estado_ventas,id',
            'subTotal' => 'required|numeric',
            'IGV' => 'required|numeric',
            'montoTotal' => 'required|numeric',
        ]);

        $venta->update($validated);

        // Actualizar detalles de la venta
        VentaDetalle::where('venta_id', $venta->id)->delete();
        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $productoData['cantidad'],
                'precio_unitario' => $producto->precio,
                'subtotal' => $productoData['cantidad'] * $producto->precio,
            ]);
        }

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada con éxito.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        $venta->delete();
        return redirect()->route('ventas.index')->with('success', 'Venta eliminada con éxito.');
    }


    /**
     * Método para actualizar los cálculos de subtotal, IGV y monto total.
     * Se puede llamar vía AJAX.
     */
    public function calcularTotales(Request $request)
    {
        $subtotal = 0;
        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            $subtotal += $productoData['cantidad'] * $producto->precio;
        }
        
        $igv = $subtotal * 0.18; // 18% de IGV
        $montoTotal = $subtotal + $igv;

        return response()->json([
            'subtotal' => number_format($subtotal, 2),
            'IGV' => number_format($igv, 2),
            'montoTotal' => number_format($montoTotal, 2),
        ]);
    }
}
