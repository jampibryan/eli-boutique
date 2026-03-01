<?php
namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class VentaController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:gestionar ventas|crear ventas', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear ventas|gestionar ventas', ['only' => ['create', 'store', 'calcularTotales']]);
        $this->middleware('permission:anular ventas', ['only' => ['anularVenta']]);
        $this->middleware('permission:gestionar ventas', ['only' => ['edit', 'update', 'destroy']]);
    }

    public function apiVentas()
    {
        $ventas = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago.comprobante'])
            ->whereHas('estadoTransaccion')
            ->whereHas('pago.comprobante')
            ->get();

        return response()->json($ventas);
    }

    // Detalle de venta para API (incluye cliente, estado, detalles con producto, pago y comprobante)

    public function apiVentaDetalle($id)
    {
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles.producto', 'pago.comprobante'])
            ->findOrFail($id);

        return response()->json($venta);
    }


    public function obtenerDatosVentas(Request $request)
        {
            // Obtiene todas las ventas con detalles y producto asociado
            $ventas = Venta::with(['detalles.producto'])->get();

            // Estructura de respuesta
            $resultados = collect();

            foreach ($ventas as $venta) {
                // Validar que existan detalles
                if ($venta->detalles && $venta->detalles->count() > 0) {
                    foreach ($venta->detalles as $detalle) {
                        // Validar que exista producto
                        if ($detalle->producto) {
                            $resultados->push([
                                'venta_id' => $venta->id,
                                'producto_id' => $detalle->producto_id,
                                'producto_nombre' => $detalle->producto->descripcionP,
                                'cantidad_vendida' => $detalle->cantidad,
                                'año' => $venta->created_at->year,
                                'mes' => $venta->created_at->month,
                                'dia' => $venta->created_at->day,
                            ]);
                        }
                    }
                }
            }

            // Devuelve la colección como array JSON
            return response()->json($resultados->all());
        }


    public function pdfVentas()
    {
        $ventas = Venta::with(['cliente', 'pago.comprobante', 'estadoTransaccion'])->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Venta.reporte', compact('ventas')));

        return $pdf->stream('Reporte de Ventas.pdf');
    }

    public function pdfComprobante($id)
    {
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago.comprobante'])
            ->findOrFail($id);

        $tipoComprobante = $venta->pago->comprobante->descripcionCOM ?? '';

        $pdf = App::make('dompdf.wrapper');
        $view = view('Venta.comprobante', [
            'venta' => $venta,
            'tipoComprobante' => $tipoComprobante
        ]);
        $pdf->loadHTML($view);
        $nombreArchivo = ($tipoComprobante === 'Factura' ? 'Factura_' : 'Boleta_') . $venta->codigoVenta . '.pdf';
        return $pdf->stream($nombreArchivo);
    }

    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'estadoTransaccion', 'detalles', 'pago']);

        if ($request->filled('search')) {
            $query->where('codigoVenta', 'like', "%{$request->search}%");
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $orden = $request->get('orden', 'reciente');
        $query->orderBy('id', $orden === 'reciente' ? 'desc' : 'asc');

        $ventas = $query->paginate(6)->appends($request->query());
        return view('Venta.index', compact('ventas'));
    }

    public function create()
    {
        $carrito = session()->get('carrito', []);
        if (empty($carrito)) {
            return redirect()->route('productos.index')->with('error', 'El carrito está vacío. Agrega productos primero.');
        }

        $clientes = Cliente::withoutTrashed()->get();
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

        $clienteSeleccionado = null; // Siempre iniciar con cliente vacío

        return view('Venta.create', compact('clientes', 'carrito', 'productos', 'tallas', 'stocksPorTalla', 'clienteSeleccionado'));
    }

    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array',
            'montoTotal' => 'required|numeric',
        ]);

        // ==================== VERIFICAR CAJA ABIERTA ====================
        $cajaHoy = Caja::whereDate('fecha', today())->first();

        if (!$cajaHoy) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se puede registrar ventas. Debes abrir la caja primero.');
        }

        if ($cajaHoy->hora_cierre) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No se puede registrar ventas. La caja del día ya está cerrada.');
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

        // Calcular subTotal e IGV a partir del montoTotal (precio incluye IGV)
        $montoTotal = $request->montoTotal;
        $baseImponible = round($montoTotal / 1.18, 2);
        $igv = round($montoTotal - $baseImponible, 2);

        // Crear una nueva venta CON ESTADO "PENDIENTE"
        $venta = new Venta();
        $venta->cliente_id = $request->cliente_id;
        $venta->caja_id = $cajaHoy->id; // ← ASOCIAR A LA CAJA DEL DÍA
        $venta->estado_transaccion_id = $estadoPendiente->id; // ← ESTADO PENDIENTE (NO PAGADO)
        $venta->subTotal = $baseImponible;
        $venta->IGV = $igv;
        $venta->montoTotal = $montoTotal;
        $venta->save(); // ← LOS EVENTOS NO SUMARÁN A CAJA (porque está pendiente)

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
                return redirect()->back()->with(
                    'error',
                    "No se encontró stock para la talla seleccionada de {$productoValidar->descripcionP}."
                );
            }

            if ($tallaStock->stock < $productoData['cantidad']) {
                $tallaNombre = ProductoTalla::find($productoData['talla_id'])->descripcion ?? '';
                return redirect()->back()->with(
                    'error',
                    "Stock insuficiente para {$productoValidar->descripcionP} talla {$tallaNombre}. Stock disponible: {$tallaStock->stock}, solicitado: {$productoData['cantidad']}"
                );
            }
        }

        // Registrar los productos en la venta
        foreach ($request->productos as $productoData) {
            $productoDetalle = new VentaDetalle();
            $productoDetalle->venta_id = $venta->id;
            $productoDetalle->producto_id = $productoData['id'];
            $productoDetalle->cantidad = $productoData['cantidad'];

            $productoSeleccionado = Producto::find($productoData['id']);
            $precioConIGV = $productoSeleccionado->precioP; // Precio final que ve el cliente
            $baseImponible = round($precioConIGV / 1.18, 2); // Precio sin IGV
            $igv = round($precioConIGV - $baseImponible, 2); // Monto del IGV

            $productoDetalle->precio_unitario = $precioConIGV;
            $productoDetalle->base_imponible = $baseImponible;
            $productoDetalle->igv = $igv;
            $productoDetalle->subtotal = $productoData['cantidad'] * $precioConIGV;

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

        $clientes = Cliente::withoutTrashed()->get();
        $productos = Producto::withoutTrashed()->get();
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

        // Calcular subTotal e IGV a partir del montoTotal (precio incluye IGV)
        $montoTotal = $validated['montoTotal'];
        $baseImponible = round($montoTotal / 1.18, 2);
        $igv = round($montoTotal - $baseImponible, 2);

        // Obtener los detalles actuales de la venta
        $detallesAntiguos = $venta->detalles;

        // Actualizar la venta con los datos principales
        $venta->update([
            'cliente_id' => $validated['cliente_id'],
            'subTotal' => $baseImponible,
            'IGV' => $igv,
            'montoTotal' => $montoTotal,
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
                    return redirect()->back()->with(
                        'error',
                        "Stock insuficiente para {$producto->descripcionP}. Stock disponible: {$producto->stockP}, adicional solicitado: {$diferencia}"
                    );
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

            // Calcular IGV (precio incluye IGV)
            $precioConIGV = $producto->precioP;
            $baseImponible = round($precioConIGV / 1.18, 2);
            $igv = round($precioConIGV - $baseImponible, 2);

            // Crear o actualizar el detalle de la venta
            if ($detalleAntiguo) {
                $detalleAntiguo->update([
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $precioConIGV,
                    'base_imponible' => $baseImponible,
                    'igv' => $igv,
                    'subtotal' => $productoData['cantidad'] * $precioConIGV,
                ]);
            } else {
                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $precioConIGV,
                    'base_imponible' => $baseImponible,
                    'igv' => $igv,
                    'subtotal' => $productoData['cantidad'] * $precioConIGV,
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
        $total = 0;
        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            $total += $productoData['cantidad'] * $producto->precioP;
        }

        // El precio ya incluye IGV, desglosamos
        $montoTotal = $total;
        $baseImponible = round($total / 1.18, 2);
        $igv = round($total - $baseImponible, 2);

        return response()->json([
            'baseImponible' => number_format($baseImponible, 2),
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
