<?php

namespace App\Http\Middleware;

use App\Services\MobileAccess;
use Closure;
use Illuminate\Http\Request;

/** Evitar que un token de reportes pueda consultar directorios y operaciones sin permiso. */
class ApiReadPermission
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()->tokenCan('read'), 403);
        $module = $request->segment(2);
        $access = app(MobileAccess::class);
        if (in_array($module, ['categorias', 'tallas'], true)) { $module = 'productos'; }
        if ($module === 'estados-transaccion') {
            abort_unless(array_intersect(['ventas', 'compras'], $access->modules($request->user())), 403);
        } else {
            $access->authorize($request->user(), $module);
        }
        return $next($request)->header('Cache-Control', 'private, no-store');
    }
}
