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
        // Inserta los registros en la tabla comprobante
        DB::table('estado_transacciones')->insert([
            ['descripcionET' => 'Pendiente'],
            ['descripcionET' => 'Pagado'],
            ['descripcionET' => 'Anulado'],
            ['descripcionET' => 'Recibido'],
        ]);
    }
}
