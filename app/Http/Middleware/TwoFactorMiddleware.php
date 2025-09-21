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

        $currentPath = ltrim($request->path(), '/');
        $routeName = $request->route()?->getName() ?? '';
        $adminBasePath = trim(config('filament.panels.admin.path', env('FILAMENT_BASE_URL', 'admin')), '/');

        // Verifica se está em uma rota 2FA, logout, ou autenticação
        $is2FARoute = str_starts_with($currentPath, '2fa/') ||
                      str_starts_with($currentPath, '2fa') ||
                      $request->routeIs('2fa.*') ||
                      $request->routeIs('logout');

        $isAuthRoute = str_contains($currentPath, 'login') ||
                       str_contains($currentPath, 'register') ||
                       str_contains($currentPath, 'password-reset') ||
                       $request->routeIs('filament.admin.auth.*');

        if ($is2FARoute || $isAuthRoute) {
            return $next($request);
        }

        $isAdminArea = ($adminBasePath && str_starts_with($currentPath, $adminBasePath))
            || str_starts_with($routeName, 'filament.admin.');

        if (! $isAdminArea) {
            return $next($request);
        }

        if ($user->hasRole('admin')) {
            if (!$user->two_factor_secret || !$user->two_factor_confirmed_at) {
                return redirect()->route('2fa.setup')
                    ->with('error', '2FA é obrigatório para administradores. Configure agora.');
            }

            if (!session('2fa_verified')) {
                return redirect()->route('2fa.verify')
                    ->with('info', 'Por favor, insira seu código 2FA.');
            }
        }

        return $next($request);
    }
}
