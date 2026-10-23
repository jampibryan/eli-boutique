<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    public const TIPO_INGRESO_COMPRA = 'INGRESO_COMPRA';
    public const TIPO_SALIDA_VENTA = 'SALIDA_VENTA';
    public const TIPO_REVERSION_VENTA = 'REVERSION_VENTA';
    public const TIPO_AJUSTE_INVENTARIO = 'AJUSTE_INVENTARIO';
    public const TIPO_INVENTARIO_INICIAL = 'INVENTARIO_INICIAL';

    protected $fillable = [
        'producto_id',
        'producto_talla_id',
        'tipo_movimiento',
        'cantidad',
        'costo_unitario',
        'precio_unitario',
        'stock_anterior',
        'stock_nuevo',
        'origen_tipo',
        'origen_id',
        'referencia_documento',
        'user_id',
        'motivo',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class)->withTrashed();
    }

    public function talla()
    {
        return $this->belongsTo(ProductoTalla::class, 'producto_talla_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function origen()
    {
        if ($this->origen_tipo && class_exists($this->origen_tipo)) {
            return $this->belongsTo($this->origen_tipo, 'origen_id');
        }

        return null;
    }
}
