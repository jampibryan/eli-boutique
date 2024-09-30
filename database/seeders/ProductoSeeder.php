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
                // 'imagenP' => 'camiseta_basica.jpg',
                'descripcionP' => 'Camiseta básica de algodón, ideal para uso diario.',
                'precioP' => 20,
                'stockP' => 15,
            ],
            [
                'categoria_producto_id' => 1, // ID de la categoría "Camisetas"
                // 'imagenP' => 'camiseta_estampada.jpg',
                'descripcionP' => 'Camiseta estampada con diseños modernos.',
                'precioP' => 25,
                'stockP' => 10,
            ],


            [
                'categoria_producto_id' => 2, // ID de la categoría "Pantalones"
                // 'imagenP' => 'jeans_clasicos.jpg',
                'descripcionP' => 'Jeans clásicos de corte recto, perfectos para cualquier ocasión.',
                'precioP' => 50,
                'stockP' => 8,
            ],
            [
                'categoria_producto_id' => 2, // ID de la categoría "Pantalones"
                // 'imagenP' => 'pantalon_cargo.jpg',
                'descripcionP' => 'Pantalones cargo con múltiples bolsillos, ideales para un look casual y práctico.',
                'precioP' => 60,
                'stockP' => 10,
            ],


            [
                'categoria_producto_id' => 3, // ID de la categoría "Zapatos"
                // 'imagenP' => 'zapatos_deportivos.jpg',
                'descripcionP' => 'Zapatos deportivos ligeros y cómodos, perfectos para correr.',
                'precioP' => 60,
                'stockP' => 10,
            ],
            [
                'categoria_producto_id' => 3, // ID de la categoría "Zapatos"
                // 'imagenP' => 'zapatos_formales.jpg',
                'descripcionP' => 'Zapatos formales de cuero, ideales para ocasiones especiales.',
                'precioP' => 100,
                'stockP' => 4,
            ],


            // [
            //     'categoria_producto_id' => 4, // ID de la categoría "Accesorios"
            //     'imagenP' => 'gafas_de_sol.jpg',
            //     'descripcionP' => 'Gafas de sol con protección UV, estilo clásico.',
            //     'precioP' => 15,
            //     'stockP' => 20,
            // ],
            // [
            //     'categoria_producto_id' => 4, // ID de la categoría "Accesorios"
            //     'imagenP' => 'reloj.jpg',
            //     'descripcionP' => 'Reloj analógico con correa de cuero, diseño elegante.',
            //     'precioP' => 35,
            //     'stockP' => 10,
            // ],


            [
                'categoria_producto_id' => 5, // ID de la categoría "Chaquetas"
                // 'imagenP' => 'chaqueta_cuero.jpg',
                'descripcionP' => 'Chaqueta de cuero auténtico, estilo biker.',
                'precioP' => 200,
                'stockP' => 6,
            ],
            [
                'categoria_producto_id' => 5, // ID de la categoría "Chaquetas"
                // 'imagenP' => 'chaqueta_deportiva.jpg',
                'descripcionP' => 'Chaqueta deportiva con capucha.',
                'precioP' => 80,
                'stockP' => 10,
            ],


            [
                'categoria_producto_id' => 6, // ID de la categoría "Shorts"
                // 'imagenP' => 'shorts_verano.jpg',
                'descripcionP' => 'Shorts ligeros para verano, disponibles en varios colores.',
                'precioP' => 24,
                'stockP' => 12,
            ],
            [
                'categoria_producto_id' => 6, // ID de la categoría "Shorts"
                // 'imagenP' => 'shorts_deportivos.jpg',
                'descripcionP' => 'Shorts deportivos con tecnología de secado rápido, ideales para entrenar.',
                'precioP' => 30,
                'stockP' => 18,
            ],
        ]);
    }
}
