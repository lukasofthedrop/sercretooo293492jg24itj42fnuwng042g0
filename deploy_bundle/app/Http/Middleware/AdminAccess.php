<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // TEMPORARIAMENTE DESABILITADO PARA DEBUG
        return $next($request);
        
        // Se não está logado, deixa o Filament redirecionar para login
        if (!$user) {
            return $next($request);
        }
        
        // Se é afiliado (não-admin com inviter_code), redireciona para painel de afiliado
        if (!$user->hasRole('admin') && $user->inviter_code) {
            return redirect('/afiliado');
        }
        
        // É admin ou usuário comum, pode acessar
        return $next($request);
    }
}