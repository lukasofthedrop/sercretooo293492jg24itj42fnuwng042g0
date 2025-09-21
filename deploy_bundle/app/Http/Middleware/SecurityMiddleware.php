<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityMiddleware
{
    /**
     * Padrões perigosos para detectar
     */
    private $dangerousPatterns = [
        'sql' => '/(union|select|insert|update|delete|drop|create|alter|exec|execute|script|javascript|onerror|onload|onclick)/i',
        'xss' => '/<script|<iframe|javascript:|onerror=|onclick=|onload=/i',
        'path' => '/(\.\.|\.\.\/|\.\.\\\\)/i'
    ];

    public function handle(Request $request, Closure $next)
    {
        // Validar todos os inputs
        $allInput = json_encode($request->all());
        
        foreach ($this->dangerousPatterns as $type => $pattern) {
            if (preg_match($pattern, $allInput)) {
                \Log::warning("Tentativa de ataque detectada: $type", [
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'user_agent' => $request->userAgent()
                ]);
                
                abort(403, 'Requisição bloqueada por segurança');
            }
        }
        
        // Headers de segurança
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        }
        
        return $response;
    }
}
