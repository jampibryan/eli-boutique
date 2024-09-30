<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla comprobante
        DB::table('estado_ventas')->insert([
            ['descripcionEV' => 'Pendiente'],
            ['descripcionEV' => 'Pagado'],
            ['descripcionEV' => 'Anulado'],
        ]);
    }
}
