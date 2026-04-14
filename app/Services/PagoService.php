<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\EstadoTransaccion;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PagoService
{
    public function register(int $transactionId, string $type, array $validated): void
    {
        DB::transaction(function () use ($transactionId, $type, $validated) {
            if ($type === 'venta') {
                $this->registerSalePayment($transactionId, $validated);
                return;
            }

            if ($type !== 'compra') {
                throw ValidationException::withMessages([
                    'tipo' => 'Tipo de transaccion no valido.',
                ]);
            }

            $this->registerPurchasePayment($transactionId, $validated);
        });
    }

    private function registerSalePayment(int $ventaId, array $validated): void
    {
        $venta = Venta::with('pago')->findOrFail($ventaId);

        if ($venta->pago) {
            throw ValidationException::withMessages([
                'pago' => 'La venta ya tiene un pago registrado.',
            ]);
        }

        $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();

        if (!$estadoPagado) {
            throw ValidationException::withMessages([
                'estado' => 'No se encontro el estado Pagado.',
            ]);
        }

        $vuelto = $validated['importe'] - $venta->montoTotal;

        if ($vuelto < 0) {
            throw ValidationException::withMessages([
                'importe' => 'El importe no puede ser menor al monto total de la venta.',
            ]);
        }

        $venta->update([
            'estado_transaccion_id' => $estadoPagado->id,
        ]);

        Pago::create([
            'venta_id' => $venta->id,
            'compra_id' => null,
            'comprobante_id' => $validated['comprobante_id'],
            'importe' => $validated['importe'],
            'vuelto' => $vuelto,
        ]);
    }

    private function registerPurchasePayment(int $compraId, array $validated): void
    {
        $compra = Compra::with(['estadoTransaccion', 'pago'])->findOrFail($compraId);

        if ($compra->estadoTransaccion->descripcionET !== 'Recibida') {
            throw ValidationException::withMessages([
                'compra' => 'Solo se pueden pagar ordenes que ya fueron recibidas y verificadas.',
            ]);
        }

        if ($compra->pago) {
            throw ValidationException::withMessages([
                'pago' => 'La compra ya tiene un pago registrado.',
            ]);
        }

        $estadoPagada = EstadoTransaccion::where('descripcionET', 'Pagada')->first();

        if (!$estadoPagada) {
            throw ValidationException::withMessages([
                'estado' => 'No se encontro el estado Pagada.',
            ]);
        }

        $pago = Pago::create([
            'venta_id' => null,
            'compra_id' => $compra->id,
            'comprobante_id' => $validated['comprobante_id'],
            'importe' => $validated['importe'],
            'vuelto' => null,
        ]);

        $compra->update([
            'comprobante_id' => $validated['comprobante_id'],
            'estado_transaccion_id' => $estadoPagada->id,
        ]);

        $cajaHoy = Caja::whereDate('fecha', now()->toDateString())
            ->lockForUpdate()
            ->first();

        if ($cajaHoy) {
            $cajaHoy->increment('egresoDiario', $pago->importe);
        }
    }
}
