<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla categoria_producto
        DB::table('categoria_productos')->insert([
            [
                'nombreCP' => 'Polos & Camisetas',
                'descripcionCP' => 'Polos clásicos, camisetas básicas y estampadas para uso casual y deportivo.',
            ],
            [
                'nombreCP' => 'Jeans & Pantalones',
                'descripcionCP' => 'Jeans ajustados, rectos, boyfriend y pantalones de vestir y cargo.',
            ],
            [
                'nombreCP' => 'Shorts & Bermudas',
                'descripcionCP' => 'Shorts denim, deportivos y bermudas de lino y cargo para climas cálidos.',
            ],
            [
                'nombreCP' => 'Abrigos & Chaquetas',
                'descripcionCP' => 'Chaquetas denim, bomber, de cuero, abrigos trench y blazers elegantes.',
            ],
            [
                'nombreCP' => 'Ropa Deportiva',
                'descripcionCP' => 'Conjuntos deportivos, leggings, sudaderas y tops para entrenamiento y yoga.',
            ],
        ]);
    }
}
