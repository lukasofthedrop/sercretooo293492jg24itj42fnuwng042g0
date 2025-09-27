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
        \Filament\Facades\Filament::serving(function () use ($assetUrl) {
            $panel = \Filament\Facades\Filament::getCurrentPanel();
            $panelId = $panel?->getId();

            if ($panelId === 'affiliate') {
                FilamentAsset::register([
                    Css::make('custom-filament-theme-affiliate', $assetUrl('css/custom-filament-theme-affiliate.css')),
                ]);
            } else {
                FilamentAsset::register([
                    Css::make('custom-filament-theme', $assetUrl('css/custom-filament-theme.css')),
                ]);
            }
        });

        FilamentAsset::register([
            Js::make('fontawesomepro-script', $assetUrl('js/fontawesomepro.min.js'))->loadedOnRequest(),
            Js::make('apexcharts', 'https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js'),
        ]);
    }
}
