<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'comprobante_id',
        'importeRecibido',
        'vuelto',
    ];

    // Un pago pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Un pago tiene un comprobante
    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

}
