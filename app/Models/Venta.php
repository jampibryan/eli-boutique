<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoVenta',
        'cliente_id',
        'estado_venta_id',
        'subTotal',
        'IGV',
        'montoTotal',
    ];

    protected static function booted()
    {
        static::creating(function ($venta) {
            // Asigna el estado predeterminado "Pendiente" si no está establecido
            if (!$venta->estado_venta_id) {
                $estadoPendiente = EstadoVenta::where('descripcionEV', 'Pendiente')->first();
                $venta->estado_venta_id = $estadoPendiente->id;
            }

            // Generar el código de venta automáticamente
            $ultimoCodigo = Venta::max('codigoVenta');
            $nuevoCodigo = str_pad((int)$ultimoCodigo + 1, 7, '0', STR_PAD_LEFT);
            $venta->codigoVenta = $nuevoCodigo;
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function estadoVenta()
    {
        return $this->belongsTo(EstadoVenta::class);
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pago()
    {
        // return $this->hasMany(Pago::class);
        return $this->hasOne(Pago::class);
    }
}
