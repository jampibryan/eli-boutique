<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Venta;
use App\Services\PagoService;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
    }

    public function create($ventaId, $type = 'venta')
    {
        if ($type === 'venta') {
            $transaction = Venta::findOrFail($ventaId);
            $transaction->montoTotal = $transaction->montoTotal;
        } else {
            $transaction = Compra::with('estadoTransaccion')->findOrFail($ventaId);

            if ($transaction->estadoTransaccion->descripcionET !== 'Recibida') {
                return redirect()->route('compras.index')->with('error', 'Solo se pueden pagar ordenes que ya fueron recibidas y verificadas.');
            }

            $transaction->montoTotal = $transaction->total;
        }

        $comprobantes = Comprobante::all();

        return view('Pago.create', compact('transaction', 'comprobantes', 'type'));
    }

    public function store(Request $request, $ventaId, $type = 'venta', PagoService $pagoService)
    {
        $validated = $request->validate([
            'comprobante_id' => 'required|exists:comprobantes,id',
            'importe' => 'required|numeric|min:0',
        ]);

        $pagoService->register($ventaId, $type, $validated);

        if ($type === 'venta') {
            session()->forget('carrito');
            session()->forget('venta_cliente');
        }

        $redirectRoute = $type === 'venta' ? 'ventas.index' : 'compras.index';

        return redirect()->route($redirectRoute)->with('success', 'Pago registrado con exito.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
