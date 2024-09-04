<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $fillable = ['descripcionCargo']; // Opcional, si quieres asignación masiva

    public function colaboradores()
    {
        return $this->hasMany(Colaborador::class, 'cargo_id');
        // Aquí especificas 'cargo_id' si quieres ser explícito
        // Laravel asumirá 'cargo_id' automáticamente basándose en la convención
    }
}
