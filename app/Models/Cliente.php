<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombreCliente',
        'apellidoCliente',
        'dniCliente',
        'correoCliente',
        'telefonoCliente',
        'tipo_genero_id'
    ];

    protected $dates = ['deleted_at'];

    public function tipoGenero()
    {
        return $this->belongsTo(TipoGenero::class, 'tipo_genero_id');
        // Define la relación inversa de muchos a uno con el modelo TipoGenero.
        // Un Cliente pertenece a un TipoGenero.
    }
}
