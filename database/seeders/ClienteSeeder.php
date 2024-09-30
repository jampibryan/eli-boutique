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
                'dniCliente'      => '12345679',
                'correoCliente'   => 'luis_fernandez@gmail.com',
                'telefonoCliente' => '987654322',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'María',
                'apellidoCliente' => 'Pérez Sánchez',
                'dniCliente'      => '23456780',
                'correoCliente'   => 'maria_perez@gmail.com',
                'telefonoCliente' => '987654323',
                'tipo_genero_id'  => 2,
            ],
            [
                'nombreCliente'   => 'Carlos',
                'apellidoCliente' => 'Martínez López',
                'dniCliente'      => '34567891',
                'correoCliente'   => 'carlos_martinez@gmail.com',
                'telefonoCliente' => '987654324',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'Laura',
                'apellidoCliente' => 'Ramírez Salas',
                'dniCliente'      => '45678902',
                'correoCliente'   => 'laura_ramirez@gmail.com',
                'telefonoCliente' => '987654325',
                'tipo_genero_id'  => 2,
            ],
            [
                'nombreCliente'   => 'José',
                'apellidoCliente' => 'García Montalvo',
                'dniCliente'      => '56789013',
                'correoCliente'   => 'jose_garcia@gmail.com',
                'telefonoCliente' => '987654326',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'Sofía',
                'apellidoCliente' => 'Cruz Díaz',
                'dniCliente'      => '67890124',
                'correoCliente'   => 'sofia_cruz@gmail.com',
                'telefonoCliente' => '987654327',
                'tipo_genero_id'  => 2,
            ],
            [
                'nombreCliente'   => 'Andrés',
                'apellidoCliente' => 'Castillo Peña',
                'dniCliente'      => '78901235',
                'correoCliente'   => 'andres_castillo@gmail.com',
                'telefonoCliente' => '987654328',
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
                'telefonoCliente' => '987654330',
                'tipo_genero_id'  => 1,
            ],
            [
                'nombreCliente'   => 'Valentina',
                'apellidoCliente' => 'Salazar Álvarez',
                'dniCliente'      => '01234568',
                'correoCliente'   => 'valentina_salazar@gmail.com',
                'telefonoCliente' => '987654331',
                'tipo_genero_id'  => 2,
            ],
        ]);
        
    }
}

