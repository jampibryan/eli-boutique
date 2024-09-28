<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoVenta extends Model
{
    use HasFactory;
 
    protected $fillable = ['descripcionEV'];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'estado_venta_id');
        // Define la relación de uno a muchos con el modelo Venta.
        // Un EstadoVenta puede tener muchas ventas.
    }
}
