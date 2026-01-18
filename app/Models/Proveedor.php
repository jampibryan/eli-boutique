<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    // Especifica el nombre de la tabla en caso de que no siga la convención
    protected $table = 'proveedores';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        // 'tipo_proveedor_id',
        'nombreEmpresa',
        'nombreProveedor',
        'apellidoProveedor',
        'RUC',
        'direccionProveedor',
        'correoProveedor',
        'telefonoProveedor',
    ];

    // public function tipoProveedor()
    // {
    //     return $this->belongsTo(TipoProveedor::class, 'tipo_proveedor_id');
    //     // 'tipo_proveedor_id' es la columna en 'Proveedores' que se refiere a 'TipoProveedor'
    // }
}
