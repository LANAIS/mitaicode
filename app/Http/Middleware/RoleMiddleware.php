<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verificar si el usuario está autenticado
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Obtener el rol del usuario
        $userRole = $request->user()->role;
        
        // Verificar si el usuario tiene el rol necesario
        if ($userRole !== $role && $userRole !== 'admin') { // Administradores pueden acceder a todo
            return redirect()->route('dashboard')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
