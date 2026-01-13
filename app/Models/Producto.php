<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoP',
        'categoria_producto_id',
        'producto_genero_id',
        'imagenP',
        'descripcionP',
        'precioP',
    ];

    public function categoriaProducto()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
        // Define la relación inversa de muchos a uno con el modelo CategoriaProducto.
        // Un Producto o varios pueden pertenecen a una sola CategoriaProducto.
    }

    public function productoGenero()
    {
        return $this->belongsTo(ProductoGenero::class, 'producto_genero_id');
    }

    // Relación muchos a muchos con tallas a través de tabla pivot
    public function tallas()
    {
        return $this->belongsToMany(ProductoTalla::class, 'producto_talla_stock', 'producto_id', 'producto_talla_id')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

    // Relación directa con la tabla pivot
    public function tallaStocks()
    {
        return $this->hasMany(ProductoTallaStock::class);
    }

    // Helper para obtener stock total del producto (suma de todas las tallas)
    public function getStockTotalAttribute()
    {
        return $this->tallaStocks()->sum('stock');
    }

}
