<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:gestionar ventas|crear ventas', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear ventas|gestionar ventas', ['only' => ['create', 'store', 'calcularTotales']]);
        $this->middleware('permission:anular ventas', ['only' => ['anularVenta']]);
        $this->middleware('permission:gestionar ventas', ['only' => ['edit', 'update', 'destroy']]);
    }

    public function apiVentas()
    {
        $ventas = Venta::with(['cliente', 'estadoTransaccion', 'detalles.talla', 'pago.comprobante'])
            ->whereHas('estadoTransaccion')
            ->whereHas('pago.comprobante')
            ->get();

        return response()->json($ventas);
    }

    public function apiVentaDetalle($id)
    {
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles.producto', 'detalles.talla', 'pago.comprobante'])
            ->findOrFail($id);

        return response()->json($venta);
    }

    public function obtenerDatosVentas(Request $request)
    {
        $ventas = Venta::with(['detalles.producto'])->get();
        $resultados = collect();

        foreach ($ventas as $venta) {
            if (!$venta->detalles || $venta->detalles->isEmpty()) {
                continue;
            }

            foreach ($venta->detalles as $detalle) {
                if (!$detalle->producto) {
                    continue;
                }

                $resultados->push([
                    'venta_id' => $venta->id,
                    'producto_id' => $detalle->producto_id,
                    'producto_nombre' => $detalle->producto->descripcionP,
                    'cantidad_vendida' => $detalle->cantidad,
                    'anio' => $venta->created_at->year,
                    'mes' => $venta->created_at->month,
                    'dia' => $venta->created_at->day,
                ]);
            }
        }

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
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles.producto', 'detalles.talla', 'pago.comprobante'])
            ->findOrFail($id);

        $tipoComprobante = $venta->pago->comprobante->descripcionCOM ?? '';

        $pdf = App::make('dompdf.wrapper');
        $view = view('Venta.comprobante', [
            'venta' => $venta,
            'tipoComprobante' => $tipoComprobante,
        ]);

        $pdf->loadHTML($view);

        $nombreArchivo = ($tipoComprobante === 'Factura' ? 'Factura_' : 'Boleta_') . $venta->codigoVenta . '.pdf';

        return $pdf->stream($nombreArchivo);
    }

    public function pdfComprobanteModerno($id)
    {
        $venta = Venta::with(['cliente', 'estadoTransaccion', 'detalles.producto', 'detalles.talla', 'pago.comprobante'])
            ->findOrFail($id);

        $tipoComprobante = $venta->pago->comprobante->descripcionCOM ?? '';

        $pdf = App::make('dompdf.wrapper');
        $view = view('Venta.comprobante_moderno', [
            'venta' => $venta,
            'tipoComprobante' => $tipoComprobante,
        ]);

        $pdf->loadHTML($view);

        $nombreArchivo = ($tipoComprobante === 'Factura' ? 'Factura_' : 'Boleta_') . $venta->codigoVenta . '_moderno.pdf';

        return $pdf->stream($nombreArchivo);
    }

    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'estadoTransaccion', 'detalles.talla', 'pago']);

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
            return redirect()->route('productos.index')->with('error', 'El carrito esta vacio. Agrega productos primero.');
        }

        $clientes = Cliente::withoutTrashed()->get();
        $productos = Producto::whereIn('id', collect($carrito)->pluck('producto_id'))->get()->keyBy('id');
        $tallas = ProductoTalla::whereIn('id', collect($carrito)->pluck('talla_id'))->get()->keyBy('id');

        $stocksPorTalla = [];

        foreach ($carrito as $item) {
            $key = $item['producto_id'] . '_' . $item['talla_id'];
            $tallaStock = ProductoTallaStock::where('producto_id', $item['producto_id'])
                ->where('producto_talla_id', $item['talla_id'])
                ->first();

            $stocksPorTalla[$key] = $tallaStock ? $tallaStock->stock : 0;
        }

        $clienteSeleccionado = null;

        return view('Venta.create', compact('clientes', 'carrito', 'productos', 'tallas', 'stocksPorTalla', 'clienteSeleccionado'));
    }

    public function store(Request $request, VentaService $ventaService)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.talla_id' => 'required|exists:producto_tallas,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'montoTotal' => 'required|numeric',
        ]);

        $venta = $ventaService->create($validated);

        session()->forget('carrito');

        return redirect()->route('pagos.create', ['id' => $venta->id, 'type' => 'venta'])
            ->with('success', 'Venta creada exitosamente. Proceda al pago.');
    }

    public function show(Venta $venta)
    {
        $venta->load(['detalles.producto', 'detalles.talla']);

        return view('Venta.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        if ($venta->estadoTransaccion->descripcionET !== 'Pendiente') {
            return redirect()->route('ventas.index')->with('error', 'No se puede editar una venta ya pagada.');
        }

        if ($venta->detalles()->whereNull('producto_talla_id')->exists()) {
            return redirect()->route('ventas.index')->with(
                'error',
                'No se puede editar esta venta porque tiene detalles antiguos sin talla registrada.'
            );
        }

        $clientes = Cliente::withoutTrashed()->get();
        $productos = Producto::withoutTrashed()->get();
        $tallas = ProductoTalla::all();
        $carrito = [];

        foreach ($venta->detalles as $detalle) {
            $carrito[] = [
                'producto_id' => $detalle->producto_id,
                'talla_id' => $detalle->producto_talla_id ?? ProductoTalla::first()->id,
                'cantidad' => $detalle->cantidad,
            ];
        }

        $clienteSeleccionado = $venta->cliente_id;
        $subtotal = $venta->subTotal;
        $igv = $venta->IGV;
        $montoTotal = $venta->montoTotal;

        return view('Venta.edit', compact('venta', 'clientes', 'productos', 'tallas', 'carrito', 'clienteSeleccionado', 'subtotal', 'igv', 'montoTotal'));
    }

    public function update(Request $request, Venta $venta, VentaService $ventaService)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'montoTotal' => 'required|numeric',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.talla_id' => 'required|exists:producto_tallas,id',
            'productos.*.cantidad' => 'required|numeric|min:1',
        ]);

        $ventaService->update($venta, $validated);

        return redirect()->route('pagos.create', ['id' => $venta->id, 'type' => 'venta'])
            ->with('success', 'Venta actualizada con exito.');
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada con exito.');
    }

    public function calcularTotales(Request $request)
    {
        $total = 0;

        foreach ($request->productos as $productoData) {
            $producto = Producto::find($productoData['id']);
            $total += $productoData['cantidad'] * $producto->precioP;
        }

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
            $venta->anular();

            return redirect()->route('ventas.index')->with('success', 'Venta anulada correctamente.');
        }

        return redirect()->route('ventas.index')->with('error', 'La venta ya esta anulada.');
    }
}
