<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla en caso de que no siga la convención
    protected $table = 'colaboradores';

    protected $fillable = [
        'cargo_id',
        'nombreColab',
        'apellidosColab',
        'tipo_genero_id',
        'dniColab',
        'edadColab',
        'correoColab',
        'telefonoColab',
    ];


    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
        // 'cargo_id' es la columna en 'colaboradores' que se refiere a 'Cargo'
    }

    public function tipoGenero()
    {
        return $this->belongsTo(TipoGenero::class, 'tipo_genero_id');
        // 'tipo_genero_id' es la columna en 'colaboradores' que se refiere a 'TipoGenero'
    }
}
