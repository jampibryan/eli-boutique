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

        $esFactura = false;
        if ($type === 'venta') {
            $comprobante = Comprobante::find($request->comprobante_id);
            $esFactura = ($comprobante && strcasecmp($comprobante->descripcionCOM, 'Factura') === 0);

            if ($esFactura) {
                $request->validate([
                    'ruc_factura' => 'required|regex:/^\d{11}$/',
                    'razon_social_factura' => 'required|string|max:255',
                ], [
                    'ruc_factura.required' => 'El RUC es obligatorio para emitir una Factura.',
                    'ruc_factura.regex' => 'El RUC debe tener exactamente 11 dígitos.',
                    'razon_social_factura.required' => 'La Razón Social es obligatoria para emitir una Factura.',
                ]);
            }
        }

        $pagoService->register($ventaId, $type, $validated);

        if ($type === 'venta') {
            session()->forget('carrito');
            session()->forget('venta_cliente');

            // Guardar RUC y Razón Social en la venta si es Factura
            $venta = Venta::find($ventaId);
            if ($venta) {
                if ($esFactura) {
                    $venta->update([
                        'ruc_factura' => $request->ruc_factura,
                        'razon_social_factura' => $request->razon_social_factura,
                    ]);
                }

                // Dispatch Job de transmisión asíncrona a SUNAT
                \App\Jobs\SendVentaToSunatJob::dispatch($venta);
            }
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
