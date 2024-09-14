<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('proveedores')->insert([
            [
                'tipo_proveedor_id' => 1,
                'nombreProveedor' => 'Carlos García',
                'RUC' => '12345678901',
                'direccionProveedor' => 'Av. Las Flores 123',
                'correoProveedor' => 'carlos.garcia@gmail.com',
                'telefonoProveedor' => '987654321',
            ],
            [
                'tipo_proveedor_id' => 2,
                'nombreProveedor' => 'Ana Martínez',
                'RUC' => '09876543210',
                'direccionProveedor' => 'Calle del Sol 456',
                'correoProveedor' => 'ana.martinez@gmail.com',
                'telefonoProveedor' => '987654322',
            ],
            [
                'tipo_proveedor_id' => 1,
                'nombreProveedor' => 'Luis Fernández',
                'RUC' => '10293847562',
                'direccionProveedor' => 'Jr. Santa Rosa 789',
                'correoProveedor' => 'luis.fernandez@gmail.com',
                'telefonoProveedor' => '987654323',
            ],
        ]);
    }
}
