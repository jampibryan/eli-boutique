<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'gestionar usuarios',
            'gestionar clientes',
            'ver clientes',
            'gestionar colaboradores',
            'gestionar proveedores',
            'gestionar productos',
            'ver productos',
            'gestionar ventas',
            'crear ventas',
            'anular ventas',
            'gestionar compras',
            'ver cajas',
            'gestionar cajas',
            'ver reportes gráficos',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('administrador', 'web');
        $gerenteRole = Role::findOrCreate('gerente', 'web');
        $vendedorRole = Role::findOrCreate('vendedor', 'web');

        $adminRole->syncPermissions([
            'gestionar usuarios',
            'gestionar clientes',
            'ver clientes',
            'gestionar colaboradores',
            'gestionar proveedores',
            'gestionar productos',
            'ver productos',
            'gestionar ventas',
            'crear ventas',
            'anular ventas',
            'gestionar compras',
            'ver cajas',
            'gestionar cajas',
            'ver reportes gráficos',
        ]);

        $gerenteRole->syncPermissions([
            'gestionar clientes',
            'ver clientes',
            'gestionar colaboradores',
            'gestionar proveedores',
            'gestionar productos',
            'ver productos',
            'gestionar ventas',
            'crear ventas',
            'anular ventas',
            'gestionar compras',
            'ver cajas',
            'gestionar cajas',
            'ver reportes gráficos',
        ]);

        $vendedorRole->syncPermissions([
            'gestionar clientes',
            'ver clientes',
            'gestionar colaboradores',
            'gestionar productos',
            'ver productos',
            'gestionar ventas',
            'crear ventas',
            'anular ventas',
            'ver cajas',
            'gestionar cajas',
        ]);
    }
}
