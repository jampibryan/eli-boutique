<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta registros en la tabla productos, uno para cada categoría
        DB::table('productos')->insert([
            [
                'categoria_producto_id' => 1, // ID de la categoría "Camisetas"
                'imagenP' => NULL,
                'descripcionP' => 'Camiseta básica de algodón.',
                'precioP' => 20,
                'stockP' => 20,
            ],
            [
                'categoria_producto_id' => 1, // ID de la categoría "Camisetas"
                'imagenP' => NULL,
                'descripcionP' => 'Camiseta estampada con diseños modernos.',
                'precioP' => 25,
                'stockP' => 20,
            ],

            [
                'categoria_producto_id' => 2, // ID de la categoría "Pantalones"
                'imagenP' => 'jeans_clasicos.jpg',
                'descripcionP' => 'Jeans clásicos de corte recto.',
                'precioP' => 50,
                'stockP' => 20,
            ],
            [
                'categoria_producto_id' => 2, // ID de la categoría "Pantalones"
                'imagenP' => NULL,
                'descripcionP' => 'Pantalones cargo con múltiples bolsillos.',
                'precioP' => 60,
                'stockP' => 20,
            ],


            [
                'categoria_producto_id' => 3, // ID de la categoría "Zapatos"
                'imagenP' => NULL,
                'descripcionP' => 'Zapatos deportivos ligeros y cómodos.',
                'precioP' => 60,
                'stockP' => 20,
            ],
            [
                'categoria_producto_id' => 3, // ID de la categoría "Zapatos"
                'imagenP' => NULL,
                'descripcionP' => 'Zapatos formales de cuero.',
                'precioP' => 100,
                'stockP' => 20,
            ],


            [
                'categoria_producto_id' => 4, // ID de la categoría "Accesorios"
                'imagenP' => NULL,
                'descripcionP' => 'Gafas de sol con protección UV.',
                'precioP' => 15,
                'stockP' => 20,
            ],
            [
                'categoria_producto_id' => 4, // ID de la categoría "Accesorios"
                'imagenP' => NULL,
                'descripcionP' => 'Reloj analógico con correa de cuero.',
                'precioP' => 35,
                'stockP' => 20,
            ],


            [
                'categoria_producto_id' => 5, // ID de la categoría "Chaquetas"
                'imagenP' => NULL,
                'descripcionP' => 'Chaqueta de cuero auténtico.',
                'precioP' => 200,
                'stockP' => 20,
            ],
            [
                'categoria_producto_id' => 5, // ID de la categoría "Chaquetas"
                'imagenP' => NULL,
                'descripcionP' => 'Chaqueta deportiva con capucha.',
                'precioP' => 80,
                'stockP' => 20,
            ],
            
            [
                'categoria_producto_id' => 6, // ID de la categoría "Shorts"
                'imagenP' => NULL,
                'descripcionP' => 'Shorts ligeros para verano.',
                'precioP' => 24,
                'stockP' => 20,
            ],
            [
                'categoria_producto_id' => 6, // ID de la categoría "Shorts"
                'imagenP' => NULL,
                // 'imagenP' => 'shorts_deportivos.jpg',
                'descripcionP' => 'Shorts deportivos con tecnología de secado rápido.',
                'precioP' => 30,
                'stockP' => 20,
            ],

        ]);
    }
}

