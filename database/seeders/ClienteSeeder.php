<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::create([
            'nombreCliente'   => 'Juan',
            'apellidoCliente' => 'Pérez',
            'dniCliente'      => '12345678',
            'correoCliente'   => 'juan.perez@example.com',
            'telefonoCliente' => '987654321',
            'tipo_genero_id'  => 1,
        ]);

        Cliente::create([
            'nombreCliente'   => 'Ana',
            'apellidoCliente' => 'Gómez',
            'dniCliente'      => '87654321',
            'correoCliente'   => 'ana.gomez@example.com',
            'telefonoCliente' => '123456789',
            'tipo_genero_id'  => 2,
        ]);
    }
}
