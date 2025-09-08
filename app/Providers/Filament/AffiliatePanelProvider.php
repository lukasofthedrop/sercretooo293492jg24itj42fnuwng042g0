<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AffiliateDashboard;
use App\Filament\Pages\MyConversions;
use App\Filament\Pages\MyUsers;
use App\Filament\Pages\MyPayments;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Enums\ThemeMode;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\AffiliateAccess;

class AffiliatePanelProvider extends PanelProvider
{

    /**
     * @param Panel $panel
     * @return Panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('affiliate')
            ->path('afiliado')
            ->login()
            ->authGuard('web')
            ->darkMode(condition: true, isForced: true)
            ->defaultThemeMode(ThemeMode::Dark)
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::hex('#3BC117'),
                'primary' => Color::hex('#3BC117'),
                'success' => Color::hex('#3BC117'),
                'warning' => Color::Orange,
            ])
            ->font('Roboto Condensed')
            ->brandLogo(fn () => view('filament.components.logo'))
            ->pages([
                AffiliateDashboard::class,
                MyConversions::class,
                MyUsers::class,
                MyPayments::class,
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(true)
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $user = auth()->user();
                
                // Se não há usuário logado, retorna menu vazio
                if (!$user || !$user->inviter_code) {
                    return $builder->groups([]);
                }
                
                $groups = [];
                
                // Dashboard Principal
                $groups[] = NavigationGroup::make()
                    ->items([
                        NavigationItem::make('affiliate-dashboard')
                            ->icon('heroicon-o-home')
                            ->label('Minha Dashboard')
                            ->url(fn (): string => AffiliateDashboard::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.affiliate.pages.affiliate-dashboard')),
                    ]);
                
                // Gestão de Afiliados
                $affiliateItems = [
                    // Minhas Conversões
                    NavigationItem::make('my-conversions')
                        ->icon('heroicon-o-chart-bar')
                        ->label('Minhas Conversões')
                        ->url(fn (): string => MyConversions::getUrl())
                        ->isActiveWhen(fn () => request()->is('afiliado/minhas-conversoes*')),
                    
                    // Usuários Indicados
                    NavigationItem::make('referred-users')
                        ->icon('heroicon-o-users')
                        ->label('Usuários Indicados')
                        ->url(fn (): string => MyUsers::getUrl())
                        ->isActiveWhen(fn () => request()->is('afiliado/usuarios-indicados*')),
                    
                    // Histórico de Pagamentos
                    NavigationItem::make('payment-history')
                        ->icon('heroicon-o-banknotes')
                        ->label('Histórico de Pagamentos')
                        ->url(fn (): string => MyPayments::getUrl())
                        ->isActiveWhen(fn () => request()->is('afiliado/historico-pagamentos*')),
                    
                    // Remarketing - BLOQUEADA
                    NavigationItem::make('remarketing')
                        ->icon('heroicon-o-megaphone')
                        ->label('🔒 Remarketing - EM BREVE')
                        ->url('#')
                        ->isActiveWhen(fn () => false),
                ];
                
                $groups[] = NavigationGroup::make('AFILIADO')
                    ->items($affiliateItems);
                
                return $builder->groups($groups);
            })
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                AffiliateAccess::class,
            ]);
    }
}