<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombreCP',
        'descripcionCP',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'tipo_producto_id');
        // Define la relación de uno a muchos con el modelo Producto.
        // Una CategoriaProducto puede tener muchos Productos.
    }
}
