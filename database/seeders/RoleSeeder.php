<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        Permission::create(['name' => 'gestionar usuarios']);
        Permission::create(['name' => 'gestionar colaboradores']);
        Permission::create(['name' => 'gestionar productos']);
        Permission::create(['name' => 'gestionar clientes']);
        Permission::create(['name' => 'gestionar proveedores']);
        Permission::create(['name' => 'gestionar ventas']);
        Permission::create(['name' => 'gestionar compras']);

        // Crear roles
        $adminRole = Role::create(['name' => 'administrador']);
        $gerenteRole = Role::create(['name' => 'gerente']);
        $vendedorRole = Role::create(['name' => 'vendedor']);

        // Asignar permisos a roles
        $adminRole->givePermissionTo([
            'gestionar usuarios',
            'gestionar colaboradores',
            'gestionar productos',
            'gestionar clientes',
            'gestionar proveedores',
            'gestionar ventas',
            'gestionar compras',
        ]);

        $gerenteRole->givePermissionTo([
            'gestionar colaboradores',
            'gestionar productos',
            'gestionar clientes',
            'gestionar proveedores',
            'gestionar ventas',
            'gestionar compras',
        ]);

        $vendedorRole->givePermissionTo([
            'gestionar clientes',
            'gestionar productos',
            'gestionar ventas',
        ]);
    }
}