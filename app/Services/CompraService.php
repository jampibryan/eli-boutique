<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\EstadoTransaccion;
use App\Models\ProductoTallaStock;
use Illuminate\Support\Facades\DB;
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

            $compra = Compra::create([
                'proveedor_id' => $validated['proveedor_id'],
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
            $compra->update([
                'proveedor_id' => $validated['proveedor_id'],
            ]);

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

            return $compra;
        });
    }
}
