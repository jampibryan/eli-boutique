<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoTallaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla producto_tallas
        DB::table('producto_tallas')->insert([
            ['descripcion' => 'S'],
            ['descripcion' => 'M'],
            ['descripcion' => 'L'],
            ['descripcion' => 'XL'],
            ['descripcion' => '28'],
            ['descripcion' => '30'],
            ['descripcion' => '32'],
            ['descripcion' => '34'],
        ]);
    }
}
