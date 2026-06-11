<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDocumentoSunat extends Model
{
    use HasFactory;

    protected $table = 'venta_documentos_sunat';

    protected $fillable = [
        'venta_id',
        'uuid',
        'estado_envio',
        'xml_path',
        'cdr_path',
        'signature_hash',
        'codigo_respuesta_sunat',
        'descripcion_respuesta_sunat',
        'intentos_envio',
        'fecha_envio',
        'fecha_respuesta',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_respuesta' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
