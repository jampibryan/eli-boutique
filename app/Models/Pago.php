<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'compra_id',
        'comprobante_id',
        'importe',
        'vuelto',
    ];

    // Un pago pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    
    // Un pago pertenece a una compra
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    // Un pago tiene un comprobante
    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

}
