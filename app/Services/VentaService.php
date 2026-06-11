<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\EstadoTransaccion;
use App\Models\Producto;
use App\Models\ProductoTalla;
use App\Models\ProductoTallaStock;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaService
{
    public function create(array $validated): Venta
    {
        return DB::transaction(function () use ($validated) {
            $cajaHoy = Caja::whereDate('fecha', today())
                ->lockForUpdate()
                ->first();

            if (!$cajaHoy) {
                throw ValidationException::withMessages([
                    'caja' => 'No se puede registrar ventas. Debes abrir la caja primero.',
                ]);
            }

            if ($cajaHoy->hora_cierre) {
                throw ValidationException::withMessages([
                    'caja' => 'No se puede registrar ventas. La caja del dia ya esta cerrada.',
                ]);
            }

            $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();

            if (!$estadoPendiente) {
                throw ValidationException::withMessages([
                    'estado' => 'No se encontro el estado Pendiente. Configura los estados de transaccion primero.',
                ]);
            }

            $detallesVenta = [];

            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::find($productoData['id']);

                if (!$producto) {
                    throw ValidationException::withMessages([
                        'productos' => 'Producto no encontrado.',
                    ]);
                }

                $tallaStock = ProductoTallaStock::where('producto_id', $productoData['id'])
                    ->where('producto_talla_id', $productoData['talla_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$tallaStock) {
                    throw ValidationException::withMessages([
                        'productos' => "No se encontro stock para la talla seleccionada de {$producto->descripcionP}.",
                    ]);
                }

                if ($tallaStock->stock < $productoData['cantidad']) {
                    $tallaNombre = ProductoTalla::find($productoData['talla_id'])->descripcion ?? '';

                    throw ValidationException::withMessages([
                        'productos' => "Stock insuficiente para {$producto->descripcionP} talla {$tallaNombre}. Stock disponible: {$tallaStock->stock}, solicitado: {$productoData['cantidad']}",
                    ]);
                }

                $detallesVenta[] = [
                    'producto' => $producto,
                    'talla_id' => $productoData['talla_id'],
                    'talla_stock' => $tallaStock,
                    'cantidad' => $productoData['cantidad'],
                ];
            }

            $montoTotal = $validated['montoTotal'];
            $baseImponible = round($montoTotal / 1.18, 2);
            $igv = round($montoTotal - $baseImponible, 2);

            $colaboradorId = $validated['colaborador_id'] ?? null;
            if (!$colaboradorId && auth()->check()) {
                $user = auth()->user();
                $colaborador = \App\Models\Colaborador::where('correoColab', $user->email)->first();
                if ($colaborador) {
                    $colaboradorId = $colaborador->id;
                }
            }

            $venta = Venta::create([
                'cliente_id' => $validated['cliente_id'],
                'caja_id' => $cajaHoy->id,
                'estado_transaccion_id' => $estadoPendiente->id,
                'subTotal' => $baseImponible,
                'IGV' => $igv,
                'montoTotal' => $montoTotal,
                'colaborador_id' => $colaboradorId,
            ]);

            foreach ($detallesVenta as $detalleVenta) {
                $precioConIGV = $detalleVenta['producto']->precioP;
                $baseImponible = round($precioConIGV / 1.18, 2);
                $igv = round($precioConIGV - $baseImponible, 2);

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $detalleVenta['producto']->id,
                    'producto_talla_id' => $detalleVenta['talla_id'],
                    'cantidad' => $detalleVenta['cantidad'],
                    'precio_unitario' => $precioConIGV,
                    'base_imponible' => $baseImponible,
                    'igv' => $igv,
                    'subtotal' => $detalleVenta['cantidad'] * $precioConIGV,
                ]);

                $detalleVenta['talla_stock']->decrement('stock', $detalleVenta['cantidad']);
            }

            return $venta;
        });
    }

    public function update(Venta $venta, array $validated): Venta
    {
        return DB::transaction(function () use ($venta, $validated) {
            $venta->loadMissing('estadoTransaccion');

            if ($venta->estadoTransaccion->descripcionET !== 'Pendiente') {
                throw ValidationException::withMessages([
                    'venta' => 'No se puede editar una venta ya pagada.',
                ]);
            }

            if ($venta->detalles()->whereNull('producto_talla_id')->exists()) {
                throw ValidationException::withMessages([
                    'venta' => 'No se puede editar esta venta porque tiene detalles antiguos sin talla registrada.',
                ]);
            }

            if (!$venta->caja_id) {
                $cajaHoy = Caja::whereDate('fecha', today())
                    ->lockForUpdate()
                    ->first();

                if (!$cajaHoy) {
                    throw ValidationException::withMessages([
                        'caja' => 'Debes abrir caja primero antes de actualizar ventas.',
                    ]);
                }

                $venta->caja_id = $cajaHoy->id;
            }

            $montoTotal = $validated['montoTotal'];
            $baseImponible = round($montoTotal / 1.18, 2);
            $igv = round($montoTotal - $baseImponible, 2);
            $detallesAntiguos = $venta->detalles()->get();
            $lineasAntiguas = $this->normalizarLineas(
                $detallesAntiguos->map(function ($detalle) {
                    return [
                        'id' => $detalle->producto_id,
                        'talla_id' => $detalle->producto_talla_id,
                        'cantidad' => $detalle->cantidad,
                    ];
                })->all()
            );
            $lineasNuevas = $this->normalizarLineas($validated['productos']);
            $productos = Producto::whereIn('id', collect($lineasNuevas)->pluck('id'))->get()->keyBy('id');
            $stocksPorTalla = $this->obtenerStocksPorTallaBloqueados(
                array_merge(array_values($lineasAntiguas), array_values($lineasNuevas))
            );

            foreach ($lineasNuevas as $key => $lineaNueva) {
                $producto = $productos->get($lineaNueva['id']);

                if (!$producto) {
                    throw ValidationException::withMessages([
                        'productos' => 'Producto no encontrado.',
                    ]);
                }

                $stockTalla = $stocksPorTalla[$key] ?? null;

                if (!$stockTalla) {
                    throw ValidationException::withMessages([
                        'productos' => "No se encontro stock para la talla seleccionada de {$producto->descripcionP}.",
                    ]);
                }

                $cantidadAnterior = $lineasAntiguas[$key]['cantidad'] ?? 0;
                $stockDisponible = $stockTalla->stock + $cantidadAnterior;

                if ($stockDisponible < $lineaNueva['cantidad']) {
                    $tallaNombre = ProductoTalla::find($lineaNueva['talla_id'])->descripcion ?? '';

                    throw ValidationException::withMessages([
                        'productos' => "Stock insuficiente para {$producto->descripcionP} talla {$tallaNombre}. Stock disponible: {$stockDisponible}, solicitado: {$lineaNueva['cantidad']}",
                    ]);
                }
            }

            $venta->update([
                'cliente_id' => $validated['cliente_id'],
                'caja_id' => $venta->caja_id,
                'subTotal' => $baseImponible,
                'IGV' => $igv,
                'montoTotal' => $montoTotal,
            ]);

            foreach ($lineasAntiguas as $key => $lineaAntigua) {
                $stockTalla = $stocksPorTalla[$key] ?? null;

                if ($stockTalla) {
                    $stockTalla->increment('stock', $lineaAntigua['cantidad']);
                }
            }

            $venta->detalles()->delete();

            foreach ($lineasNuevas as $key => $lineaNueva) {
                $producto = $productos->get($lineaNueva['id']);
                $precioConIGV = $producto->precioP;
                $baseImponibleLinea = round($precioConIGV / 1.18, 2);
                $igvLinea = round($precioConIGV - $baseImponibleLinea, 2);

                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'producto_talla_id' => $lineaNueva['talla_id'],
                    'cantidad' => $lineaNueva['cantidad'],
                    'precio_unitario' => $precioConIGV,
                    'base_imponible' => $baseImponibleLinea,
                    'igv' => $igvLinea,
                    'subtotal' => $lineaNueva['cantidad'] * $precioConIGV,
                ]);

                $stocksPorTalla[$key]->decrement('stock', $lineaNueva['cantidad']);
            }

            return $venta;
        });
    }

    private function normalizarLineas(array $lineas): array
    {
        $lineasNormalizadas = [];

        foreach ($lineas as $linea) {
            $key = $this->crearClaveLinea((int) $linea['id'], (int) $linea['talla_id']);

            if (!isset($lineasNormalizadas[$key])) {
                $lineasNormalizadas[$key] = [
                    'id' => (int) $linea['id'],
                    'talla_id' => (int) $linea['talla_id'],
                    'cantidad' => 0,
                ];
            }

            $lineasNormalizadas[$key]['cantidad'] += (int) $linea['cantidad'];
        }

        return $lineasNormalizadas;
    }

    private function obtenerStocksPorTallaBloqueados(array $lineas): array
    {
        $productoIds = collect($lineas)->pluck('id')->unique()->values();
        $tallaIds = collect($lineas)->pluck('talla_id')->filter()->unique()->values();

        return ProductoTallaStock::whereIn('producto_id', $productoIds)
            ->whereIn('producto_talla_id', $tallaIds)
            ->lockForUpdate()
            ->get()
            ->keyBy(function ($stock) {
                return $this->crearClaveLinea($stock->producto_id, $stock->producto_talla_id);
            })
            ->all();
    }

    private function crearClaveLinea(int $productoId, int $tallaId): string
    {
        return $productoId . ':' . $tallaId;
    }
}
