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
            if (strpos($html, $path) === false) {
                $html = $this->injectBeforeHeadEnd($html, $path);
            }
            // Remove affiliate link if present erroneously
            $html = str_replace('<link rel="stylesheet" href="'.$affPath.'">', '', $html);
        }

        // Affiliate login: force affiliate CSS and remove admin one
        if ($request->is('afiliado/login')) {
            // Replace admin with affiliate if needed
            if (strpos($html, $affPath) === false && strpos($html, $path) !== false) {
                $html = str_replace($path, $affPath, $html);
            }
            if (strpos($html, $affPath) === false) {
                $html = $this->injectBeforeHeadEnd($html, $affPath);
            }
            // Ensure admin link not present
            $html = str_replace('<link rel="stylesheet" href="'.$path.'">', '', $html);
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

