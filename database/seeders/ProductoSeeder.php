<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\ProductoTallaStock;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tallas para ropa (letras): S, M, L, XL
        $tallasRopa = [1, 2, 3, 4]; // IDs de las tallas S, M, L, XL
        
        // Tallas para pantalones (números): 28, 30, 32, 34
        $tallasPantalones = [5, 6, 7, 8]; // IDs de las tallas 28, 30, 32, 34
        
        // CATEGORÍA 1: "Polos & Camisetas" - Usan tallas de letras
        $polos = [
            [
                'codigoP' => 'POL-001',
                'categoria_producto_id' => 1,
                'producto_genero_id' => 1,
                'descripcionP' => 'Polo clásico cuello pique',
                'precioP' => 20,
            ],
            [
                'codigoP' => 'CAM-002',
                'categoria_producto_id' => 1,
                'producto_genero_id' => 1,
                'descripcionP' => 'Camiseta básica algodón',
                'precioP' => 25,
            ],
            [
                'codigoP' => 'POL-003',
                'categoria_producto_id' => 1,
                'producto_genero_id' => 1,
                'descripcionP' => 'Polo manga larga',
                'precioP' => 25,
            ],
            [
                'codigoP' => 'CAM-004',
                'categoria_producto_id' => 1,
                'producto_genero_id' => 1,
                'descripcionP' => 'Camiseta estampada',
                'precioP' => 25,
            ],
            [
                'codigoP' => 'POL-005',
                'categoria_producto_id' => 1,
                'producto_genero_id' => 1,
                'descripcionP' => 'Polo tipo golf',
                'precioP' => 25,
            ],
        ];

        foreach ($polos as $polo) {
            $producto = Producto::create($polo);
            
            // Asignar tallas con stock para cada polo
            foreach ($tallasRopa as $tallaId) {
                ProductoTallaStock::create([
                    'producto_id' => $producto->id,
                    'producto_talla_id' => $tallaId,
                    'stock' => rand(5, 15), // Stock aleatorio entre 5 y 15 por talla
                ]);
            }
        }

        // CATEGORÍA 2: "Jeans & Pantalones" - Usan tallas numéricas
        $pantalones = [
            [
                'codigoP' => 'JEA-021',
                'categoria_producto_id' => 2,
                'producto_genero_id' => 1,
                'descripcionP' => 'Jeans skinny ajustados',
                'precioP' => 50,
            ],
            [
                'codigoP' => 'JEA-022',
                'categoria_producto_id' => 2,
                'producto_genero_id' => 1,
                'descripcionP' => 'Jeans rectos clásicos',
                'precioP' => 60,
            ],
            [
                'codigoP' => 'PAN-023',
                'categoria_producto_id' => 2,
                'producto_genero_id' => 1,
                'descripcionP' => 'Pantalón de vestir',
                'precioP' => 60,
            ],
            [
                'codigoP' => 'JEA-024',
                'categoria_producto_id' => 2,
                'producto_genero_id' => 1,
                'descripcionP' => 'Jeans boyfriend',
                'precioP' => 65,
            ],
            [
                'codigoP' => 'PAN-025',
                'categoria_producto_id' => 2,
                'producto_genero_id' => 1,
                'descripcionP' => 'Pantalón cargo',
                'precioP' => 55,
            ],
        ];

        foreach ($pantalones as $pantalon) {
            $producto = Producto::create($pantalon);
            
            // Asignar tallas con stock para cada pantalón
            foreach ($tallasPantalones as $tallaId) {
                ProductoTallaStock::create([
                    'producto_id' => $producto->id,
                    'producto_talla_id' => $tallaId,
                    'stock' => rand(5, 15), // Stock aleatorio entre 5 y 15 por talla
                ]);
            }
        }

        // CATEGORÍA 3: "Shorts & Bermudas" - Usan tallas numéricas
        $shorts = [
            [
                'codigoP' => 'SHO-041',
                'categoria_producto_id' => 3,
                'producto_genero_id' => 1,
                'descripcionP' => 'Shorts denim',
                'precioP' => 35,
            ],
            [
                'codigoP' => 'SHO-042',
                'categoria_producto_id' => 3,
                'producto_genero_id' => 1,
                'descripcionP' => 'Shorts deportivos',
                'precioP' => 30,
            ],
            [
                'codigoP' => 'BER-043',
                'categoria_producto_id' => 3,
                'producto_genero_id' => 1,
                'descripcionP' => 'Bermudas de lino',
                'precioP' => 40,
            ],
        ];

        foreach ($shorts as $short) {
            $producto = Producto::create($short);
            
            // Asignar tallas con stock
            foreach ($tallasPantalones as $tallaId) {
                ProductoTallaStock::create([
                    'producto_id' => $producto->id,
                    'producto_talla_id' => $tallaId,
                    'stock' => rand(5, 15),
                ]);
            }
        }

        // CATEGORÍA 4: "Abrigos & Chaquetas" - Usan tallas de letras
        $abrigos = [
            [
                'codigoP' => 'CHA-061',
                'categoria_producto_id' => 4,
                'producto_genero_id' => 1,
                'descripcionP' => 'Chaqueta denim',
                'precioP' => 80,
            ],
            [
                'codigoP' => 'CHA-062',
                'categoria_producto_id' => 4,
                'producto_genero_id' => 1,
                'descripcionP' => 'Chaqueta bomber',
                'precioP' => 90,
            ],
            [
                'codigoP' => 'ABR-063',
                'categoria_producto_id' => 4,
                'producto_genero_id' => 1,
                'descripcionP' => 'Abrigo trench',
                'precioP' => 120,
            ],
        ];

        foreach ($abrigos as $abrigo) {
            $producto = Producto::create($abrigo);
            
            // Asignar tallas con stock
            foreach ($tallasRopa as $tallaId) {
                ProductoTallaStock::create([
                    'producto_id' => $producto->id,
                    'producto_talla_id' => $tallaId,
                    'stock' => rand(3, 10),
                ]);
            }
        }

        // CATEGORÍA 5: "Ropa Deportiva" - Usan tallas de letras
        $deportiva = [
            [
                'codigoP' => 'DEP-081',
                'categoria_producto_id' => 5,
                'producto_genero_id' => 1,
                'descripcionP' => 'Conjunto deportivo',
                'precioP' => 70,
            ],
            [
                'codigoP' => 'LEG-082',
                'categoria_producto_id' => 5,
                'producto_genero_id' => 1,
                'descripcionP' => 'Leggings deportivos',
                'precioP' => 45,
            ],
            [
                'codigoP' => 'SUD-083',
                'categoria_producto_id' => 5,
                'producto_genero_id' => 1,
                'descripcionP' => 'Sudadera con capucha',
                'precioP' => 55,
            ],
        ];

        foreach ($deportiva as $dep) {
            $producto = Producto::create($dep);
            
            // Asignar tallas con stock
            foreach ($tallasRopa as $tallaId) {
                ProductoTallaStock::create([
                    'producto_id' => $producto->id,
                    'producto_talla_id' => $tallaId,
                    'stock' => rand(5, 15),
                ]);
            }
        }
    }
}
