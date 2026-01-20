<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigoCaja',
        'fecha',
        'hora_cierre',
        'clientesHoy',
        'productosVendidos',
        'ingresoDiario',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generar automáticamente el código de caja al crear un registro
        static::creating(function ($model) {
            $latestCaja = self::latest('id')->first();
            $nextCodigo = $latestCaja ? str_pad($latestCaja->id + 1, 6, '0', STR_PAD_LEFT) : '000001';
            $model->codigoCaja = $nextCodigo;
        });
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
