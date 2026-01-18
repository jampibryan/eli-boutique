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
                // 'tipo_proveedor_id' => 1,
                'nombreEmpresa' => 'Moda Eclipse S.A.',
                'nombreProveedor' => 'Carlos',
                'apellidoProveedor' => 'García Maquén',
                'RUC' => '20512345678',
                'direccionProveedor' => 'Av. Las Flores 123',
                'correoProveedor' => 'carlos_garcia@gmail.com',
                'telefonoProveedor' => '998765432',
            ],
            [
                // 'tipo_proveedor_id' => 2,
                'nombreEmpresa' => 'Estilo Urbano E.I.R.L.',
                'nombreProveedor' => 'Ana',
                'apellidoProveedor' => 'Martínez Gómez',
                'RUC' => '09876543210',
                'direccionProveedor' => 'Calle del Sol 456',
                'correoProveedor' => 'ana_martinez@gmail.com',
                'telefonoProveedor' => '992345678',
            ],
            [
                // 'tipo_proveedor_id' => 1,
                'nombreEmpresa' => 'Hilos de Plata S.A.C.',
                'nombreProveedor' => 'Luis',
                'apellidoProveedor' => 'Fernández Castro',
                'RUC' => '10293847562',
                'direccionProveedor' => 'Jr. Santa Rosa 789',
                'correoProveedor' => 'luis_fernandez@gmail.com',
                'telefonoProveedor' => '993456789',
            ],
            [
                // 'tipo_proveedor_id' => 2,
                'nombreEmpresa' => 'Ropa Estelar S.R.L.',
                'nombreProveedor' => 'María López',
                'apellidoProveedor' => 'López Chuiman',
                'RUC' => '20325464682',
                'direccionProveedor' => 'Av. El Sol 321',
                'correoProveedor' => 'maria_lopez@gmail.com',
                'telefonoProveedor' => '994567890',
            ],
            [
                // 'tipo_proveedor_id' => 1,
                'nombreEmpresa' => 'Textiles de Oro S.A.',
                'nombreProveedor' => 'Jorge',
                'apellidoProveedor' => 'Ramírez Salazar',
                'RUC' => '20456712345',
                'direccionProveedor' => 'Calle Los Almendros 88',
                'correoProveedor' => 'jorge_ramirez@gmail.com',
                'telefonoProveedor' => '996543210',
            ],
        ]);
    }
}
