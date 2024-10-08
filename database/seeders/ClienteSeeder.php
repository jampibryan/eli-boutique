<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clientes')->insert([
            [
                'nombreCliente'   => 'Luis',
                'apellidoCliente' => 'Fernández Torres',
                'dniCliente'      => '21748275',
                'correoCliente'   => 'luis_fernandez@gmail.com',
                'telefonoCliente' => '947838444',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'María',
                'apellidoCliente' => 'Pérez Sánchez',
                'dniCliente'      => '74838538',
                'correoCliente'   => 'maria_perez@gmail.com',
                'telefonoCliente' => '937582373',
                'tipo_genero_id'  => 2,
            ],
            [
                'nombreCliente'   => 'Carlos',
                'apellidoCliente' => 'Martínez López',
                'dniCliente'      => '83467492',
                'correoCliente'   => 'carlos_martinez@gmail.com',
                'telefonoCliente' => '987655524',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'Laura',
                'apellidoCliente' => 'Ramírez Salas',
                'dniCliente'      => '53171482',
                'correoCliente'   => 'laura_ramirez@gmail.com',
                'telefonoCliente' => '937285313',
                'tipo_genero_id'  => 2,
            ],
            [
                'nombreCliente'   => 'José',
                'apellidoCliente' => 'García Montalvo',
                'dniCliente'      => '74641929',
                'correoCliente'   => 'jose_garcia@gmail.com',
                'telefonoCliente' => '937847241',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'Sofía',
                'apellidoCliente' => 'Cruz Díaz',
                'dniCliente'      => '67890124',
                'correoCliente'   => 'sofia_cruz@gmail.com',
                'telefonoCliente' => '961484889',
                'tipo_genero_id'  => 2,
            ],
            [
                'nombreCliente'   => 'Andrés',
                'apellidoCliente' => 'Castillo Peña',
                'dniCliente'      => '78901235',
                'correoCliente'   => 'andres_castillo@gmail.com',
                'telefonoCliente' => '974938483',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'Isabella',
                'apellidoCliente' => 'Hernández Soto',
                'dniCliente'      => '89012346',
                'correoCliente'   => 'isabella_hernandez@gmail.com',
                'telefonoCliente' => '987654329',
                'tipo_genero_id'  => 2,
            ],
            [
                'nombreCliente'   => 'Diego',
                'apellidoCliente' => 'Morales Ruiz',
                'dniCliente'      => '90123457',
                'correoCliente'   => 'diego_morales@gmail.com',
                'telefonoCliente' => '979473873',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'Valentina',
                'apellidoCliente' => 'Salazar Álvarez',
                'dniCliente'      => '01234568',
                'correoCliente'   => 'valentina_salazar@gmail.com',
                'telefonoCliente' => '937482743',
                'tipo_genero_id'  => 2,
            ],
        ]);
        
    }
}

