<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AffiliateAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Se não está logado, deixa o Filament redirecionar para login
        if (!$user) {
            return $next($request);
        }
        
        // Se é admin, redireciona para painel admin
        if ($user->hasRole('admin')) {
            return redirect('/admin');
        }
        
        // Se não tem inviter_code, não é afiliado
        if (!$user->inviter_code) {
            return redirect('/');
        }
        
        // É afiliado, pode acessar
        return $next($request);
    }
}