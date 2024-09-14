<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProveedor extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla en caso de que no siga la convención
    protected $table = 'tipo_proveedores';

    protected $fillable = ['descripcionTE'];

    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'tipo_proveedor_id');
        // Define la relación de uno a muchos con el modelo Proveedor.
        // Un TipoProveedor puede tener muchos Proveedores.
    }
}