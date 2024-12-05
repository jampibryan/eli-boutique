<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CajaController extends Controller
{
    public function __construct()
    {
        // Aplicar middleware para verificar permisos
        $this->middleware('permission:ver cajas');
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
        // Actualizar la caja de hoy si existe
        $caja = Caja::whereDate('fecha', now()->toDateString())->first();
        if ($caja) {
            $caja->update([
                'clientesHoy' => $request->input('clientesHoy'),
                'productosVendidos' => $request->input('productosVendidos'),
                'ingresoDiario' => $request->input('ingresoDiario'),
            ]);
            
            // Guardar en sesión que la caja ha sido cerrada
            session(['cajaCerrada' => true]);
        }
        
        return redirect()->route('home')->with('success', 'La caja ha sido cerrada correctamente.');
    }
    
}
