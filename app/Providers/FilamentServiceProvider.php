<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $assetUrl = function (string $path): string {
            return app()->environment(['local', 'testing']) ? asset($path) : secure_asset($path);
        };

        // Registra CSS base comum
        FilamentAsset::register([
            Css::make('custom-local-stylesheet', $assetUrl('css/filament.css')),
            Css::make('fontawesomepro-stylesheet', $assetUrl('css/fontawesomepro.min.css')),
        ]);

        // Carrega CSS específico por painel (admin vs afiliado) durante o evento Serving
        Filament::serving(function () use ($assetUrl) {
            $panel = Filament::getCurrentPanel();
            $panelId = $panel?->getId();

            // Fallback para detecção por URL quando o panel ainda não está resolvido (ex.: tela de login)
            $isAffiliateContext = $panelId === 'affiliate'
                || request()->is('afiliado*');

            if ($isAffiliateContext) {
                FilamentAsset::register([
                    Css::make('custom-filament-theme-affiliate', $assetUrl('css/custom-filament-theme-affiliate.css')),
                ]);
                return;
            }

            FilamentAsset::register([
                Css::make('custom-filament-theme', $assetUrl('css/custom-filament-theme.css')),
            ]);
        });

        FilamentAsset::register([
            Js::make('fontawesomepro-script', $assetUrl('js/fontawesomepro.min.js'))->loadedOnRequest(),
            Js::make('apexcharts', 'https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js'),
        ]);
    }
}
