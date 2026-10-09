<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\EstadoTransaccion;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $cajaHoy = Caja::whereDate('fecha', now()->toDateString())
            ->lockForUpdate()
            ->first();

        if (!$cajaHoy) {
            throw ValidationException::withMessages([
                'caja' => 'No se puede registrar el pago. Debes abrir la caja primero.',
            ]);
        }

        if ($cajaHoy->hora_cierre) {
            throw ValidationException::withMessages([
                'caja' => 'No se puede registrar el pago. La caja del dia ya esta cerrada.',
            ]);
        }

        if ($venta->caja_id !== $cajaHoy->id) {
            $venta->caja_id = $cajaHoy->id;
        }

        $updateData = [
            'caja_id' => $venta->caja_id,
            'estado_transaccion_id' => $estadoPagado->id,
        ];

        if (!empty($validated['ruc_factura'])) {
            $updateData['ruc_factura'] = $validated['ruc_factura'];
        }

        if (!empty($validated['razon_social_factura'])) {
            $updateData['razon_social_factura'] = $validated['razon_social_factura'];
        }

        $venta->update($updateData);

        $pago = Pago::create([
            'venta_id' => $venta->id,
            'compra_id' => null,
            'comprobante_id' => $validated['comprobante_id'],
            'importe' => $validated['importe'],
            'vuelto' => $vuelto,
        ]);

        Log::info('Pago de venta registrado exitosamente (SSOT Pagado)', [
            'venta_id' => $venta->id,
            'codigo_venta' => $venta->codigoVenta,
            'caja_id' => $cajaHoy->id,
            'importe' => $validated['importe'],
            'vuelto' => $vuelto,
            'monto_total' => $venta->montoTotal,
            'es_factura' => !empty($validated['ruc_factura']),
            'ruc' => $validated['ruc_factura'] ?? null,
            'usuario_id' => auth()->id(),
            'timestamp_pet' => now()->toDateTimeString(),
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

        $cajaHoy = Caja::whereDate('fecha', now()->toDateString())
            ->lockForUpdate()
            ->first();

        if (!$cajaHoy) {
            throw ValidationException::withMessages([
                'caja' => 'No se puede registrar el pago de la compra. Debes abrir la caja primero.',
            ]);
        }

        if ($cajaHoy->hora_cierre) {
            throw ValidationException::withMessages([
                'caja' => 'No se puede registrar el pago de la compra. La caja del dia ya esta cerrada.',
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

        $cajaHoy->increment('egresoDiario', $pago->importe);

        Log::info('Pago de orden de compra registrado exitosamente (SSOT Pagada)', [
            'compra_id' => $compra->id,
            'codigo_compra' => $compra->codigoCompra,
            'caja_id' => $cajaHoy->id,
            'importe' => $pago->importe,
            'usuario_id' => auth()->id(),
            'timestamp_pet' => now()->toDateTimeString(),
        ]);
    }
}
