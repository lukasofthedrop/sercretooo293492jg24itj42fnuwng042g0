<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class DDoSProtection
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $key = "ddos_check_{$ip}";
        $requests = Cache::get($key, 0);
        
        // Bloqueia se mais de 100 requests em 10 segundos
        if ($requests > 100) {
            // Adiciona à blacklist por 1 hora
            Cache::put("blacklist_{$ip}", true, 3600);
            abort(503, 'Service temporarily unavailable');
        }
        
        // Verifica blacklist
        if (Cache::has("blacklist_{$ip}")) {
            abort(403, 'Access denied');
        }
        
        // Incrementa contador
        Cache::put($key, $requests + 1, 10);
        
        return $next($request);
    }
}
