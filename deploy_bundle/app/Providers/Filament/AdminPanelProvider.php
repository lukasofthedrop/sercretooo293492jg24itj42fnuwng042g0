<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Pages\SyncGames;
use App\Filament\Pages\GamesKeyPage;
use App\Filament\Pages\GatewayPage;
use App\Filament\Pages\AureoLinkGatewayPage;
use App\Filament\Pages\LayoutCssCustom;
use App\Filament\Pages\SettingMailPage;
use App\Filament\Resources\AffiliateWithdrawResource;
use App\Filament\Resources\BannerResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\DepositResource;
use App\Filament\Resources\GameResource;
use App\Filament\Resources\MissionResource;
use App\Filament\Resources\ProviderResource;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\WalletResource;
use App\Filament\Resources\WithdrawalResource;


use App\Livewire\WalletOverview;
use App\Http\Middleware\AdminAccess;
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
use App\Filament\Pages\DashboardAdmin;
use App\Filament\Pages\AffiliateDashboard; // Dashboard de Afiliados

// NOVAS FUNÇOES 

use App\Filament\Resources\PromotionResource; // Adicionado
use App\Filament\Resources\CupomResource; // Adicionado
use App\Filament\Resources\VipResource; // Adicionado
use App\Filament\Resources\DistributionSystemResource; // Adicionado
use App\Filament\Resources\MinesConfigResource; // Adicionado
use App\Filament\Resources\DailyBonusConfigResource; // Adicionado
use App\Filament\Resources\GameOpenConfigResource; // Adicionado
use App\Filament\Pages\AffiliateHistory;
use App\Filament\Pages\AffiliateReports;
use App\Filament\Pages\AffiliateAnalytics;

class AdminPanelProvider extends PanelProvider
{



    /** 
     * @param Panel $panel
     * @return Panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path(env("FILAMENT_BASE_URL"))
            
            ->login()
            ->darkMode(condition: true, isForced: true)
            ->defaultThemeMode(ThemeMode::Dark)
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::hex('#00ff00'),
                'primary' => Color::hex('#00ff00'),
                'success' => Color::hex('#00ff00'),
                'warning' => Color::Orange,
            ])

            ->font('Roboto Condensed')
            ->brandLogo(fn () => view('filament.components.logo'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                DashboardAdmin::class,
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(true)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                WalletOverview::class,


            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $user = auth()->user();
                
                // Se não é admin, retorna menu vazio (será redirecionado para /afiliado)
                if (!$user || !$user->hasRole('admin')) {
                    return $builder->groups([]);
                }
                
                $groups = [];
                
                // Dashboard - só admin vê
                $groups[] = NavigationGroup::make()
                    ->items([
                        NavigationItem::make('dashboard')
                            ->icon('heroicon-o-home')
                            ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                            ->url(fn (): string => DashboardAdmin::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.pages.dashboard')),
                    ]);
                
                // Definições - só admin
                $groups[] = NavigationGroup::make('DEFINIÇÕES DA PLATAFORMA')
                    ->items([
                        NavigationItem::make('settings')
                        ->icon('heroicon-o-cog')
                        ->label(fn (): string => 'DEFINIÇÕES DA PLATAFORMA')
                        ->url(fn (): string => SettingResource::getUrl())
                        ->isActiveWhen(fn () => request()->routeIs('filament.pages.settings')),


                        NavigationItem::make('custom-layout')
                            ->icon('heroicon-o-paint-brush')
                            ->label(fn (): string => 'DEFINIÇÕES DE CSS E PIXELS')
                            ->url(fn (): string => LayoutCssCustom::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.pages.layout-css-custom')),

                        NavigationItem::make('gateway')
                            ->icon('heroicon-o-credit-card')
                            ->label(fn (): string => 'DEFINIÇÕES DE PAGAMENTO')
                            ->url(fn (): string => GatewayPage::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.pages.gateway-page')), 

                        NavigationItem::make('games-key')
                            ->icon('heroicon-o-cpu-chip')
                            ->label(fn (): string => 'DEFINIÇÕES DA API DE JOGOS')
                            ->url(fn (): string => GamesKeyPage::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.pages.games-key-page')),

                        // LICENSE API
                        NavigationItem::make('license-key')
                            ->icon('heroicon-o-cpu-chip')
                            ->label(fn (): string => 'LICENSE API')
                            ->url(fn (): string => url('/admin/license-api'))
                            ->isActiveWhen(fn () => request()->is('admin/license-api*')),

                        // AUREOLINK GATEWAY
                        NavigationItem::make('aureolink-gateway')
                            ->icon('heroicon-o-credit-card')
                            ->label(fn (): string => 'AUREOLINK GATEWAY')
                            ->url(fn (): string => AureoLinkGatewayPage::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.aureolink-gateway')),


                        ...BannerResource::getNavigationItems(),


                        NavigationItem::make('setting-mail')
                            ->icon('heroicon-o-inbox-stack')
                            ->label(fn (): string => 'DEFINIÇÕES DE E-MAIL')
                            ->url(fn (): string => SettingMailPage::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.pages.setting-mail-page')),
                    ]);

                // Promoções - só admin
                $groups[] = NavigationGroup::make('PROMOÇÕES DA PLATAFORMA')
                ->items([
                    ...CupomResource::getNavigationItems(),  // Adiciona o recurso Cupom ao grupo
                    ...PromotionResource::getNavigationItems(),  // Adiciona o recurso Promotion ao grupo
                    ...MissionResource::getNavigationItems(),
                    ...VipResource::getNavigationItems(),
                    ]);

                // Gestão da Plataforma - só admin
                $groups[] = NavigationGroup::make('GESTÃO DA PLATAFORMA')
                    ->items([

                        ...UserResource::getNavigationItems(),    
                        ...WalletResource::getNavigationItems(),
                        ...DepositResource::getNavigationItems(),
                        ...DistributionSystemResource::getNavigationItems(),
                        ...DailyBonusConfigResource::getNavigationItems(),
                        ...GameOpenConfigResource::getNavigationItems(),

                    ]);

                // Gestão de Afiliados - apenas para ADMIN (gestão)
                $affiliateItems = [
                    NavigationItem::make('affiliate-management')
                        ->icon('heroicon-o-users')
                        ->label(fn (): string => 'Gestão de Afiliados')
                        ->url(fn (): string => AffiliateHistory::getUrl())
                        ->isActiveWhen(fn () => request()->is('admin/gestao-afiliados*')),
                    
                    NavigationItem::make('affiliate-reports')
                        ->icon('heroicon-o-chart-pie')
                        ->label(fn (): string => 'Relatórios de Afiliados')
                        ->url(fn (): string => AffiliateReports::getUrl())
                        ->isActiveWhen(fn () => request()->is('admin/afiliado/relatorios*')),
                    
                    NavigationItem::make('affiliate-analytics')
                        ->icon('heroicon-o-magnifying-glass-circle')
                        ->label(fn (): string => 'Análise Individual')
                        ->url(fn (): string => AffiliateAnalytics::getUrl())
                        ->isActiveWhen(fn () => request()->is('admin/afiliado/analise*')),
                ];
                
                $groups[] = NavigationGroup::make('GESTÃO DE AFILIADOS')
                    ->items($affiliateItems);

                // Saques - só admin
                $groups[] = NavigationGroup::make('SAQUES DA PLATAFORMA')
                    ->items([
                        NavigationItem::make('withdraw_affiliates')
                            ->icon('heroicon-o-banknotes')
                            ->label(fn (): string => 'SAQUES AFILIADOS')
                            ->url(fn (): string => AffiliateWithdrawResource::getUrl())
                            ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.sub-affiliates.index')),
                        ...WithdrawalResource::getNavigationItems(),
                    ]);

                // Jogos - só admin
                $groups[] = NavigationGroup::make('JOGOS DA PLATAFORMA')
                    ->items([
                        // bx NavigationItem::make(label: 'sync-games')
                         //   ->icon('heroicon-o-arrow-path')
                          //    ->label(fn (): string => 'Sincronizar Jogos')
                          //    ->url(fn (): string => SyncGames::getUrl())
                           //   ->isActiveWhen(fn () => request()->routeIs('filament.pages.sync-games')),
                        ...CategoryResource::getNavigationItems(),
                        ...ProviderResource::getNavigationItems(),
                        ...GameResource::getNavigationItems(),
                    ]);

                // Sistema - só admin
                $groups[] = NavigationGroup::make('Otimização')
                    ->label('SISTEMA')
                    ->items([
                        NavigationItem::make('LIMPAR CACHE')
                            ->url(url('/clear'), shouldOpenInNewTab: false)
                            ->icon('heroicon-o-trash'),
                    ]);
                
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
                AdminAccess::class,
            ])
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make());
    }
}
