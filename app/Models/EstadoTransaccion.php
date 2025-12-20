<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTransaccion extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla en caso de que no siga la convención
    protected $table = 'estado_transacciones';

    protected $fillable = ['descripcionET'];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'estado_transaccion_id');
        // Define la relación de uno a muchos con el modelo Venta.
        // Un EstadoTransaccion puede tener muchas ventas.
    }
}
