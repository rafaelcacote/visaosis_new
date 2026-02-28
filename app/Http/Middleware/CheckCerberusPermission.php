<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use Symfony\Component\HttpFoundation\Response;

class CheckCerberusPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Verificar se o usuário está autenticado
        if (!AuthHelper::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar autenticado para acessar esta página.');
        }

        // Se não há permissões específicas requeridas, permitir acesso
        if (empty($permissions)) {
            return $next($request);
        }

        // Verificar se o usuário tem pelo menos uma das permissões requeridas
        foreach ($permissions as $permission) {
            if (AuthHelper::hasPermission($permission)) {
                return $next($request);
            }
        }

        // Se chegou aqui, não tem permissão
        abort(403, 'Você não tem permissão para acessar esta página.');
    }
}
