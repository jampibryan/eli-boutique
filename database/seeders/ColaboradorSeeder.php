<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColaboradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inserta los registros en la tabla cargos
        DB::table('colaboradores')->insert([
            [
                'cargo_id' => 1,
                'nombreColab' => 'Juan',
                'apellidosColab' => 'Pérez García',
                'tipo_genero_id' => 1,
                'dniColab' => '12345678',
                'edadColab' => 30,
                'correoColab' => 'juan.perez@gmail.com',
                'telefonoColab' => '987654321',
            ],
            [
                'cargo_id' => 2,
                'nombreColab' => 'María',
                'apellidosColab' => 'López Fernández',
                'tipo_genero_id' => 2,
                'dniColab' => '87654321',
                'edadColab' => 33,
                'correoColab' => 'maria.lopez@gmail.com',
                'telefonoColab' => '912345678',
            ],
            [
                'cargo_id' => 3,
                'nombreColab' => 'Carlos',
                'apellidosColab' => 'Ramírez Sánchez',
                'tipo_genero_id' => 1,
                'dniColab' => '11223344',
                'edadColab' => 25,
                'correoColab' => 'carlos.ramirez@gmail.com',
                'telefonoColab' => '923456789',
            ],
            [
                'cargo_id' => 4,
                'nombreColab' => 'Ana',
                'apellidosColab' => 'Martínez Gómez',
                'tipo_genero_id' => 2,
                'dniColab' => '22334455',
                'edadColab' => 29,
                'correoColab' => 'ana.martinez@gmail.com',
                'telefonoColab' => '934567890',
            ],
            [
                'cargo_id' => 5,
                'nombreColab' => 'Luis',
                'apellidosColab' => 'González Ruiz',
                'tipo_genero_id' => 1,
                'dniColab' => '33445566',
                'edadColab' => 35,
                'correoColab' => 'luis.gonzalez@gmail.com',
                'telefonoColab' => '945678901',
            ],
            [
                'cargo_id' => 6,
                'nombreColab' => 'Laura',
                'apellidosColab' => 'Hernández Díaz',
                'tipo_genero_id' => 2,
                'dniColab' => '44556677',
                'edadColab' => 27,
                'correoColab' => 'laura.hernandez@gmail.com',
                'telefonoColab' => '956789012',
            ],
        ]);
    }
}
