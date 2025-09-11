<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->ip();
        
        // Limites por tipo de rota
        $limits = [
            'api' => ['attempts' => 60, 'decay' => 60],
            'login' => ['attempts' => 5, 'decay' => 300],
            'register' => ['attempts' => 3, 'decay' => 600],
            'default' => ['attempts' => 100, 'decay' => 60]
        ];
        
        $routeType = 'default';
        if (str_contains($request->path(), 'api/')) $routeType = 'api';
        if (str_contains($request->path(), 'login')) $routeType = 'login';
        if (str_contains($request->path(), 'register')) $routeType = 'register';
        
        $limit = $limits[$routeType];
        
        if (RateLimiter::tooManyAttempts($key, $limit['attempts'])) {
            return response()->json([
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }
        
        RateLimiter::hit($key, $limit['decay']);
        
        return $next($request);
    }
}
