<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\TipoGenero;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Generar 50 clientes
        foreach (range(1, 25) as $index) {
            // Generar un nombre y apellidos aleatorios
            $nombreCliente = $faker->firstName;
            $apellidoCliente = $faker->lastName;
            $segundoApellidoCliente = $faker->lastName;

            // Generar el teléfono (9 caracteres)
            $telefonoCliente = $faker->numerify('9########');

            // Generar el DNI (8 caracteres)
            $dniCliente = $faker->numerify('########');

            // Generar correo
            $correoCliente = strtolower($nombreCliente) . '.' . strtolower($apellidoCliente) . '@gmail.com';

            // Generar género aleatorio (1 = hombre, 2 = mujer)
            $tipoGeneroId = $faker->randomElement([1, 2]);

            // Insertar el cliente en la base de datos
            Cliente::create([
                'nombreCliente'   => $nombreCliente,
                'apellidoCliente' => $apellidoCliente,
                'dniCliente'      => $dniCliente,
                'correoCliente'   => $correoCliente,
                'telefonoCliente' => $telefonoCliente,
                'tipo_genero_id'  => $tipoGeneroId,
            ]);
        }
    }
}
