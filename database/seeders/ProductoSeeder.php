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

            //CATEGORÍA "Polos & Camisetas"
            [
                'codigoP' => 'POL-001',
                'categoria_producto_id' => 1,
                'imagenP' => NULL,
                'descripcionP' => 'Polo clásico cuello pique',
                'precioP' => 20,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'CAM-002',
                'categoria_producto_id' => 1,
                'imagenP' => NULL,
                'descripcionP' => 'Camiseta básica algodón',
                'precioP' => 25,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'POL-003',
                'categoria_producto_id' => 1,
                'imagenP' => NULL,
                'descripcionP' => 'Polo manga larga',
                'precioP' => 25,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'CAM-004',
                'categoria_producto_id' => 1,
                'imagenP' => NULL,
                'descripcionP' => 'Camiseta estampada',
                'precioP' => 25,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'POL-005',
                'categoria_producto_id' => 1,
                'imagenP' => NULL,
                'descripcionP' => 'Polo tipo golf',
                'precioP' => 25,
                'stockP' => 30,
            ],

            //CATEGORÍA "Jeans & Pantalones"
            [
                'codigoP' => 'JEA-021',
                'categoria_producto_id' => 2,
                // 'imagenP' => 'Jeans_skinny_ajustados.jpg',
                'imagenP' => NULL,
                'descripcionP' => 'Jeans skinny ajustados',
                'precioP' => 50,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'JEA-022',
                'categoria_producto_id' => 2,
                'imagenP' => NULL,
                'descripcionP' => 'Jeans rectos clásicos',
                'precioP' => 60,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'PAN-023',
                'categoria_producto_id' => 2,
                'imagenP' => NULL,
                'descripcionP' => 'Pantalón de vestir',
                'precioP' => 60,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'JEA-024',
                'categoria_producto_id' => 2,
                'imagenP' => NULL,
                'descripcionP' => 'Jeans boyfriend',
                'precioP' => 60,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'PAN-025',
                'categoria_producto_id' => 2,
                'imagenP' => NULL,
                'descripcionP' => 'Pantalón cargo',
                'precioP' => 60,
                'stockP' => 30,
            ],

            //CATEGORÍA "Shorts & Bermudas"
            [
                'codigoP' => 'SHO-031',
                'categoria_producto_id' => 3,
                'imagenP' => NULL,
                'descripcionP' => 'Shorts denim',
                'precioP' => 35,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'BER-032',
                'categoria_producto_id' => 3,
                'imagenP' => NULL,
                'descripcionP' => 'Bermudas de lino',
                'precioP' => 40,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'SHO-033',
                'categoria_producto_id' => 3,
                'imagenP' => NULL,
                'descripcionP' => 'Shorts deportivos',
                'precioP' => 30,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'BER-034',
                'categoria_producto_id' => 3,
                'imagenP' => NULL,
                'descripcionP' => 'Bermudas cargo',
                'precioP' => 45,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'SHO-035',
                'categoria_producto_id' => 3,
                'imagenP' => NULL,
                'descripcionP' => 'Shorts elegantes',
                'precioP' => 50,
                'stockP' => 30,
            ],

            //CATEGORÍA "Abrigos & Chaquetas"
            [
                'codigoP' => 'CHA-041',
                'categoria_producto_id' => 4,
                'imagenP' => NULL,
                'descripcionP' => 'Chaqueta denim',
                'precioP' => 80,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'ABR-042',
                'categoria_producto_id' => 4,
                'imagenP' => NULL,
                'descripcionP' => 'Abrigo trench coat',
                'precioP' => 120,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'CHA-043',
                'categoria_producto_id' => 4,
                'imagenP' => NULL,
                'descripcionP' => 'Chaqueta de cuero',
                'precioP' => 150,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'CHA-044',
                'categoria_producto_id' => 4,
                'imagenP' => NULL,
                'descripcionP' => 'Chaqueta bomber',
                'precioP' => 90,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'BLA-045',
                'categoria_producto_id' => 4,
                'imagenP' => NULL,
                'descripcionP' => 'Blazer elegante',
                'precioP' => 100,
                'stockP' => 30,
            ],

            //CATEGORÍA "Ropa Deportiva"
            [
                'codigoP' => 'DEP-051',
                'categoria_producto_id' => 5,
                'imagenP' => NULL,
                'descripcionP' => 'Conjunto deportivo (top + pants)',
                'precioP' => 60,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'DEP-052',
                'categoria_producto_id' => 5,
                'imagenP' => NULL,
                'descripcionP' => 'Leggings de yoga',
                'precioP' => 45,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'DEP-053',
                'categoria_producto_id' => 5,
                'imagenP' => NULL,
                'descripcionP' => 'Sudadera con capucha',
                'precioP' => 70,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'DEP-054',
                'categoria_producto_id' => 5,
                'imagenP' => NULL,
                'descripcionP' => 'Shorts de running',
                'precioP' => 35,
                'stockP' => 30,
            ],
            [
                'codigoP' => 'DEP-055',
                'categoria_producto_id' => 5,
                'imagenP' => NULL,
                'descripcionP' => 'Top deportivo',
                'precioP' => 25,
                'stockP' => 30,
            ],
        ]);
    }
}