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
        if ($request->is('admin/login') || $request->is('admin')) {
            // Remove any affiliate link (absolute or relative, com ou sem query)
            $html = preg_replace('~<link[^>]+href=\"[^\"]*/css/custom-filament-theme-affiliate\\.css(?:\?[^\"]*)?\"[^>]*>~i', '', $html);
            // Garante presença do admin CSS (se estiver ausente)
            if (! preg_match('~href=\"[^\"]*/css/custom-filament-theme\\.css(?:\?[^\"]*)?\"~i', $html)) {
                $html = $this->injectBeforeHeadEnd($html, $path);
            }
        }

        // Affiliate login: force affiliate CSS e remove admin
        if ($request->is('afiliado/login') || $request->is('afiliado')) {
            // Troca o admin por affiliate quando presente
            $html = preg_replace('~href=\"[^\"]*/css/custom-filament-theme\\.css((?:\?[^\"]*)?)\"~i', 'href="'.$affPath.'$1"', $html);
            // Remove duplicados de affiliate (se houver vários)
            $html = preg_replace('~(<link[^>]+href=\"[^\"]*/css/custom-filament-theme-affiliate\\.css(?:\?[^\"]*)?\"[^>]*>)(?=.*\1)~is', '', $html, -1);
            // Garante presença do affiliate CSS
            if (! preg_match('~href=\"[^\"]*/css/custom-filament-theme-affiliate\\.css(?:\?[^\"]*)?\"~i', $html)) {
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
