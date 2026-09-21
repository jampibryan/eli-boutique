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
        'egresoDiario',
    ];

    protected $casts = [
        'ingresoDiario' => 'decimal:2',
        'egresoDiario' => 'decimal:2',
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

    public function pagos()
    {
        return $this->hasManyThrough(Pago::class, Venta::class);
    }

    /**
     * Balance del día: ingresos - egresos
     */
    public function getBalanceDiarioAttribute()
    {
        return $this->ingresoDiario - $this->egresoDiario;
    }

    /**
     * Carbon instance segura para la fecha de caja
     */
    public function getFechaCarbonAttribute(): \Carbon\Carbon
    {
        if (empty($this->fecha)) {
            return now();
        }

        try {
            return \Carbon\Carbon::parse($this->fecha);
        } catch (\Throwable $e) {
            $cleaned = preg_replace('/:(AM|PM)$/i', ' $1', trim($this->fecha));
            try {
                return \Carbon\Carbon::parse($cleaned);
            } catch (\Throwable $e2) {
                return now();
            }
        }
    }

    /**
     * Fecha formateada (d/m/Y) resistente a formatos SQL Server ("Jun 11 2026 12:00:00:AM")
     */
    public function getFechaFormateadaAttribute(): string
    {
        return $this->fecha_carbon->format('d/m/Y');
    }
}
