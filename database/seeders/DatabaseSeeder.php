<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Colaborador;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llama al seeder TipoGeneroSeeder
        $this->call(TipoGeneroSeeder::class);

        // Llama al seeder ClienteSeeder
        $this->call(ClienteSeeder::class);

        // Llama al seeder CategoriaProducto
        $this->call(CategoriaProductoSeeder::class);

        // Llama al seeder Producto
        $this->call(ProductoSeeder::class);
        
        // Llama al seeder Cargo
        $this->call(CargoSeeder::class);

        // Llama al seeder Colaborador
        $this->call(ColaboradorSeeder::class);
    }
}
