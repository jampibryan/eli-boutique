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
        'comprobante_id',
        'estado_venta_id',
        'subTotal',
        'IGV',
        'montoTotal',
    ];

    protected static function booted()
    {
        static::creating(function ($venta) {
            // Asigna el estado predeterminado "pendiente"
            if (!$venta->estado_venta_id) {
                $estadoPendiente = EstadoVenta::where('descripcionEV', 'pendiente')->first();
                $venta->estado_venta_id = $estadoPendiente->id;
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function estadoVenta()
    {
        return $this->belongsTo(EstadoVenta::class);
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
