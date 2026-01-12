<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoTallaStock extends Model
{
    use HasFactory;

    protected $table = 'producto_talla_stock';

    protected $fillable = [
        'producto_id',
        'producto_talla_id',
        'stock'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function talla()
    {
        return $this->belongsTo(ProductoTalla::class, 'producto_talla_id');
    }
}
