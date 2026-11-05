<?php

namespace App\Services;

use App\Models\User;

final class MobileAccess
{
    public const PERMISSIONS = [
        'dashboard' => ['ver productos', 'ver cajas', 'ver reportes gráficos', 'ver clientes',
            'gestionar productos', 'gestionar clientes', 'gestionar proveedores', 'gestionar ventas', 'gestionar compras'],
        'productos' => ['ver productos', 'gestionar productos'],
        'clientes' => ['ver clientes', 'gestionar clientes'],
        'ventas' => ['gestionar ventas', 'crear ventas'],
        'compras' => ['gestionar compras'],
        'proveedores' => ['gestionar proveedores'],
        'cajas' => ['ver cajas', 'gestionar cajas'],
        'graficos' => ['ver reportes gráficos'],
    ];

    public function modules(User $user): array
    {
        $permissions = $user->getAllPermissions()->pluck('name')->all();
        return array_keys(array_filter(self::PERMISSIONS,
            fn ($required) => count(array_intersect($required, $permissions)) > 0));
    }

    public function authorize(User $user, string $module): void
    {
        abort_unless(in_array($module, $this->modules($user), true), 403, 'No tienes permiso para consultar este módulo.');
    }

    public function profile(User $user): array
    {
        return ['id' => (int) $user->id, 'name' => $user->name,
            'modules' => $this->modules($user), 'role' => $user->roleLabel()];
    }
}
