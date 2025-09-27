<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectLoginTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $request->isMethod('GET')) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type', '');
        if (stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $path = '/css/custom-filament-theme.css';
        $affPath = '/css/custom-filament-theme-affiliate.css';

        $html = $response->getContent();
        if (! is_string($html) || $html === '') {
            return $response;
        }

        // Admin login: force admin CSS and ensure affiliate CSS not injected
        if ($request->is('admin/login')) {
            // Ensure admin link exists
            if (! preg_match('~href=\"?/css/custom-filament-theme\\.css\"?~i', $html)) {
                $html = $this->injectBeforeHeadEnd($html, $path);
            }
            // Remove any affiliate link tag variants if present
            $html = preg_replace('~<link[^>]+href=\"?/css/custom-filament-theme-affiliate\\.css\"?[^>]*>~i', '', $html);
        }

        // Affiliate login: force affiliate CSS and remove admin one
        if ($request->is('afiliado/login')) {
            // Remove any admin link tag variants
            $html = preg_replace('~<link[^>]+href=\"?/css/custom-filament-theme\\.css\"?[^>]*>~i', '', $html);
            // Ensure affiliate link exists
            if (! preg_match('~href=\"?/css/custom-filament-theme-affiliate\\.css\"?~i', $html)) {
                $html = $this->injectBeforeHeadEnd($html, $affPath);
            }
        }

        $response->setContent($html);
        return $response;
    }

    private function injectBeforeHeadEnd(string $html, string $href): string
    {
        $tag = '<link rel="stylesheet" href="'.$href.'">';
        $pos = stripos($html, '</head>');
        if ($pos === false) {
            return $html.$tag; // fallback append
        }
        return substr($html, 0, $pos).$tag.substr($html, $pos);
    }
}
