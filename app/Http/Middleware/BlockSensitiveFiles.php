<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockSensitiveFiles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $blockedPatterns = [
            '/.env',
            '/.git',
            '/.gitignore',
            '/composer.json',
            '/composer.lock',
            '/package.json',
            '/package-lock.json',
            '/webpack.mix.js',
            '/yarn.lock',
            '/phpunit.xml',
            '/README.md',
            '/.htaccess'
        ];

        $path = $request->getPathInfo();
        
        foreach ($blockedPatterns as $pattern) {
            if (strpos($path, $pattern) !== false) {
                abort(404);
            }
        }

        return $next($request);
    }
}