<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/** Read-only integration contract. Never uses model events or changes stock/cash. */
class AnalyticsSnapshotService
{
    public function snapshot(): array
    {
        $data = [
            'ventas' => $this->sales(),
            'productos' => $this->products(),
            'compras' => $this->purchases(),
            'pagos' => $this->payments(),
            'cajas' => $this->cashboxes(),
        ];
        $marked = (new DemoDataProvenance())->mark($data);
        $data = $marked['data'];

        return [
            'success' => true,
            'schema_version' => '1.0',
            'generated_at' => now()->toIso8601String(),
            // Includes deletions and corrections; generated_at must not invalidate the cache.
            'revision' => hash('sha256', json_encode($data, JSON_THROW_ON_ERROR)),
            'data' => $data,
            'provenance' => $marked['provenance'],
        ];
    }

    private function sales(): array
    {
        return DB::table('venta_detalles as d')
            ->join('ventas as v', 'v.id', '=', 'd.venta_id')
            ->join('estado_transacciones as e', 'e.id', '=', 'v.estado_transaccion_id')
            // Query builder preserves historical references to soft-deleted products.
            ->leftJoin('productos as p', 'p.id', '=', 'd.producto_id')
            ->where('e.descripcionET', 'Pagado')
            ->whereExists(fn ($query) => $query->selectRaw('1')->from('pagos')
                ->whereColumn('pagos.venta_id', 'v.id'))
            ->orderBy('d.id')
            ->get(['d.id', 'd.venta_id', 'd.producto_id', 'd.producto_talla_id',
                'p.descripcionP', 'd.cantidad', 'd.precio_unitario', 'd.costo_unitario',
                'd.subtotal', 'v.created_at'])
            ->map(fn ($row) => [
                'detalle_id' => (int) $row->id,
                'venta_id' => (int) $row->venta_id,
                'producto_id' => (int) $row->producto_id,
                'producto_talla_id' => $this->nullableId($row->producto_talla_id),
                'producto_nombre' => $row->descripcionP ?? 'Producto '.$row->producto_id,
                'fecha' => $this->date($row->created_at),
                'cantidad_vendida' => (int) $row->cantidad,
                'precio_unitario' => (float) $row->precio_unitario,
                'costo_unitario' => (float) $row->costo_unitario,
                'subtotal' => (float) $row->subtotal,
            ])->all();
    }

    private function products(): array
    {
        $stocks = DB::table('producto_talla_stock as s')
            ->leftJoin('producto_tallas as t', 't.id', '=', 's.producto_talla_id')
            ->orderBy('s.id')->get(['s.producto_id', 's.producto_talla_id', 't.descripcion', 's.stock'])
            ->groupBy('producto_id');

        return DB::table('productos')->orderBy('id')
            ->get(['id', 'descripcionP', 'precioP', 'costo_compra', 'deleted_at'])
            ->map(function ($row) use ($stocks) {
                $sizes = $stocks->get($row->id, collect())->map(fn ($size) => [
                    'producto_talla_id' => (int) $size->producto_talla_id,
                    'talla' => $size->descripcion,
                    'stock' => (int) $size->stock,
                ])->values()->all();

                return [
                    'producto_id' => (int) $row->id,
                    'producto_nombre' => $row->descripcionP ?? 'Producto '.$row->id,
                    'activo' => $row->deleted_at === null,
                    'precio_actual' => (float) $row->precioP,
                    'costo_actual' => (float) $row->costo_compra,
                    'stock_total' => array_sum(array_column($sizes, 'stock')),
                    'tallas' => $sizes,
                ];
            })->all();
    }

    private function purchases(): array
    {
        return DB::table('compra_detalles as d')
            ->join('compras as c', 'c.id', '=', 'd.compra_id')
            ->join('estado_transacciones as e', 'e.id', '=', 'c.estado_transaccion_id')
            ->orderBy('d.id')
            ->get(['d.id', 'd.compra_id', 'd.producto_id', 'd.producto_talla_id',
                'e.descripcionET', 'c.created_at', 'd.cantidad', 'd.precio_final', 'd.subtotal_linea'])
            ->map(fn ($row) => [
                'detalle_id' => (int) $row->id,
                'compra_id' => (int) $row->compra_id,
                'producto_id' => (int) $row->producto_id,
                'producto_talla_id' => $this->nullableId($row->producto_talla_id),
                'fecha_orden' => $this->date($row->created_at),
                'estado' => $row->descripcionET,
                'recibida' => in_array($row->descripcionET, ['Recibida', 'Recibido', 'Pagada'], true),
                'cantidad' => (int) $row->cantidad,
                'precio_final' => $row->precio_final === null ? null : (float) $row->precio_final,
                'subtotal_linea' => (float) $row->subtotal_linea,
            ])->all();
    }

    private function payments(): array
    {
        return DB::table('pagos as p')
            ->leftJoin('comprobantes as co', 'co.id', '=', 'p.comprobante_id')
            ->leftJoin('ventas as v', 'v.id', '=', 'p.venta_id')
            ->leftJoin('compras as c', 'c.id', '=', 'p.compra_id')
            ->leftJoin('estado_transacciones as ev', 'ev.id', '=', 'v.estado_transaccion_id')
            ->leftJoin('estado_transacciones as ec', 'ec.id', '=', 'c.estado_transaccion_id')
            ->orderBy('p.id')
            ->get(['p.id', 'p.venta_id', 'p.compra_id', 'p.created_at', 'p.importe',
                'p.vuelto', 'p.metodo_pago', 'co.descripcionCOM as comprobante', 'ev.descripcionET as estado_venta', 'ec.descripcionET as estado_compra'])
            ->map(fn ($row) => [
                'pago_id' => (int) $row->id,
                'venta_id' => $this->nullableId($row->venta_id),
                'compra_id' => $this->nullableId($row->compra_id),
                'fecha' => $this->date($row->created_at),
                'tipo' => $row->venta_id === null ? 'compra' : 'venta',
                'vigente' => $row->venta_id === null ? $row->estado_compra === 'Pagada' : $row->estado_venta === 'Pagado',
                'metodo_pago' => $row->metodo_pago,
                'comprobante' => $row->comprobante,
                'importe' => (float) $row->importe,
                'vuelto' => (float) ($row->vuelto ?? 0),
                'monto_neto' => round((float) $row->importe - (float) ($row->vuelto ?? 0), 2),
            ])->all();
    }

    private function cashboxes(): array
    {
        return DB::table('cajas')->orderBy('id')
            ->get(['id', 'fecha', 'hora_cierre', 'ingresoDiario', 'egresoDiario'])
            ->map(fn ($row) => [
                'caja_id' => (int) $row->id,
                'fecha' => $this->date($row->fecha),
                'cerrada' => $row->hora_cierre !== null,
                'ingresos' => (float) $row->ingresoDiario,
                'egresos' => (float) $row->egresoDiario,
            ])->all();
    }

    private function nullableId($value): ?int
    {
        return $value === null ? null : (int) $value;
    }

    private function date($value): ?string
    {
        return $value === null ? null : Carbon::parse($value)->toDateString();
    }
}
