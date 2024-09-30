<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\EstadoVenta;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;

class PagoController extends Controller
{

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($ventaId)
    {
        // Obtén la venta y todos los comprobantes disponibles
        $venta = Venta::findOrFail($ventaId);
        $comprobantes = Comprobante::all();

        // Retorna la vista de creación de pago con los datos necesarios
        return view('pago.create', compact('venta', 'comprobantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $ventaId)
    {
        // Validación de datos
        $request->validate([
            'comprobante_id' => 'required|exists:comprobantes,id',
            'importeRecibido' => 'required|numeric|min:0',
        ]);

        // Obtener la venta
        $venta = Venta::findOrFail($ventaId);

        // Obtener el estado "Pagado"
        $estadoPagado = EstadoVenta::where('descripcionEV', 'Pagado')->first();

        // Actualizar el estado de la venta a "Pagado"
        if ($estadoPagado) {
            $venta->estado_venta_id = $estadoPagado->id;
            $venta->save();
        }

        // Calcular el vuelto
        $montoTotal = $venta->montoTotal;
        $importeRecibido = $request->input('importeRecibido');
        $vuelto = $importeRecibido - $montoTotal;

        // Crear el pago
        $pago = new Pago();
        $pago->venta_id = $venta->id;
        $pago->comprobante_id = $request->input('comprobante_id');
        $pago->importeRecibido = $importeRecibido;
        $pago->vuelto = $vuelto;
        $pago->save();

        // Redirigir con éxito
        return redirect()->route('ventas.index')->with('success', 'Pago registrado con éxito.');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
