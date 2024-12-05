<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VentaController extends Controller
{

    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar ventas', ['only' => ['index', 'create', 'store', 'edit', 'update', 'destroy', 'calcularTotales']]);
    }


    public function exportarVentasCsv()
    {
        // Obtener los datos de ventas
        $ventas = Venta::with('detalles.producto')->get();
    
        // Crear un archivo CSV
        $csvFileName = 'ventas.csv';
        $file = fopen($csvFileName, 'w');
    
        // Escribir la cabecera del CSV
        fputcsv($file, ['producto_id', 'cantidad_vendida', 'año', 'mes', 'día']);
    
        // Escribir los datos de ventas
        foreach ($ventas as $venta) {
            foreach ($venta->detalles as $detalle) {
                // Dividir la fecha en año, mes y día
                $fecha = $venta->created_at; // Obtener la fecha de la venta
                $año = $fecha->year; // Año
                $mes = $fecha->month; // Mes
                $dia = $fecha->day; // Día
    
                // Escribir los datos de cada venta en el CSV
                fputcsv($file, [
                    'producto_id' => $detalle->producto_id,
                    'cantidad_vendida' => $detalle->cantidad,
                    'año' => $año,
                    'mes' => $mes,
                    'día' => $dia
                ]);
            }
        }
    
        fclose($file);
    
        return response()->download($csvFileName)->deleteFileAfterSend(true);
    }
    
    // Método para obtener las ventas desde la base de datos
    public function obtenerDatosVentas(Request $request)
    {
        // Obtener las ventas con los detalles y productos relacionados
        $ventas = Venta::with('detalles.producto')  // Asegurándonos de que cargamos la relación con Producto
            ->get();
    
        // Preparar los datos para ser enviados a la API
        $resultados = [];
    
        foreach ($ventas as $venta) {
            foreach ($venta->detalles as $detalle) {
                $resultados[] = [
                    'producto_nombre' => $detalle->producto->descripcionP,  // Aquí obtenemos el nombre del producto
                    'cantidad_vendida' => $detalle->cantidad,
                    'año' => $venta->created_at->year,   // Año de la venta
                    'mes' => $venta->created_at->month,  // Mes de la venta
                    'dia' => $venta->created_at->day,    // Día de la venta
                    'producto_id' => $detalle->producto_id, // También puedes dejar el ID si lo necesitas
                ];
            }
        }
    
        // Retornar los resultados en formato JSON
        return response()->json($resultados);
    }
    

    public function pdfVentas()
    {
        $ventas = Venta::whereNotNull('id')->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Venta.reporte', compact('ventas')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Ventas.pdf'); //Abre una pestaña
    }

    public function pdfComprobante($id)
    {
        // Obtener la venta específica por ID
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago.comprobante'])
            ->findOrFail($id);

        // Obtener el colaborador específico (por ejemplo, el que tiene id = 1)
        $colaborador = Colaborador::find(1);
        
        // Verificar el tipo de comprobante
        $tipoComprobante = $venta->pago->comprobante->descripcionCOM ?? '';

        // Generar el PDF utilizando la vista correspondiente
        $pdf = App::make('dompdf.wrapper');

        if ($tipoComprobante === 'Boleta') {
            $pdf->loadHTML(view('Venta.boletaV', compact('venta', 'colaborador')));
            // Abrir el PDF en una nueva pestaña
            return $pdf->stream('Boleta_' . $venta->codigoVenta . '.pdf');
            
        } elseif ($tipoComprobante === 'Factura') {
            $pdf->loadHTML(view('Venta.facturaV', compact('venta', 'colaborador')));
            // Abrir el PDF en una nueva pestaña
            return $pdf->stream('Factura_' . $venta->codigoVenta . '.pdf');

        } else {
            // Si no es ni boleta ni factura, puedes manejarlo como desees
            // Por ejemplo, redirigir a una vista de error o cargar una vista por defecto
            return abort(404, 'Comprobante no válido');
        }
    }

    public function index()
    {
        $ventas = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago'])->get();
        return view('Venta.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::all();
        $productos = Producto::all();
        return view('Venta.create', compact('clientes', 'productos'));

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
            
            // Obtener el producto seleccionado y su precio
            $productoSeleccionado = Producto::find($productoData['id']);
            $productoDetalle->precio_unitario = $productoSeleccionado->precioP; // Asignar el precio unitario
            $productoDetalle->subtotal = $productoData['cantidad'] * $productoSeleccionado->precioP; // Calcular subtotal
        
            $productoDetalle->save();

            // Actualizar el stock del producto
            $productoSeleccionado->stockP -= $productoData['cantidad'];
            $productoSeleccionado->save();
        }
    
        // Redirigir al formulario de pago, pasando el ID de la venta
        // return redirect()->route('ventas.pagos.create', $venta->id)->with('success', 'Venta registrada con éxito.');
        return redirect()->route('ventas.index')->with('success', 'Venta registrada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        $venta->load('detalles.producto'); // Cargar los detalles con sus productos
        return view('Venta.show', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        $clientes = Cliente::all();
        $productos = Producto::all();

        // Cargar los productos asociados a través de los detalles de la venta
        // $venta->load('detalles.producto');

        // Calcular el stock inicial para cada detalle
        foreach ($venta->detalles as $detalle) {
            $producto = $detalle->producto;
            $detalle->stock_inicial = $producto->stockP + $detalle->cantidad;
        }
        
        // return view('venta.edit', compact('venta', 'clientes', 'estadoVentas', 'productos'));
        return view('Venta.edit', compact('venta', 'clientes', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        // Validación de la venta y los productos
        $validated = $request->validate([
            'codigoVenta' => 'required|string|max:20|unique:ventas,codigoVenta,' . $venta->id,
            'cliente_id' => 'required|exists:clientes,id',
            'subTotal' => 'required|numeric',
            'IGV' => 'required|numeric',
            'montoTotal' => 'required|numeric',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
        ]);

        // Obtener los detalles actuales de la venta
        $detallesAntiguos = $venta->detalles;

        // Crear un arreglo para almacenar las diferencias de stock
        $diferenciasStock = [];

        // Actualizar la venta con los datos principales
        $venta->update([
            'cliente_id' => $validated['cliente_id'],
            'subTotal' => $validated['subTotal'],
            'IGV' => $validated['IGV'],
            'montoTotal' => $validated['montoTotal'],
        ]);

        // Almacenar IDs de productos existentes en la venta
        $productoIdsActuales = $detallesAntiguos->pluck('producto_id')->toArray();

        // Actualizar detalles de la venta con los nuevos productos seleccionados
        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);

            // Buscar el detalle de venta anterior
            $detalleAntiguo = $detallesAntiguos->firstWhere('producto_id', $producto->id);
            $cantidadAntigua = $detalleAntiguo ? $detalleAntiguo->cantidad : 0;

            // Calcular la diferencia en la cantidad
            $diferencia = $productoData['cantidad'] - $cantidadAntigua;

            // Ajustar el stock del producto según la diferencia
            if ($diferencia !== 0) {
                $producto->stockP -= $diferencia; // Resta si es negativo, suma si es positivo
                $producto->save();
            }

            // Crear o actualizar el detalle de la venta
            if ($detalleAntiguo) {
                // Actualizar el detalle existente
                $detalleAntiguo->update([
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $producto->precioP,
                    'subtotal' => $productoData['cantidad'] * $producto->precioP,
                ]);
            } else {
                // Crear un nuevo detalle si no existía
                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $producto->precioP,
                    'subtotal' => $productoData['cantidad'] * $producto->precioP,
                ]);
            }
        }

        // Ahora, verifica qué productos han sido eliminados
        $nuevosProductoIds = collect($request->productos)->pluck('id')->toArray();
        $productosEliminados = array_diff($productoIdsActuales, $nuevosProductoIds);

        // Actualiza el stock de los productos eliminados
        foreach ($productosEliminados as $productoId) {
            $detalleAntiguo = $detallesAntiguos->firstWhere('producto_id', $productoId);
            if ($detalleAntiguo) {
                $producto = Producto::find($productoId);
                $producto->stockP += $detalleAntiguo->cantidad; // Regresa el stock al producto
                $producto->save();

                // Opcional: Eliminar el detalle de la venta si es necesario
                $detalleAntiguo->delete();
            }
        }

        // Redireccionar a la lista de ventas con un mensaje de éxito
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

    public function anularVenta($id)
    {
        $venta = Venta::findOrFail($id); // Encuentra la venta por su ID
        if ($venta->estadoTransaccion->descripcionET !== 'Anulado') {
            $venta->anular(); // Llama a la función anular en el modelo Venta
            return redirect()->route('ventas.index')->with('success', 'Venta anulada correctamente.');
        } else {
            return redirect()->route('ventas.index')->with('error', 'La venta ya está anulada.');
        }
    }

}
