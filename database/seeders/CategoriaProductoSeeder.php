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
                'nombreCP' => 'Camisetas',
                'descripcionCP' => 'Ropa para la parte superior del cuerpo, comúnmente de manga corta o larga.',
            ],
            [
                'nombreCP' => 'Pantalones',
                'descripcionCP' => 'Ropa para la parte inferior del cuerpo, disponible en varios estilos como jeans, chinos, etc.',
            ],
            [
                'nombreCP' => 'Zapatos',
                'descripcionCP' => 'Calzado para diferentes ocasiones, incluyendo deportivos, formales, y casuales.',
            ],
            [
                'nombreCP' => 'Accesorios',
                'descripcionCP' => 'Complementos para la ropa como cinturones, sombreros, bufandas, etc.',
            ],
            [
                'nombreCP' => 'Chaquetas',
                'descripcionCP' => 'Ropa de abrigo para la parte superior del cuerpo, adecuada para diferentes climas.',
            ],
            [
                'nombreCP' => 'Shorts',
                'descripcionCP' => 'Ropa para la parte inferior del cuerpo, generalmente usada en climas cálidos o para actividades deportivas.',
            ],
        ]);
    }
}
