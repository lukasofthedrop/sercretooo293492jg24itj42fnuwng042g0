<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustVercelProxies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Trust Vercel proxies
        $request->setTrustedProxies(
            ['cf', 'localhost', '127.0.0.1'],
            Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO
        );

        return $next($request);
    }
}