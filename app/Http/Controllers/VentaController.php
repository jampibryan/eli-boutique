<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
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

    public function apiVentas()
    {
        $ventas = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago.comprobante'])
            ->whereHas('estadoTransaccion')
            ->whereHas('pago.comprobante')
            ->get();

        return response()->json($ventas);
    }

    public function apiVentaDetalle($id)
    {
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles.producto', 'pago.comprobante'])
            ->findOrFail($id);

        return response()->json($venta);
    }

    public function exportarVentasCsv()
    {
        $ventas = Venta::with('detalles.producto')->get();

        $csvFileName = 'ventas.csv';
        $file = fopen($csvFileName, 'w');

        fputcsv($file, ['producto_id', 'cantidad_vendida', 'año', 'mes', 'día']);

        foreach ($ventas as $venta) {
            foreach ($venta->detalles as $detalle) {
                $fecha = $venta->created_at;
                fputcsv($file, [
                    'producto_id' => $detalle->producto_id,
                    'cantidad_vendida' => $detalle->cantidad,
                    'año' => $fecha->year,
                    'mes' => $fecha->month,
                    'día' => $fecha->day
                ]);
            }
        }

        fclose($file);

        return response()->download($csvFileName)->deleteFileAfterSend(true);
    }

    public function obtenerDatosVentas(Request $request)
    {
        $ventas = Venta::with('detalles.producto')->get();

        $resultados = [];

        foreach ($ventas as $venta) {
            foreach ($venta->detalles as $detalle) {
                $resultados[] = [
                    'producto_nombre' => $detalle->producto->descripcionP,
                    'cantidad_vendida' => $detalle->cantidad,
                    'año' => $venta->created_at->year,
                    'mes' => $venta->created_at->month,
                    'dia' => $venta->created_at->day,
                    'producto_id' => $detalle->producto_id,
                ];
            }
        }

        return response()->json($resultados);
    }


    public function pdfVentas()
    {
        $ventas = Venta::whereNotNull('id')->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Venta.reporte', compact('ventas')));

        return $pdf->stream('Reporte de Ventas.pdf');
    }

    public function pdfComprobante($id)
    {
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago.comprobante'])
            ->findOrFail($id);

        $colaborador = Colaborador::find(1);

        $tipoComprobante = $venta->pago->comprobante->descripcionCOM ?? '';

        $pdf = App::make('dompdf.wrapper');

        if ($tipoComprobante === 'Boleta') {
            $pdf->loadHTML(view('Venta.boletaV', compact('venta', 'colaborador')));
            return $pdf->stream('Boleta_' . $venta->codigoVenta . '.pdf');
        } elseif ($tipoComprobante === 'Factura') {
            $pdf->loadHTML(view('Venta.facturaV', compact('venta', 'colaborador')));
            return $pdf->stream('Factura_' . $venta->codigoVenta . '.pdf');
        } else {
            return abort(404, 'Comprobante no válido');
        }
    }

    public function index()
    {
        $ventas = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago'])->get();
        return view('Venta.index', compact('ventas'));
    }

    public function create()
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('productos.index')->with('error', 'El carrito está vacío. Agrega productos primero.');
        }

        $clientes = Cliente::all();
        $productos = Producto::whereIn('id', collect($carrito)->pluck('producto_id'))->get()->keyBy('id');
        $tallas = ProductoTalla::whereIn('id', collect($carrito)->pluck('talla_id'))->get()->keyBy('id');

        // Cargar stocksPorTalla para validación en la vista
        $stocksPorTalla = [];
        foreach ($carrito as $item) {
            $key = $item['producto_id'] . '_' . $item['talla_id'];
            $tallaStock = ProductoTallaStock::where('producto_id', $item['producto_id'])
                ->where('producto_talla_id', $item['talla_id'])
                ->first();
            $stocksPorTalla[$key] = $tallaStock ? $tallaStock->stock : 0;
        }

        $clienteSeleccionado = session('venta_cliente');

        return view('Venta.create', compact('clientes', 'carrito', 'productos', 'tallas', 'stocksPorTalla', 'clienteSeleccionado'));
    }

    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array',
            'subTotal' => 'required|numeric',
            'IGV' => 'required|numeric',
            'montoTotal' => 'required|numeric',
        ]);

        // ==================== VERIFICAR CAJA ABIERTA ====================
        $cajaHoy = Caja::whereDate('fecha', today())->first();

        if (!$cajaHoy) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Debes abrir caja primero antes de registrar ventas.');
        }
        // ================================================================

        // ==================== OBTENER ESTADO "PENDIENTE" ====================
        $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();

        if (!$estadoPendiente) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se encontró el estado "Pendiente". Configura los estados de transacción primero.');
        }
        // ====================================================================

        // Crear una nueva venta CON ESTADO "PENDIENTE"
        $venta = new Venta();
        $venta->cliente_id = $request->cliente_id;
        $venta->caja_id = $cajaHoy->id; // ← ASOCIAR A LA CAJA DEL DÍA
        $venta->estado_transaccion_id = $estadoPendiente->id; // ← ESTADO PENDIENTE (NO PAGADO)
        $venta->subTotal = $request->subTotal;
        $venta->IGV = $request->IGV;
        $venta->montoTotal = $request->montoTotal;
        $venta->save(); // ← LOS EVENTOS NO SUMARÁN A CAJA (porque está pendiente)

        // Guardar cliente seleccionado en sesión para mantenerlo al regresar
        session(['venta_cliente' => $request->cliente_id]);

        // VALIDAR STOCK ANTES DE PROCESAR LA VENTA (con sistema de tallas)
        foreach ($request->productos as $productoData) {
            $productoValidar = Producto::find($productoData['id']);
            
            if (!$productoValidar) {
                return redirect()->back()->with('error', 'Producto no encontrado.');
            }
            
            // Validar stock de la talla específica
            $tallaStock = ProductoTallaStock::where('producto_id', $productoData['id'])
                ->where('producto_talla_id', $productoData['talla_id'])
                ->first();
            
            if (!$tallaStock) {
                return redirect()->back()->with('error', 
                    "No se encontró stock para la talla seleccionada de {$productoValidar->descripcionP}.");
            }
            
            if ($tallaStock->stock < $productoData['cantidad']) {
                $tallaNombre = ProductoTalla::find($productoData['talla_id'])->descripcion ?? '';
                return redirect()->back()->with('error', 
                    "Stock insuficiente para {$productoValidar->descripcionP} talla {$tallaNombre}. Stock disponible: {$tallaStock->stock}, solicitado: {$productoData['cantidad']}");
            }
        }

        // Registrar los productos en la venta
        foreach ($request->productos as $productoData) {
            $productoDetalle = new VentaDetalle();
            $productoDetalle->venta_id = $venta->id;
            $productoDetalle->producto_id = $productoData['id'];
            $productoDetalle->cantidad = $productoData['cantidad'];

            $productoSeleccionado = Producto::find($productoData['id']);
            $productoDetalle->precio_unitario = $productoSeleccionado->precioP;
            $productoDetalle->subtotal = $productoData['cantidad'] * $productoSeleccionado->precioP;

            $productoDetalle->save();

            // Actualizar el stock de la talla específica (VALIDADO PREVIAMENTE)
            $tallaStock = ProductoTallaStock::where('producto_id', $productoData['id'])
                ->where('producto_talla_id', $productoData['talla_id'])
                ->first();
            
            $tallaStock->stock -= $productoData['cantidad'];
            $tallaStock->save();
        }

        // Limpiar carrito después de registrar
        session()->forget('carrito');

        // ==================== REDIRIGIR AL PAGO ====================

        return redirect()->route('pagos.create', ['id' => $venta->id, 'type' => 'venta'])
            ->with('success', 'Venta creada exitosamente. Proceda al pago.');
    }

    public function show(Venta $venta)
    {
        $venta->load('detalles.producto');
        return view('Venta.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        // Solo permitir editar si la venta está pendiente
        if ($venta->estadoTransaccion->descripcionET !== 'Pendiente') {
            return redirect()->route('ventas.index')->with('error', 'No se puede editar una venta ya pagada.');
        }

        $clientes = Cliente::all();
        $productos = Producto::all();
        $tallas = ProductoTalla::all(); // Para el select de tallas

        // Cargar detalles de la venta como "carrito"
        $carrito = [];
        foreach ($venta->detalles as $detalle) {
            $carrito[] = [
                'producto_id' => $detalle->producto_id,
                'talla_id' => $detalle->producto_talla_id ?? ProductoTalla::first()->id, // Usar primera talla si no hay
                'cantidad' => $detalle->cantidad,
            ];
        }

        // Pasar datos a la vista
        $clienteSeleccionado = $venta->cliente_id;
        $subtotal = $venta->subTotal;
        $igv = $venta->IGV;
        $montoTotal = $venta->montoTotal;

        return view('Venta.edit', compact('venta', 'clientes', 'productos', 'tallas', 'carrito', 'clienteSeleccionado', 'subtotal', 'igv', 'montoTotal'));
    }

    public function update(Request $request, Venta $venta)
    {
        // Validación de la venta y los productos
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'subTotal' => 'required|numeric',
            'IGV' => 'required|numeric',
            'montoTotal' => 'required|numeric',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
        ]);

        // ==================== VERIFICAR CAJA (SI LA VENTA NO TIENE) ====================
        if (!$venta->caja_id) {
            $cajaHoy = Caja::whereDate('fecha', today())->first();

            if (!$cajaHoy) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Debes abrir caja primero antes de actualizar ventas.');
            }

            $venta->caja_id = $cajaHoy->id;
        }
        // ===============================================================================

        // Obtener los detalles actuales de la venta
        $detallesAntiguos = $venta->detalles;

        // Actualizar la venta con los datos principales
        $venta->update([
            'cliente_id' => $validated['cliente_id'],
            'subTotal' => $validated['subTotal'],
            'IGV' => $validated['IGV'],
            'montoTotal' => $validated['montoTotal'],
        ]);

        // Almacenar IDs de productos existentes en la venta
        $productoIdsActuales = $detallesAntiguos->pluck('producto_id')->toArray();

        // PRIMERO: VALIDAR STOCK PARA TODOS LOS CAMBIOS
        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            
            if (!$producto) {
                return redirect()->back()->with('error', 'Producto no encontrado.');
            }

            // Buscar el detalle de venta anterior
            $detalleAntiguo = $detallesAntiguos->firstWhere('producto_id', $producto->id);
            $cantidadAntigua = $detalleAntiguo ? $detalleAntiguo->cantidad : 0;

            // Calcular la diferencia en la cantidad
            $diferencia = $productoData['cantidad'] - $cantidadAntigua;

            // Validar stock si se está aumentando la cantidad
            if ($diferencia > 0) {
                if ($producto->stockP < $diferencia) {
                    return redirect()->back()->with('error', 
                        "Stock insuficiente para {$producto->descripcionP}. Stock disponible: {$producto->stockP}, adicional solicitado: {$diferencia}");
                }
            }
        }

        // SEGUNDO: Actualizar detalles de la venta con los nuevos productos seleccionados
        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);

            // Buscar el detalle de venta anterior
            $detalleAntiguo = $detallesAntiguos->firstWhere('producto_id', $producto->id);
            $cantidadAntigua = $detalleAntiguo ? $detalleAntiguo->cantidad : 0;

            // Calcular la diferencia en la cantidad
            $diferencia = $productoData['cantidad'] - $cantidadAntigua;

            // Ajustar el stock del producto según la diferencia (YA VALIDADO)
            if ($diferencia !== 0) {
                $producto->stockP -= $diferencia;
                $producto->save();
            }

            // Crear o actualizar el detalle de la venta
            if ($detalleAntiguo) {
                $detalleAntiguo->update([
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $producto->precioP,
                    'subtotal' => $productoData['cantidad'] * $producto->precioP,
                ]);
            } else {
                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $producto->precioP,
                    'subtotal' => $productoData['cantidad'] * $producto->precioP,
                ]);
            }
        }

        // Verificar qué productos han sido eliminados
        $nuevosProductoIds = collect($request->productos)->pluck('id')->toArray();
        $productosEliminados = array_diff($productoIdsActuales, $nuevosProductoIds);

        // Actualizar el stock de los productos eliminados
        foreach ($productosEliminados as $productoId) {
            $detalleAntiguo = $detallesAntiguos->firstWhere('producto_id', $productoId);
            if ($detalleAntiguo) {
                $producto = Producto::find($productoId);
                $producto->stockP += $detalleAntiguo->cantidad;
                $producto->save();

                $detalleAntiguo->delete();
            }
        }

        // NOTA: Si la venta cambia de montoTotal, la caja NO se actualiza automáticamente
        // porque sería complejo llevar seguimiento de cambios. Solo se actualiza en creación/anulación.

        return redirect()->route('pagos.create', ['id' => $venta->id, 'type' => 'venta'])->with('success', 'Venta actualizada con éxito.');
    }

    public function destroy(Venta $venta)
    {
        // IMPORTANTE: Antes de eliminar, considerar si es mejor anular
        // La eliminación física NO actualizará la caja automáticamente
        $venta->delete();
        return redirect()->route('ventas.index')->with('success', 'Venta eliminada con éxito.');
    }

    public function calcularTotales(Request $request)
    {
        $subtotal = 0;
        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            $subtotal += $productoData['cantidad'] * $producto->precio;
        }

        $igv = $subtotal * 0.18;
        $montoTotal = $subtotal + $igv;

        return response()->json([
            'subtotal' => number_format($subtotal, 2),
            'IGV' => number_format($igv, 2),
            'montoTotal' => number_format($montoTotal, 2),
        ]);
    }

    public function anularVenta($id)
    {
        $venta = Venta::findOrFail($id);
        if ($venta->estadoTransaccion->descripcionET !== 'Anulado') {
            $venta->anular(); // ← Esto activará el evento 'updated' que resta de la caja
            return redirect()->route('ventas.index')->with('success', 'Venta anulada correctamente.');
        } else {
            return redirect()->route('ventas.index')->with('error', 'La venta ya está anulada.');
        }
    }
}
