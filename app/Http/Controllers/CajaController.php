<?php

namespace App\Http\Controllers;

use App\Models\Caja;
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

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Caja.informeCaja', compact('caja', 'ventas', 'clientes', 'productosVendidos')));
        $nombreArchivo = 'Informe_Caja_' . $caja->codigoCaja . '.pdf';
        return $pdf->stream($nombreArchivo);
    }
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:ver cajas|gestionar cajas', ['only' => ['index', 'pdfCajas']]);
        $this->middleware('permission:gestionar cajas', ['only' => ['abrirCaja', 'cerrarCaja']]);
    }

    public function pdfCajas()
    {
        $cajas = Caja::All();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML(view('Caja.reporte', compact('cajas')));

        // return $pdf->download(); //Descarga automática
        return $pdf->stream('Reporte de Cajas.pdf'); //Abre una pestaña
    }

    public function index()
    {
        $cajas = Caja::all();
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
