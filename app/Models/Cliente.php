<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, Auditable, Filterable;

    protected $searchableFields = ['nombreCliente', 'apellidoCliente', 'dniCliente', 'telefonoCliente'];

    protected $fillable = [
        'nombreCliente',
        'apellidoCliente',
        'dniCliente',
        'correoCliente',
        'telefonoCliente',
        'tipo_genero_id',
        'tipo_documento',
        'direccionCliente',
    ];

    protected $dates = ['deleted_at'];

    public function tipoGenero()
    {
        return $this->belongsTo(TipoGenero::class, 'tipo_genero_id');
        // Define la relación inversa de muchos a uno con el modelo TipoGenero.
        // Un Cliente pertenece a un TipoGenero.
    }
}
