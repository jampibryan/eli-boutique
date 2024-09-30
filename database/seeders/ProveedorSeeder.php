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
                'RUC' => '20512345678',
                'direccionProveedor' => 'Av. Las Flores 123',
                'correoProveedor' => 'carlos_garcia@gmail.com',
                'telefonoProveedor' => '998765432',
            ],
            [
                'tipo_proveedor_id' => 2,
                'nombreProveedor' => 'Ana Martínez',
                'RUC' => '09876543210',
                'direccionProveedor' => 'Calle del Sol 456',
                'correoProveedor' => 'ana_martinez@gmail.com',
                'telefonoProveedor' => '992345678',
            ],
            [
                'tipo_proveedor_id' => 1,
                'nombreProveedor' => 'Luis Fernández',
                'RUC' => '10293847562',
                'direccionProveedor' => 'Jr. Santa Rosa 789',
                'correoProveedor' => 'luis_fernandez@gmail.com',
                'telefonoProveedor' => '993456789',
            ],
            [
                'tipo_proveedor_id' => 2,
                'nombreProveedor' => 'María López',
                'RUC' => '20123456789',
                'direccionProveedor' => 'Av. El Sol 321',
                'correoProveedor' => 'maria_lopez@gmail.com',
                'telefonoProveedor' => '994567890',
            ],
        ]);
    }
}
