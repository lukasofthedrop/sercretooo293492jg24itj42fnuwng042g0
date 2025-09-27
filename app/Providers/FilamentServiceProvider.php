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

        // Registra CSS base comum
        FilamentAsset::register([
            Css::make('custom-local-stylesheet', $assetUrl('css/filament.css')),
            Css::make('fontawesomepro-stylesheet', $assetUrl('css/fontawesomepro.min.css')),
        ]);

        // O CSS específico de cada painel é injetado diretamente via renderHook nos PanelProviders.

        FilamentAsset::register([
            Js::make('fontawesomepro-script', $assetUrl('js/fontawesomepro.min.js'))->loadedOnRequest(),
            Js::make('apexcharts', 'https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js'),
        ]);
    }
}
