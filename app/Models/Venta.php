<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'caja_id',
        'codigoVenta',
        'cliente_id',
        'estado_transaccion_id',
        'subTotal',
        'IGV',
        'montoTotal',
    ];

    // App\Models\Venta.php
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($venta) {
            // Estado predeterminado "Pendiente"
            if (!$venta->estado_transaccion_id) {
                $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();
                $venta->estado_transaccion_id = $estadoPendiente->id;
            }

            // Generar código de venta
            $ultimoCodigo = Venta::max('codigoVenta');
            $nuevoCodigo = str_pad((int)$ultimoCodigo + 1, 7, '0', STR_PAD_LEFT);
            $venta->codigoVenta = $nuevoCodigo;
        });

        // ==================== EVENTO: AL CREAR VENTA ====================
        static::created(function ($venta) {
            // SOLO sumar a caja si el estado es "Pagado"
            if ($venta->caja && $venta->estadoTransaccion->descripcionET === 'Pagado') {
                $venta->caja->increment('clientesHoy', 1);
                $venta->caja->increment('ingresoDiario', $venta->montoTotal);

                $totalProductosVenta = $venta->detalles()->sum('cantidad');
                $venta->caja->increment('productosVendidos', $totalProductosVenta);
            }
        });

        // ==================== EVENTO: AL ACTUALIZAR VENTA ====================
        static::updated(function ($venta) {
            $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
            $estadoAnulado = EstadoTransaccion::where('descripcionET', 'Anulado')->first();

            // Caso 1: Cambió de Pendiente → Pagado (SUMA a caja)
            if (
                $venta->wasChanged('estado_transaccion_id') &&
                $venta->estado_transaccion_id == $estadoPagado->id &&
                $venta->caja
            ) {

                $venta->caja->increment('clientesHoy', 1);
                $venta->caja->increment('ingresoDiario', $venta->montoTotal);

                $totalProductosVenta = $venta->detalles()->sum('cantidad');
                $venta->caja->increment('productosVendidos', $totalProductosVenta);
            }

            // Caso 2: Cambió de Pagado → Anulado (RESTA de caja)
            if (
                $venta->wasChanged('estado_transaccion_id') &&
                $venta->estado_transaccion_id == $estadoAnulado->id &&
                $venta->caja
            ) {

                $venta->caja->decrement('clientesHoy', 1);
                $venta->caja->decrement('ingresoDiario', $venta->montoTotal);

                $totalProductosVenta = $venta->detalles()->sum('cantidad');
                $venta->caja->decrement('productosVendidos', $totalProductosVenta);
            }
        });
    }

    // ==================== RELACIONES ====================
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function estadoTransaccion()
    {
        return $this->belongsTo(EstadoTransaccion::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    public function anular()
    {
        // Cambiar el estado de la venta a "Anulado"
        $estadoAnulado = EstadoTransaccion::where('descripcionET', 'Anulado')->first();
        if ($estadoAnulado) {
            $this->estado_transaccion_id = $estadoAnulado->id;
            $this->save();
        }

        // Devolver los productos al stock por talla
        foreach ($this->detalles as $detalle) {
            $productoTallaStock = \App\Models\ProductoTallaStock::where('producto_id', $detalle->producto_id)
                ->where('producto_talla_id', $detalle->producto_talla_id)
                ->first();
            
            if ($productoTallaStock) {
                $productoTallaStock->stock += $detalle->cantidad;
                $productoTallaStock->save();
            }
        }

        // NOTA: La resta de la caja se hace automáticamente en el evento 'updated'
    }
}
