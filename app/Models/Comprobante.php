<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $fillable = ['descripcionCOM'];

    // Relación con Pago: un comprobante pertenece a un pago
    public function pago()
    {
        return $this->hasOne(Pago::class);
    }
}
