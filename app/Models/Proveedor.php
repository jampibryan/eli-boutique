<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla en caso de que no siga la convención
    protected $table = 'proveedores';

    protected $fillable = [
        'tipo_proveedor_id',
        'nombreProveedor',
        'RUC',
        'direccionProveedor',
        'correoProveedor',
        'telefonoProveedor',
    ];

    public function tipoProveedor()
    {
        return $this->belongsTo(TipoProveedor::class, 'tipo_proveedor_id');
        // 'tipo_proveedor_id' es la columna en 'Proveedores' que se refiere a 'TipoProveedor'
    }
}
