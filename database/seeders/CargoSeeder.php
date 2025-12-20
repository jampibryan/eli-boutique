<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla cargos
        DB::table('cargos')->insert([
            ['descripcionCargo' => 'Gerente'],
            ['descripcionCargo' => 'Vendedor'],
            ['descripcionCargo' => 'Administrador'],
            // ['descripcionCargo' => 'Supervisor'],
            // ['descripcionCargo' => 'Asistente de venta'],
            // ['descripcionCargo' => 'Asistente de inventario'],
        ]);
    }
}
