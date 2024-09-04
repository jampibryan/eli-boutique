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
                'nombreColab' => 'Juan',
                'apellidosColab' => 'Pérez García',
                'dniColab' => '12345678',
                'edadColab' => 30,
                'telefonoColab' => '987654321',
                'cargo_id' => 1,
                'tipo_genero_id' => 1,
            ],
            [
                'nombreColab' => 'María',
                'apellidosColab' => 'López Fernández',
                'dniColab' => '87654321',
                'edadColab' => 32,
                'telefonoColab' => '912345678',
                'cargo_id' => 2,
                'tipo_genero_id' => 2,
            ],
            [
                'nombreColab' => 'Carlos',
                'apellidosColab' => 'Ramírez Sánchez',
                'dniColab' => '11223344',
                'edadColab' => 25,
                'telefonoColab' => '923456789',
                'cargo_id' => 3,
                'tipo_genero_id' => 1,
            ],
            [
                'nombreColab' => 'Ana',
                'apellidosColab' => 'Martínez Gómez',
                'dniColab' => '22334455',
                'edadColab' => 29,
                'telefonoColab' => '934567890',
                'cargo_id' => 4,
                'tipo_genero_id' => 2,
            ],
            [
                'nombreColab' => 'Luis',
                'apellidosColab' => 'González Ruiz',
                'dniColab' => '33445566',
                'edadColab' => 35,
                'telefonoColab' => '945678901',
                'cargo_id' => 5,
                'tipo_genero_id' => 1,
            ],
            [
                'nombreColab' => 'Laura',
                'apellidosColab' => 'Hernández Díaz',
                'dniColab' => '44556677',
                'edadColab' => 27,
                'telefonoColab' => '956789012',
                'cargo_id' => 6,
                'tipo_genero_id' => 2,
            ],
        ]);
    }
}
