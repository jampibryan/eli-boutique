<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoGeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla producto_generos
        DB::table('producto_generos')->insert([
            ['descripcion' => 'Unisex'], // ID 1 - usado por defecto en productos
            ['descripcion' => 'Hombre'], // ID 2 - reservado para uso futuro
            ['descripcion' => 'Mujer'],  // ID 3 - reservado para uso futuro
        ]);
    }
}
