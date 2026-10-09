<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\EstadoTransaccion;
use App\Models\ProductoTallaStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CompraService
{
    public function create(array $validated): Compra
    {
        return DB::transaction(function () use ($validated) {
            $cajaHoy = Caja::whereDate('fecha', today())
                ->lockForUpdate()
                ->first();

            if (!$cajaHoy) {
                throw ValidationException::withMessages([
                    'caja' => 'No se puede crear ordenes de compra. Debes abrir la caja primero.',
                ]);
            }

            if ($cajaHoy->hora_cierre) {
                throw ValidationException::withMessages([
                    'caja' => 'No se puede crear ordenes de compra. La caja del dia ya esta cerrada.',
                ]);
            }

            $colaboradorId = $validated['colaborador_id'] ?? null;
            if (!$colaboradorId && auth()->check()) {
                $user = auth()->user();
                $colaborador = \App\Models\Colaborador::where('correoColab', $user->email)->first();
                if ($colaborador) {
                    $colaboradorId = $colaborador->id;
                }
            }

            $compra = Compra::create([
                'proveedor_id' => $validated['proveedor_id'],
                'colaborador_id' => $colaboradorId,
            ]);

            foreach ($validated['productos'] as $productoData) {
                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $productoData['id'],
                    'producto_talla_id' => $productoData['talla_id'],
                    'cantidad' => $productoData['cantidad'],
                ]);
            }

            return $compra;
        });
    }

    public function update(Compra $compra, array $validated): Compra
    {
        return DB::transaction(function () use ($compra, $validated) {
            $colaboradorId = $validated['colaborador_id'] ?? null;
            if (!$colaboradorId && auth()->check()) {
                $user = auth()->user();
                $colaborador = \App\Models\Colaborador::where('correoColab', $user->email)->first();
                if ($colaborador) {
                    $colaboradorId = $colaborador->id;
                }
            }

            $updateData = [
                'proveedor_id' => $validated['proveedor_id'],
            ];
            if ($colaboradorId) {
                $updateData['colaborador_id'] = $colaboradorId;
            }

            $compra->update($updateData);

            $compra->detalles()->delete();

            foreach ($validated['productos'] as $productoData) {
                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $productoData['id'],
                    'producto_talla_id' => $productoData['talla_id'],
                    'cantidad' => $productoData['cantidad'],
                ]);
            }

            return $compra;
        });
    }

    public function receive(int $compraId): Compra
    {
        return DB::transaction(function () use ($compraId) {
            $compra = Compra::with(['detalles', 'estadoTransaccion'])->findOrFail($compraId);

            if ($compra->estadoTransaccion->descripcionET !== 'Aprobada') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden recibir ordenes en estado Aprobada.',
                ]);
            }

            $estadoRecibido = EstadoTransaccion::whereIn('descripcionET', ['Recibida', 'Recibido'])->first();

            if ($estadoRecibido) {
                $compra->update([
                    'estado_transaccion_id' => $estadoRecibido->id,
                ]);
            }

            foreach ($compra->detalles as $detalle) {
                $tallaStock = ProductoTallaStock::where('producto_id', $detalle->producto_id)
                    ->where('producto_talla_id', $detalle->producto_talla_id)
                    ->lockForUpdate()
                    ->first();

                if ($tallaStock) {
                    $tallaStock->increment('stock', $detalle->cantidad);
                    continue;
                }

                ProductoTallaStock::create([
                    'producto_id' => $detalle->producto_id,
                    'producto_talla_id' => $detalle->producto_talla_id,
                    'stock' => $detalle->cantidad,
                ]);
            }

            Log::info('Mercadería de orden de compra recibida e ingresada al stock por tallas', [
                'compra_id' => $compra->id,
                'codigo_compra' => $compra->codigoCompra,
                'proveedor_id' => $compra->proveedor_id,
                'total_prendas' => $compra->detalles->sum('cantidad'),
                'usuario_id' => auth()->id(),
                'timestamp_pet' => now()->toDateTimeString(),
            ]);

            return $compra;
        });
    }

    public function send(int|Compra $compra): Compra
    {
        $compraModel = ($compra instanceof Compra) ? $compra : Compra::with('estadoTransaccion')->findOrFail($compra);

        if ($compraModel->estadoTransaccion->descripcionET !== 'Borrador') {
            throw ValidationException::withMessages([
                'compra' => 'Solo se pueden enviar ordenes en estado Borrador.',
            ]);
        }

        $estadoEnviada = EstadoTransaccion::where('descripcionET', 'Enviada')->first();

        if (!$estadoEnviada) {
            throw ValidationException::withMessages([
                'estado' => 'No se encontro el estado Enviada.',
            ]);
        }

        $compraModel->update([
            'estado_transaccion_id' => $estadoEnviada->id,
            'fecha_envio' => now(),
        ]);

        return $compraModel;
    }

    public function saveQuote(int|Compra $compra, array $validated, ?string $pdfPath = null): Compra
    {
        return DB::transaction(function () use ($compra, $validated, $pdfPath) {
            $compraModel = ($compra instanceof Compra) ? $compra : Compra::with(['detalles', 'estadoTransaccion'])->findOrFail($compra);

            if ($compraModel->estadoTransaccion->descripcionET !== 'Enviada') {
                throw ValidationException::withMessages([
                    'compra' => 'Solo se pueden cotizar ordenes en estado Enviada.',
                ]);
            }

            $subtotal = 0;

            foreach ($compraModel->detalles as $detalle) {
                $precioCotizado = $validated['precio_cotizado'][$detalle->id] ?? 0;
                $subtotalLinea = $precioCotizado * $detalle->cantidad;

                $detalle->update([
                    'precio_cotizado' => $precioCotizado,
                    'precio_final' => $precioCotizado,
                    'subtotal_linea' => $subtotalLinea,
                ]);

                $subtotal += $subtotalLinea;
            }

            $descuento = $validated['descuento'] ?? 0;
            $subtotalConDescuento = max(0, $subtotal - $descuento);
            $igv = round($subtotalConDescuento * 0.18, 2);
            $total = round($subtotalConDescuento + $igv, 2);
            $estadoCotizada = EstadoTransaccion::where('descripcionET', 'Cotizada')->first();

            if (!$estadoCotizada) {
                throw ValidationException::withMessages([
                    'estado' => 'No se encontro el estado Cotizada.',
                ]);
            }

            $compraModel->update([
                'estado_transaccion_id' => $estadoCotizada->id,
                'fecha_cotizacion' => now(),
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'igv' => $igv,
                'total' => $total,
                'condiciones_pago' => 'Pago contra entrega',
                'notas_proveedor' => $validated['notas_proveedor'] ?? null,
                'pdf_cotizacion' => $pdfPath ?? $compraModel->pdf_cotizacion,
            ]);

            return $compraModel;
        });
    }

    public function approve(int|Compra $compra): Compra
    {
        $compraModel = ($compra instanceof Compra) ? $compra : Compra::with('estadoTransaccion')->findOrFail($compra);

        if ($compraModel->estadoTransaccion->descripcionET !== 'Cotizada') {
            throw ValidationException::withMessages([
                'compra' => 'Solo se pueden aprobar ordenes en estado Cotizada.',
            ]);
        }

        $estadoAprobada = EstadoTransaccion::where('descripcionET', 'Aprobada')->first();

        if (!$estadoAprobada) {
            throw ValidationException::withMessages([
                'estado' => 'No se encontro el estado Aprobada.',
            ]);
        }

        $compraModel->update([
            'estado_transaccion_id' => $estadoAprobada->id,
            'fecha_aprobacion' => now(),
        ]);

        return $compraModel;
    }

    public function anular(int|Compra $compra): Compra
    {
        return DB::transaction(function () use ($compra) {
            $compraModel = ($compra instanceof Compra) ? $compra : Compra::with('estadoTransaccion')->findOrFail($compra);

            if ($compraModel->estadoTransaccion && $compraModel->estadoTransaccion->descripcionET === 'Anulado') {
                throw ValidationException::withMessages([
                    'compra' => 'La compra ya esta anulada.',
                ]);
            }

            if ($compraModel->estadoTransaccion && in_array($compraModel->estadoTransaccion->descripcionET, ['Recibida', 'Recibido', 'Pagada', 'Pagado'])) {
                throw ValidationException::withMessages([
                    'compra' => 'No se puede anular una orden de compra que ya fue recibida o pagada. El stock e importes ya fueron procesados.',
                ]);
            }

            $estadoAnulado = EstadoTransaccion::where('descripcionET', 'Anulado')->first();

            if (!$estadoAnulado) {
                throw ValidationException::withMessages([
                    'estado' => 'No se encontro el estado Anulado.',
                ]);
            }

            $compraModel->update([
                'estado_transaccion_id' => $estadoAnulado->id,
            ]);

            Log::warning('Orden de compra anulada oficialmente (Trazabilidad SSOT)', [
                'compra_id' => $compraModel->id,
                'codigo_compra' => $compraModel->codigoCompra,
                'proveedor_id' => $compraModel->proveedor_id,
                'usuario_id' => auth()->id(),
                'timestamp_pet' => now()->toDateTimeString(),
            ]);

            return $compraModel;
        });
    }
}
