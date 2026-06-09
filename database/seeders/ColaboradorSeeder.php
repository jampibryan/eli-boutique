<?php

namespace Database\Seeders;

use App\Models\Colaborador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColaboradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colaboradores = [
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
            ],
            [
                'cargo_id' => 2,
                'nombreColab' => 'Jacky',
                'apellidosColab' => 'Cubas',
                'tipo_genero_id' => 2,
                'dniColab' => '75841236',
                'edadColab' => 24,
                'correoColab' => 'jacky_cubas@gmail.com',
                'telefonoColab' => '954321678',
            ],
        ];

        foreach ($colaboradores as $colaborador) {
            Colaborador::updateOrCreate(
                ['correoColab' => $colaborador['correoColab']],
                $colaborador
            );
        }
    }
}
