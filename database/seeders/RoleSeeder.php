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
        // ====================================
        // CREAR PERMISOS DEL SISTEMA
        // ====================================
        
        // Módulo: Usuarios
        Permission::create(['name' => 'gestionar usuarios']);
        
        // Módulo: Clientes
        Permission::create(['name' => 'gestionar clientes']);
        Permission::create(['name' => 'ver clientes']);
        
        // Módulo: Colaboradores
        Permission::create(['name' => 'gestionar colaboradores']);
        
        // Módulo: Proveedores
        Permission::create(['name' => 'gestionar proveedores']);
        
        // Módulo: Productos
        Permission::create(['name' => 'gestionar productos']);
        Permission::create(['name' => 'ver productos']);
        
        // Módulo: Ventas
        Permission::create(['name' => 'gestionar ventas']);
        Permission::create(['name' => 'crear ventas']);
        Permission::create(['name' => 'anular ventas']);
        
        // Módulo: Compras
        Permission::create(['name' => 'gestionar compras']);
        
        // Módulo: Cajas
        Permission::create(['name' => 'ver cajas']);
        Permission::create(['name' => 'gestionar cajas']);
        
        // Módulo: Reportes
        Permission::create(['name' => 'ver reportes gráficos']);

        // ====================================
        // CREAR ROLES
        // ====================================
        $adminRole = Role::create(['name' => 'administrador']);
        $gerenteRole = Role::create(['name' => 'gerente']);
        $vendedorRole = Role::create(['name' => 'vendedor']);

        // ====================================
        // ADMINISTRADOR: ACCESO TOTAL
        // ====================================
        $adminRole->givePermissionTo([
            // Usuarios
            'gestionar usuarios',
            
            // Clientes
            'gestionar clientes',
            'ver clientes',
            
            // Colaboradores
            'gestionar colaboradores',
            
            // Proveedores
            'gestionar proveedores',
            
            // Productos
            'gestionar productos',
            'ver productos',
            
            // Ventas
            'gestionar ventas',
            'crear ventas',
            'anular ventas',
            
            // Compras
            'gestionar compras',
            
            // Cajas
            'ver cajas',
            'gestionar cajas',
            
            // Reportes
            'ver reportes gráficos',
        ]);
        
        // ====================================
        // GERENTE: TODO EXCEPTO USUARIOS
        // ====================================
        $gerenteRole->givePermissionTo([
            // NO: gestionar usuarios
            
            // Clientes
            'gestionar clientes',
            'ver clientes',
            
            // Colaboradores
            'gestionar colaboradores',
            
            // Proveedores
            'gestionar proveedores',
            
            // Productos
            'gestionar productos',
            'ver productos',
            
            // Ventas
            'gestionar ventas',
            'crear ventas',
            'anular ventas',
            
            // Compras
            'gestionar compras',
            
            // Cajas
            'ver cajas',
            'gestionar cajas',
            
            // Reportes
            'ver reportes gráficos',
        ]);
        
        // ====================================
        // VENDEDOR: OPERACIONES DE TIENDA
        // ====================================
        $vendedorRole->givePermissionTo([
            // Clientes (CRUD completo)
            'gestionar clientes',
            'ver clientes',
            
            // Colaboradores (CRUD completo)
            'gestionar colaboradores',
            
            // Productos (CRUD completo)
            'gestionar productos',
            'ver productos',
            
            // Ventas (CRUD completo)
            'gestionar ventas',
            'crear ventas',
            'anular ventas',
            
            // Cajas (ver y gestionar)
            'ver cajas',
            'gestionar cajas',
            
            // NO TIENE ACCESO A:
            // - Usuarios
            // - Proveedores
            // - Compras
            // - Reportes
            // - Predicción (se oculta del menú)
        ]);
    }
}