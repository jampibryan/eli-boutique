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
        'producto_talla_id',
        'imagenP',
        'descripcionP',
        'precioP',
        'stockP',
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

    public function productoTalla()
    {
        return $this->belongsTo(ProductoTalla::class, 'producto_talla_id');
    }

    public function getImagenPAttribute($value)
    {
        return asset('storage/' . $value); // Ruta completa de la imagen
    }
}
