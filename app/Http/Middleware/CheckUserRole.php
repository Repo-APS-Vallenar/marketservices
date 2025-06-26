<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if (!$request->user()) {
            abort(403, 'No has iniciado sesión.');
        }

        $allowedRoles = is_array($roles) ? $roles : explode(',', $roles);
        $allowedRoles = array_map('trim', $allowedRoles);

        if (!in_array($request->user()->role, $allowedRoles)) {
            abort(403, 'No tienes permisos suficientes para acceder a esta sección.');
        }

        return $next($request);
    }
}