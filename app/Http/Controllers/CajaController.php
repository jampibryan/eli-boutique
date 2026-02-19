<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CajaController extends Controller
{
    /**
     * Muestra el informe detallado de una caja (ventas, clientes, productos, cantidades, etc.)
     */
    public function informe(Caja $caja)
    {
        // Obtener ventas del día de la caja
        $ventas = $caja->ventas()->with(['cliente', 'detalles.producto'])->get();

        // Clientes únicos
        $clientes = $ventas->pluck('cliente')->unique('id');

        // Productos vendidos y cantidades
        $productosVendidos = collect();
        foreach ($ventas as $venta) {
            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;
                if ($producto) {
                    $existente = $productosVendidos->firstWhere('id', $producto->id);
                    if ($existente) {
                        $existente->cantidad += $detalle->cantidad;
                    } else {
                        $productosVendidos->push((object)[
                            'id' => $producto->id,
                            'descripcion' => $producto->descripcionP,
                            'cantidad' => $detalle->cantidad
                        ]);
                    }
                }
            }
        }

        // Obtener compras pagadas del mismo día
        $compras = Compra::whereHas('pago', function ($q) use ($caja) {
                $q->whereDate('created_at', $caja->fecha);
            })
            ->whereHas('estadoTransaccion', function ($q) {
                $q->whereIn('descripcionET', ['Pagada', 'Recibida']);
            })
            ->with(['proveedor', 'pago', 'detalles.producto'])
            ->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Caja.informeCaja', compact('caja', 'ventas', 'clientes', 'productosVendidos', 'compras')));
        $nombreArchivo = 'Informe_Caja_' . $caja->codigoCaja . '.pdf';
        return $pdf->stream($nombreArchivo);
    }
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:ver cajas|gestionar cajas', ['only' => ['index', 'pdfCajas']]);
        $this->middleware('permission:gestionar cajas', ['only' => ['abrirCaja', 'cerrarCaja']]);
    }

    public function pdfCajas(Request $request)
    {
        $query = Caja::query();

        if ($request->filled('desde')) {
            $query->where('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', $request->hasta);
        }

        $cajas = $query->orderBy('fecha', 'asc')->get();
        $desde = $request->desde;
        $hasta = $request->hasta;

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Caja.reporteGeneralCaja', compact('cajas', 'desde', 'hasta')));

        return $pdf->stream('Reporte de Cajas.pdf');
    }

    public function index(Request $request)
    {
        $query = Caja::query();

        if ($request->filled('search')) {
            $query->where('codigoCaja', 'like', "%{$request->search}%");
        }

        if ($request->filled('desde')) {
            $query->where('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->where('fecha', '<=', $request->hasta);
        }

        $orden = $request->get('orden', 'recientes');
        $query->orderBy('fecha', $orden === 'recientes' ? 'desc' : 'asc');

        $cajas = $query->paginate(4)->appends($request->query());
        return view('Caja.index', compact('cajas'));
    }

    public function abrirCaja(Request $request)
    {
        // Verificar si ya existe una caja abierta para hoy
        $fechaHoy = now()->toDateString();
        $cajaAbierta = Caja::where('fecha', $fechaHoy)->first();

        if ($cajaAbierta) {
            return redirect()->back()->with('error', 'La caja ya está abierta para hoy.');
        }

        // Crear un nuevo registro de caja
        $caja = Caja::create([
            'fecha' => $fechaHoy,
            'clientesHoy' => 0,
            'productosVendidos' => 0,
            'ingresoDiario' => 0.00,
            'egresoDiario' => 0.00,
        ]);

        return redirect()->back()->with('success', 'Caja abierta con éxito. Código de caja: ' . $caja->codigoCaja);
    }

    public function cerrarCaja(Request $request)
    {
        $caja = Caja::whereDate('fecha', now()->toDateString())->first();

        if (!$caja) {
            return redirect()->back()->with('error', 'No hay una caja abierta para cerrar.');
        }

        if ($caja->hora_cierre) {
            return redirect()->back()->with('error', 'La caja ya está cerrada.');
        }

        // Actualizar datos de cierre
        $caja->update([
            'hora_cierre' => now()->format('H:i:s'),
            'clientesHoy' => $request->clientesHoy ?? 0,
            'productosVendidos' => $request->productosVendidos ?? 0,
            'ingresoDiario' => $request->ingresoDiario ?? 0
        ]);

        return redirect()->route('home')->with('success', 'Caja cerrada correctamente.');
    }
}
