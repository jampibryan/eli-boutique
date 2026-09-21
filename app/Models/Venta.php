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
        'colaborador_id',
        'ruc_factura',
        'razon_social_factura',
    ];

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($venta) {
            if (!$venta->estado_transaccion_id) {
                $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();
                $venta->estado_transaccion_id = $estadoPendiente->id;
            }

            $ultimoCodigo = Venta::max('codigoVenta');
            $nuevoCodigo = str_pad((int) $ultimoCodigo + 1, 7, '0', STR_PAD_LEFT);
            $venta->codigoVenta = $nuevoCodigo;
        });

        static::created(function ($venta) {
            if ($venta->caja && $venta->estadoTransaccion->descripcionET === 'Pagado') {
                $clienteYaContado = Venta::where('caja_id', $venta->caja_id)
                    ->where('cliente_id', $venta->cliente_id)
                    ->where('id', '!=', $venta->id)
                    ->whereHas('estadoTransaccion', fn ($q) => $q->where('descripcionET', 'Pagado'))
                    ->exists();

                if (!$clienteYaContado) {
                    $venta->caja->increment('clientesHoy', 1);
                }

                $venta->caja->increment('ingresoDiario', $venta->montoTotal);

                $totalProductosVenta = $venta->detalles()->sum('cantidad');
                $venta->caja->increment('productosVendidos', $totalProductosVenta);
            }
        });

        static::updated(function ($venta) {
            $estadoPagado = EstadoTransaccion::where('descripcionET', 'Pagado')->first();
            $estadoAnulado = EstadoTransaccion::where('descripcionET', 'Anulado')->first();

            if (
                $venta->wasChanged('estado_transaccion_id') &&
                $venta->estado_transaccion_id == $estadoPagado->id &&
                $venta->caja
            ) {
                $clienteYaContado = Venta::where('caja_id', $venta->caja_id)
                    ->where('cliente_id', $venta->cliente_id)
                    ->where('id', '!=', $venta->id)
                    ->whereHas('estadoTransaccion', fn ($q) => $q->where('descripcionET', 'Pagado'))
                    ->exists();

                if (!$clienteYaContado) {
                    $venta->caja->increment('clientesHoy', 1);
                }

                $venta->caja->increment('ingresoDiario', $venta->montoTotal);

                $totalProductosVenta = $venta->detalles()->sum('cantidad');
                $venta->caja->increment('productosVendidos', $totalProductosVenta);
            }

            $previousEstadoId = $venta->getOriginal('estado_transaccion_id');

            if (
                $venta->wasChanged('estado_transaccion_id') &&
                $venta->estado_transaccion_id == $estadoAnulado->id &&
                $previousEstadoId == $estadoPagado->id &&
                $venta->caja
            ) {
                $clienteTieneOtrasVentas = Venta::where('caja_id', $venta->caja_id)
                    ->where('cliente_id', $venta->cliente_id)
                    ->where('id', '!=', $venta->id)
                    ->whereHas('estadoTransaccion', fn ($q) => $q->where('descripcionET', 'Pagado'))
                    ->exists();

                if (!$clienteTieneOtrasVentas) {
                    $venta->caja->decrement('clientesHoy', 1);
                }

                $venta->caja->decrement('ingresoDiario', $venta->montoTotal);

                $totalProductosVenta = $venta->detalles()->sum('cantidad');
                $venta->caja->decrement('productosVendidos', $totalProductosVenta);
            }
        });

        static::deleting(function ($venta) {
            if ($venta->estadoTransaccion && $venta->estadoTransaccion->descripcionET === 'Pendiente') {
                foreach ($venta->detalles as $detalle) {
                    if ($detalle->producto_talla_id) {
                        $tallaStock = ProductoTallaStock::where('producto_id', $detalle->producto_id)
                            ->where('producto_talla_id', $detalle->producto_talla_id)
                            ->first();

                        if ($tallaStock) {
                            $tallaStock->increment('stock', $detalle->cantidad);
                        }
                    }
                }
            }
        });
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
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
        return app(\App\Services\VentaService::class)->anular($this);
    }
}
