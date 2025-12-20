<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoCompra',          // Código único de la compra
        'proveedor_id',          // Relación con el proveedor
        'estado_transaccion_id', // Relación con el estado de la transacción
    ];

    protected static function booted()
    {
        static::creating(function ($compra) {
            // Asigna el estado predeterminado "Pendiente" si no está establecido
            if (!$compra->estado_transaccion_id) {
                $estadoPendiente = EstadoTransaccion::where('descripcionET', 'Pendiente')->first();
                $compra->estado_transaccion_id = $estadoPendiente ? $estadoPendiente->id : null;
            }

            // Generar el código de compra automáticamente
            $ultimoCodigo = Compra::max('codigoCompra');
            $nuevoCodigo = str_pad((int)$ultimoCodigo + 1, 7, '0', STR_PAD_LEFT);
            $compra->codigoCompra = $nuevoCodigo;
        });
    }

    // Relación de una compra con múltiples detalles de compra
    public function detalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }

    // Relación con estado de transacción
    public function estadoTransaccion()
    {
        return $this->belongsTo(EstadoTransaccion::class);
    }

    // Relación de una compra con un proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    // Relación de una compra con pagos
    public function pago()
    {
        // return $this->hasMany(Pago::class);
        return $this->hasOne(Pago::class);
    }

    public function anular()
    {
        // Cambiar el estado de la compra a "Anulado"
        $estadoAnulado = EstadoTransaccion::where('descripcionET', 'Anulado')->first();
        if ($estadoAnulado) {
            $this->estado_transaccion_id = $estadoAnulado->id;
            $this->save();
        }
    }
}