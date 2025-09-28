<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Enums\ThemeMode;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\InjectLoginTheme;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->darkMode(condition: true, isForced: true)
            ->defaultThemeMode(ThemeMode::Dark)
            ->colors([
                'primary' => Color::Green,
                'gray' => Color::Gray,
            ])
            ->brandLogo(fn () => view('filament.components.logo'))
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->renderHook(PanelsRenderHook::HEAD_END, function () {
                $file = public_path('css/custom-filament-theme.css');
                $v = file_exists($file) ? filemtime($file) : time();
                return '<link rel="stylesheet" href="' . asset('css/custom-filament-theme.css') . '?v=' . $v . '">';
            })
            ->renderHook('panels::auth.head.end', function () {
                $file = public_path('css/custom-filament-theme.css');
                $v = file_exists($file) ? filemtime($file) : time();
                return '<link rel="stylesheet" href="' . asset('css/custom-filament-theme.css') . '?v=' . $v . '">';
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                \Filament\Pages\Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\Top5GamesChart::class,
                \App\Filament\Widgets\UsersRankingColumnWidget::class,
                \App\Filament\Widgets\TopUsersOverview::class,
                \App\Filament\Widgets\Top5GamesCircularWidget::class,
                \App\Filament\Widgets\Top5GamesInfographic::class,
                \App\Filament\Widgets\TopGamesOverview::class,
                \App\Filament\Widgets\TopUsersInfographic::class,
                \App\Filament\Widgets\UsersRankingInfographic::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                InjectLoginTheme::class,
            ])
            ->authMiddleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SubstituteBindings::class,
            ]);
    }
}
