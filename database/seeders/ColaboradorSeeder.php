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
                'nombreColab' => 'Elyana',
                'apellidosColab' => 'Mostacero Saucedo',
                'tipo_genero_id' => 2,
                'dniColab' => '27654471',
                'edadColab' => 36,
                'correoColab' => 'elyana_mostacero@gmail.com',
                'telefonoColab' => '982345678',
            ],
            [
                'cargo_id' => 2,
                'nombreColab' => 'Laura',
                'apellidosColab' => 'Salcedo Peralta',
                'tipo_genero_id' => 2,
                'dniColab' => '61464837',
                'edadColab' => 20,
                'correoColab' => 'laura_salcedo@gmail.com',
                'telefonoColab' => '955456789',
            ],
            [
                'cargo_id' => 2,
                'nombreColab' => 'Sofia',
                'apellidosColab' => 'Ramírez Sánchez',
                'tipo_genero_id' => 2,
                'dniColab' => '84793754',
                'edadColab' => 25,
                'correoColab' => 'sofia_ramirez@gmail.com',
                'telefonoColab' => '963456789',
            ]
        ]);
    }
}
