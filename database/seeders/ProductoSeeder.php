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
                'imagenP' => 'camiseta_basica.jpg',
                'descripcionP' => 'Camiseta básica de algodón, ideal para uso diario.',
                'precioP' => 19.99,
                'stockP' => 100,
            ],
            [
                'categoria_producto_id' => 2, // ID de la categoría "Pantalones"
                'imagenP' => 'jeans_clasicos.jpg',
                'descripcionP' => 'Jeans clásicos de corte recto, perfectos para cualquier ocasión.',
                'precioP' => 49.99,
                'stockP' => 50,
            ],
            [
                'categoria_producto_id' => 3, // ID de la categoría "Zapatos"
                'imagenP' => 'zapatos_deportivos.jpg',
                'descripcionP' => 'Zapatos deportivos ligeros y cómodos, perfectos para correr.',
                'precioP' => 59.99,
                'stockP' => 75,
            ],
            [
                'categoria_producto_id' => 4, // ID de la categoría "Accesorios"
                'imagenP' => 'gafas_de_sol.jpg',
                'descripcionP' => 'Gafas de sol con protección UV, estilo clásico.',
                'precioP' => 14.99,
                'stockP' => 150,
            ],
            [
                'categoria_producto_id' => 5, // ID de la categoría "Chaquetas"
                'imagenP' => 'chaqueta_cuero.jpg',
                'descripcionP' => 'Chaqueta de cuero auténtico, estilo biker.',
                'precioP' => 199.99,
                'stockP' => 20,
            ],
            [
                'categoria_producto_id' => 6, // ID de la categoría "Shorts"
                'imagenP' => 'shorts_verano.jpg',
                'descripcionP' => 'Shorts ligeros para verano, disponibles en varios colores.',
                'precioP' => 24.99,
                'stockP' => 80,
            ],
        ]);
    }
}
