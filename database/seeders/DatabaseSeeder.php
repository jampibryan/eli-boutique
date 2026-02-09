<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // Llama al seeder TipoGeneroSeeder
        $this->call(TipoGeneroSeeder::class);

        // Llama al seeder ClienteSeeder
        $this->call(ClienteSeeder::class);

        // Llama al seeder CategoriaProducto
        $this->call(CategoriaProductoSeeder::class);

        // Llama al seeder ProductoGenero
        $this->call(ProductoGeneroSeeder::class);

        // Llama al seeder ProductoTalla
        $this->call(ProductoTallaSeeder::class);

        // Llama al seeder Producto
        $this->call(ProductoSeeder::class);

        // Llama al seeder Cargo
        $this->call(CargoSeeder::class);

        // Llama al seeder Colaborador
        $this->call(ColaboradorSeeder::class);

        // Llama al seeder TipoProveedor
        $this->call(TipoProveedorSeeder::class);

        // Llama al seeder Proveedor
        $this->call(ProveedorSeeder::class);

        // Llama al seeder Comprobante
        $this->call(ComprobanteSeeder::class);

        // Llama al seeder Estado de transacción
        $this->call(EstadoTransaccionSeeder::class);
        
        // Llama al seeder Venta + Compra (octubre 2025, cronológico)
        $this->call(VentaSeeder::class);

    }
}
