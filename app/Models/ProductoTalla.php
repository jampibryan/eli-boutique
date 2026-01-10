<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoTalla extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'producto_talla_id');
    }
}
