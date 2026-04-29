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
        $users = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin123',
                'role' => 'administrador',
            ],
            [
                'name' => 'Elyana',
                'email' => 'elyana_mostacero@gmail.com',
                'password' => 'elyana123',
                'role' => 'gerente',
            ],
            [
                'name' => 'Laura',
                'email' => 'laura_salcedo@gmail.com',
                'password' => 'laura123',
                'role' => 'vendedor',
            ],
            [
                'name' => 'Sofia',
                'email' => 'sofia_ramirez@gmail.com',
                'password' => 'sofia123',
                'role' => 'vendedor',
            ],
        ];

        foreach ($users as $seededUser) {
            $user = User::updateOrCreate(
                ['email' => $seededUser['email']],
                [
                    'name' => $seededUser['name'],
                    'password' => bcrypt($seededUser['password']),
                ]
            );

            $user->syncRoles([$seededUser['role']]);
        }
    }
}
