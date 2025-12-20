<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
        ]);

        $admin->assignRole('administrador'); // Asignar el rol de administrador

        // Crear un usuario gerente
        $gerente = User::create([
            'name' => 'Elyana',
            'email' => 'elyana_mostacero@gmail.com',
            'password' => bcrypt('elyana123'),
        ]);

        $gerente->assignRole('gerente'); 

        // Crear un usuario vendedor
        $vendedor = User::create([
            'name' => 'Laura',
            'email' => 'laura_salcedo@gmail.com',
            'password' => bcrypt('laura123'),
        ]);

        $vendedor->assignRole('vendedor');
        
        // Crear un usuario vendedor
        $vendedor = User::create([
            'name' => 'Sofia',
            'email' => 'sofia_ramirez@gmail.com',
            'password' => bcrypt('sofia123'),
        ]);

        $vendedor->assignRole('vendedor');
    }
}
