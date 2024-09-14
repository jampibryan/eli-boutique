<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla tipo_proveedores
        DB::table('tipo_proveedores')->insert([
            ['descripcionTE' => 'Natural'],
            ['descripcionTE' => 'Jurídico'],
        ]);
    }
}
