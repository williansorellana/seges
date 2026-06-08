<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasModuleAccess($module)) {
            abort(403, 'No tienes acceso autorizado a este módulo.');
        }

        return $next($request);
    }
}