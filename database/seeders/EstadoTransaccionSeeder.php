<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoTransaccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla estado_transacciones
        DB::table('estado_transacciones')->insert([
            ['descripcionET' => 'Pendiente'],  // Para ventas en proceso
            ['descripcionET' => 'Pagado'],     // Para ventas completadas
            ['descripcionET' => 'Anulado'],    // Para ventas/compras canceladas
            ['descripcionET' => 'Borrador'],   // Para compras en creación
            ['descripcionET' => 'Enviada'],    // Para compras enviadas al proveedor
            ['descripcionET' => 'Cotizada'],   // Para compras con cotización
            ['descripcionET' => 'Aprobada'],   // Para compras aprobadas
            ['descripcionET' => 'Recibida'],   // Para compras recibidas
            ['descripcionET' => 'Pagada'],     // Para compras pagadas
        ]);
    }
}

