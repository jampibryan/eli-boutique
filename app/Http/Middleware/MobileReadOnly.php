<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MobileReadOnly
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->bearerToken() && $request->user()->tokenCan('read')
            && $request->user()->tokenCan('mobile:read'), 403, 'Se requiere una sesión móvil de consulta.');
        return $next($request)->header('Cache-Control', 'private, no-store');
    }
}
