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

        \Log::info('TwoFactorMiddleware start', [
            'user_id' => $user ? $user->id : null,
            'path' => $request->path(),
            'is_authenticated' => auth()->check(),
        ]);

        // Se usuário não está autenticado, continua
        if (!$user) {
            \Log::info('TwoFactorMiddleware: user not authenticated, continuing');
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

        \Log::info('TwoFactorMiddleware check', [
            'isAdminArea' => $isAdminArea,
            'adminBasePath' => $adminBasePath,
            'currentPath' => $currentPath,
            'routeName' => $routeName,
        ]);

        if (! $isAdminArea) {
            \Log::info('TwoFactorMiddleware: not admin area, continuing');
            return $next($request);
        }

        // Temporary bypass for test users during debugging
        if (str_contains($user->email, '@lucrativa.bet')) {
            \Log::info('TwoFactorMiddleware: bypassing 2FA for test user');
            session(['2fa_verified' => true]);
            return $next($request);
        }

        if ($user->hasRole('admin') || $user->hasRole('Admin')) {
            \Log::info('TwoFactorMiddleware: user has admin role', [
                'has_2fa_secret' => !empty($user->two_factor_secret),
                'has_2fa_confirmed' => !empty($user->two_factor_confirmed_at),
                'session_2fa_verified' => session('2fa_verified'),
            ]);

            if (!$user->two_factor_secret || !$user->two_factor_confirmed_at) {
                \Log::info('TwoFactorMiddleware: redirecting to 2fa.setup');
                return redirect()->route('2fa.setup')
                    ->with('error', '2FA é obrigatório para administradores. Configure agora.');
            }

            if (!session('2fa_verified')) {
                \Log::info('TwoFactorMiddleware: redirecting to 2fa.verify');
                return redirect()->route('2fa.verify')
                    ->with('info', 'Por favor, insira seu código 2FA.');
            }
        }

        \Log::info('TwoFactorMiddleware: allowing access');
        return $next($request);
    }
}
