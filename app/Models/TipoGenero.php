<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoGenero extends Model
{
    use HasFactory;

    protected $fillable = ['descripcionTG'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'tipo_genero_id');
        // Define la relación de uno a muchos con el modelo Cliente.
        // Un TipoGenero puede tener muchos Clientes.
    }
}
