<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\EstadoTransaccion;
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
    public function create($ventaId, $type = 'venta')
    {
        // Obtén la venta y todos los comprobantes disponibles
        if ($type === 'venta') {
            $transaction = Venta::findOrFail($ventaId);
        } else {
            $transaction = Compra::findOrFail($ventaId);
        }

        $comprobantes = Comprobante::all();

        // Retorna la vista de creación de pago con los datos necesarios
        return view('Pago.create', compact('transaction', 'comprobantes', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $ventaId, $type = 'venta')
    {
        // Validación de datos
        $request->validate([
            'comprobante_id' => 'required|exists:comprobantes,id',
            'importe' => 'required|numeric|min:0',
        ]);

        if ($type === 'venta') {
            $transaction = Venta::findOrFail($ventaId);
            $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
    
            if ($estadoPagado) {
                $transaction->estado_transaccion_id = $estadoPagado->id;
                $transaction->save();
            }
    
            // Calcular el vuelto solo si es una venta
            $montoTotal = $transaction->montoTotal;
            $importe = $request->input('importe');
            $vuelto = $importe - $montoTotal;
        } else {
            $transaction = Compra::findOrFail($ventaId);
            $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first(); // Obtener el estado "Pagado"
            
            if ($estadoPagado) {
                $transaction->estado_transaccion_id = $estadoPagado->id; // Cambiar el estado de la compra a "Pagado"
                $transaction->save();
            }
    
            $vuelto = null; // No se necesita para compras
        }


        // Crear el pago
        $pago = new Pago();
        $pago->venta_id = $type === 'venta' ? $transaction->id : null;
        $pago->compra_id = $type === 'compra' ? $transaction->id : null;
        $pago->comprobante_id = $request->input('comprobante_id');
        $pago->importe = $request->input('importe');
        $pago->vuelto = $vuelto;
        $pago->save();

        // Limpiar carrito solo después de pagar la venta
        if ($type === 'venta') {
            session()->forget('carrito');
            session()->forget('venta_cliente');
        }

        // Redirigir a la página correspondiente
        $redirectRoute = $type === 'venta' ? 'ventas.index' : 'compras.index';
        return redirect()->route($redirectRoute)->with('success', 'Pago registrado con éxito.');
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
