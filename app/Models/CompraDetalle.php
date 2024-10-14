<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id',    // Relación con la compra
        'producto_id',  // Relación con el producto
        'cantidad',     // Cantidad del producto en la compra
    ];

    // Relación de un detalle de compra con una compra
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    // Relación de un detalle de compra con un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
