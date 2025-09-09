<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Se usuário não está autenticado, continua
        if (!$user) {
            return $next($request);
        }
        
        // Verifica se está em uma rota 2FA ou logout
        $currentPath = $request->path();
        $is2FARoute = str_starts_with($currentPath, '2fa/') || 
                      str_starts_with($currentPath, '2fa') ||
                      $request->routeIs('2fa.*') ||
                      $request->routeIs('logout');
        
        // Se está em rota 2FA, permite acesso
        if ($is2FARoute) {
            return $next($request);
        }
        
        // 2FA TEMPORARIAMENTE DESABILITADO PARA DEMO
        // TODO: Reativar após demonstração completa
        /*
        if ($user->hasRole('admin')) {
            // Se não tem 2FA configurado
            if (!$user->two_factor_secret) {
                return redirect()->route('2fa.setup')
                    ->with('error', '2FA é obrigatório para administradores. Configure agora.');
            }
            
            // Se não confirmou 2FA nesta sessão
            if (!session('2fa_verified')) {
                return redirect()->route('2fa.verify')
                    ->with('info', 'Por favor, insira seu código 2FA.');
            }
        }
        */
        
        return $next($request);
    }
}
