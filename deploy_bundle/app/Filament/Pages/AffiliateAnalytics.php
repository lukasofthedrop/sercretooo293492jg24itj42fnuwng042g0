<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Models\User;
use App\Models\AffiliateSettings;
use App\Services\AffiliateMetricsService;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\AffiliateHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AffiliateAnalytics extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'Análise Individual';
    protected static ?string $navigationGroup = 'GESTÃO DE AFILIADOS';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'analise-individual';
    protected static ?string $title = 'Análise Individual de Afiliados';
    
    protected static string $view = 'filament.pages.affiliate-analytics';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereNotNull('inviter_code')
                    ->where('email', '!=', 'lucrativa@bet.com')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                    
                TextColumn::make('inviter_code')
                    ->label('Código')
                    ->searchable()
                    ->copyable(),
                    
                TextColumn::make('referred_count')
                    ->label('Indicados')
                    ->getStateUsing(fn($record) => User::where('inviter', $record->id)->count())
                    ->sortable(),
                    
                TextColumn::make('active_referred')
                    ->label('Ativos')
                    ->getStateUsing(function($record) {
                        $referredIds = User::where('inviter', $record->id)->pluck('id');
                        return Deposit::whereIn('user_id', $referredIds)
                            ->where('status', 1)
                            ->distinct('user_id')
                            ->count('user_id');
                    }),
                    
                TextColumn::make('month_ngr')
                    ->label('NGR Mês')
                    ->getStateUsing(function($record) {
                        $ngrData = AffiliateMetricsService::calculateNGR($record->id);
                        return 'R$ ' . number_format($ngrData['ngr'], 2, ',', '.');
                    })
                    ->html(),
                    
                TextColumn::make('total_commission')
                    ->label('Comissões')
                    ->getStateUsing(function($record) {
                        return 'R$ ' . number_format($record->wallet->refer_rewards ?? 0, 2, ',', '.');
                    }),
                    
                TextColumn::make('tier')
                    ->label('Tier')
                    ->getStateUsing(function($record) {
                        $settings = AffiliateSettings::getOrCreateForUser($record->id);
                        $badges = [
                            'bronze' => '🥉 Bronze',
                            'silver' => '🥈 Prata',
                            'gold' => '🥇 Ouro',
                            'custom' => '💎 Custom'
                        ];
                        return $badges[$settings->tier] ?? $settings->tier;
                    })
                    ->html(),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('view_details')
                    ->label('Ver Análise')
                    ->icon('heroicon-o-chart-bar')
                    ->color('primary')
                    ->url(fn($record) => "/admin/analise-individual?affiliate_id={$record->id}"),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public function getAnalyticsData($affiliateId): array
    {
        $affiliate = User::find($affiliateId);
        if (!$affiliate) {
            return [];
        }

        $settings = AffiliateSettings::getOrCreateForUser($affiliateId);
        
        // Dados dos últimos 30 dias
        $last30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $referredIds = User::where('inviter', $affiliateId)->pluck('id');
            
            $deposits = Deposit::whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('status', 1)
                ->sum('amount');
                
            $withdrawals = Withdrawal::whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('status', 1)
                ->sum('amount');
                
            $last30Days[] = [
                'date' => $date->format('d/m'),
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'ngr' => $deposits - $withdrawals
            ];
        }
        
        // Top indicados por performance
        $topReferred = User::where('inviter', $affiliateId)
            ->withSum(['deposits as total_deposits' => function($q) {
                $q->where('status', 1);
            }], 'amount')
            ->withSum(['withdrawals as total_withdrawals' => function($q) {
                $q->where('status', 1);
            }], 'amount')
            ->orderByDesc('total_deposits')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'deposits' => $user->total_deposits ?? 0,
                    'withdrawals' => $user->total_withdrawals ?? 0,
                    'ngr' => ($user->total_deposits ?? 0) - ($user->total_withdrawals ?? 0),
                    'created_at' => $user->created_at->format('d/m/Y')
                ];
            });
        
        // Métricas gerais
        $metrics = AffiliateMetricsService::getAffiliateMetrics($affiliateId);
        
        return [
            'affiliate' => [
                'name' => $affiliate->name,
                'email' => $affiliate->email,
                'code' => $affiliate->inviter_code,
                'created_at' => $affiliate->created_at->format('d/m/Y'),
            ],
            'settings' => [
                'tier' => $settings->tier,
                'revshare_real' => $settings->revshare_percentage,
                'revshare_display' => $settings->revshare_display,
                'cpa_value' => $settings->cpa_value,
                'ngr_minimum' => $settings->ngr_minimum,
                'is_active' => $settings->is_active
            ],
            'metrics' => $metrics,
            'last_30_days' => $last30Days,
            'top_referred' => $topReferred
        ];
    }
}
