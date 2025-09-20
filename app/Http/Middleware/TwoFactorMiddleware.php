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
        
        if ($user->hasRole('admin')) {
            if (!$user->two_factor_secret || !$user->two_factor_confirmed_at) {
                if (!$is2FARoute) {
                    return redirect()->route('2fa.setup')
                        ->with('error', '2FA é obrigatório para administradores. Configure agora.');
                }

                return $next($request);
            }

            if (!session('2fa_verified')) {
                if (!$is2FARoute) {
                    return redirect()->route('2fa.verify')
                        ->with('info', 'Por favor, insira seu código 2FA.');
                }
            }
        }
        
        return $next($request);
    }
}
