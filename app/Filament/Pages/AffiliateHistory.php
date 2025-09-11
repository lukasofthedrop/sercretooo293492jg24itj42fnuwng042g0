<?php

namespace App\Filament\Pages;

use App\Models\AffiliateHistory as ModelsAffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\GamesKey;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;

use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use App\Models\AffiliateSettings;
use App\Services\AffiliateMetricsService;
use Filament\Tables\Actions\DetailsActin;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class AffiliateHistory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Gestão de Afiliados';
    protected static ?string $navigationGroup = 'GESTÃO DE AFILIADOS';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.affiliate';

    protected static ?string $title = 'Afiliados';
    protected static ?string $model = User::class;

    protected static ?string $slug = 'gestao-afiliados';

    /**
     * @dev  
     * @return bool
     */
    public static function canAccess(): bool
    {
        // TEMPORARIAMENTE DESABILITADO PARA DEBUG
        return true; 
        // return auth()->user()->hasRole('admin'); // Controla o acesso total à página
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla a visualização de elementos específicos
    }
    


    public ?array $data = [];


    public function table(Table $table): Table
    {  
        return $table
        ->query(self::$model::query()
            ->whereNotNull('inviter_code')
            ->where('email', '!=', 'lucrativa@bet.com')
        )
        ->defaultSort('created_at', 'desc')

        ->columns([
            TextColumn::make("id")->label("ID"),
            TextColumn::make("name")->label("Nome")->searchable(),
            TextColumn::make("email")->label("Email")->searchable(),
            TextColumn::make("wallet.refer_rewards")->label("Saldo")->money("BRL")->sortable(),
            TextColumn::make("affiliateHistory.logs")->label("RevShare")->formatStateUsing(function($record) {
                $count = 0;
                $affiliates = ModelsAffiliateHistory::where("inviter", $record->id)->where("status", 1)->get();
                foreach($affiliates as $item){
                    if ($item->commission_type == "revshare") {
                        $count += $item->commission;
                    }
                }
                
                return "R$". number_format($count, 2, ".", ",");

            })->default(0)->sortable(query: function (Builder $query, string $direction): Builder {
                return $query->leftJoin('affiliate_histories', 'affiliate_histories.user_id', '=', 'users.id')
                ->orderByRaw("FIELD(affiliate_histories.commission_type, 'revshare') " . $direction)
                ->select('affiliate_histories.commission_type', 'affiliate_histories.commission', 'users.*');
            }),
            TextColumn::make("affiliateHistory.commission")->label("CPA")->formatStateUsing(function($record) {
                $count = 0;
                $affiliates = ModelsAffiliateHistory::where("inviter", $record->id)->where("status", 1)->get();
                foreach($affiliates as $item){
                    if ($item->commission_type == "cpa" ) {
                        $count += $item->commission;
                    }
                }
                
                return "R$". number_format($count, 2, ".", ",");
            })->default(0)->sortable(query: function (Builder $query, string $direction): Builder {
                return $query->leftJoin('affiliate_histories', 'affiliate_histories.user_id', '=', 'users.id')
                ->orderByRaw("FIELD(affiliate_histories.commission_type, 'cpa') " . $direction)
                ->select('affiliate_histories.commission_type', 'affiliate_histories.commission', 'users.*');
            }),
            TextColumn::make("ngr")->label("NGR")->formatStateUsing(function($record) {
                $ngrData = AffiliateMetricsService::calculateNGR($record->id);
                $color = $ngrData['qualified'] ? 'green' : 'red';
                $icon = $ngrData['qualified'] ? '✅' : '⚠️';
                return "<span style='color: {$color};'>{$icon} R$ " . number_format($ngrData['ngr'], 2, ',', '.') . "</span>";
            })->html()->sortable(),
            TextColumn::make("inviter")->label("Afiliados")->formatStateUsing(function ($record) {
                return User::where("inviter", $record->id)->count();
            })->default(0),
            TextColumn::make("affiliateSettings.tier")->label("Tier")->formatStateUsing(function ($record) {
                $settings = AffiliateSettings::getOrCreateForUser($record->id);
                $badges = [
                    'bronze' => '🥉 Bronze',
                    'silver' => '🥈 Prata',
                    'gold' => '🥇 Ouro',
                    'custom' => '💎 Custom'
                ];
                return $badges[$settings->tier] ?? $settings->tier;
            })->html(),
            TextColumn::make("affiliateSettings.is_active")->label("Status")->formatStateUsing(function ($record) {
                $settings = AffiliateSettings::getOrCreateForUser($record->id);
                return $settings->is_active 
                    ? '<span style="color: green;">✅ Ativo</span>' 
                    : '<span style="color: red;">❌ Inativo</span>';
            })->html(),
            TextColumn::make("created_at")->label("Criado em")->dateTime()->sortable()

        ])->actions([
           Action::make('details')
               ->label('Ver Detalhes')
               ->icon('heroicon-o-eye')
               ->url(fn($record) => "/admin/afiliado/details/{$record->id}"),
               
           Action::make('settings')
               ->label('Configurações')
               ->icon('heroicon-o-cog')
               ->color('warning')
               ->modalHeading('Configurações do Afiliado')
               ->modalWidth('2xl')
               ->form(function($record) {
                   $settings = AffiliateSettings::getOrCreateForUser($record->id);
                   
                   return [
                       Forms\Components\Section::make('Configurações de Comissão')
                           ->schema([
                               Forms\Components\Grid::make(2)
                                   ->schema([
                                       Forms\Components\TextInput::make('revshare_percentage')
                                           ->label('NGR Real (%) - VALOR APLICADO')
                                           ->helperText('Este é o valor REAL que será aplicado nos cálculos')
                                           ->numeric()
                                           ->minValue(0)
                                           ->maxValue(100)
                                           ->step(0.01)
                                           ->suffix('%')
                                           ->default($settings->revshare_percentage),
                                       
                                       Forms\Components\TextInput::make('revshare_display')
                                           ->label('RevShare Visível (%) - FAKE')
                                           ->helperText('Este valor aparece para o afiliado (não afeta cálculos)')
                                           ->numeric()
                                           ->minValue(0)
                                           ->maxValue(100)
                                           ->step(0.01)
                                           ->suffix('%')
                                           ->default($settings->revshare_display),
                                   ]),
                               
                               Forms\Components\Grid::make(2)
                                   ->schema([
                                       Forms\Components\TextInput::make('cpa_value')
                                           ->label('CPA (R$)')
                                           ->numeric()
                                           ->minValue(0)
                                           ->step(0.01)
                                           ->prefix('R$')
                                           ->default($settings->cpa_value),
                                       
                                       Forms\Components\TextInput::make('ngr_minimum')
                                           ->label('NGR Mínimo (R$)')
                                           ->numeric()
                                           ->minValue(0)
                                           ->step(0.01)
                                           ->prefix('R$')
                                           ->default($settings->ngr_minimum),
                                   ]),
                           ]),
                       
                       Forms\Components\Section::make('Tier e Período')
                           ->schema([
                               Forms\Components\Grid::make(2)
                                   ->schema([
                                       Forms\Components\Select::make('tier')
                                           ->label('Tier do Afiliado')
                                           ->options([
                                               'bronze' => 'Bronze',
                                               'silver' => 'Prata',
                                               'gold' => 'Ouro',
                                               'custom' => 'Personalizado'
                                           ])
                                           ->default($settings->tier),
                                       
                                       Forms\Components\Select::make('calculation_period')
                                           ->label('Período de Cálculo')
                                           ->options([
                                               'daily' => 'Diário',
                                               'weekly' => 'Semanal',
                                               'monthly' => 'Mensal'
                                           ])
                                           ->default($settings->calculation_period),
                                   ]),
                           ]),
                       
                       Forms\Components\Section::make('Permissões de Visualização')
                           ->schema([
                               Forms\Components\Grid::make(2)
                                   ->schema([
                                       Forms\Components\Toggle::make('can_see_ngr')
                                           ->label('Pode ver NGR')
                                           ->default($settings->can_see_ngr),
                                       
                                       Forms\Components\Toggle::make('can_see_deposits')
                                           ->label('Pode ver Depósitos')
                                           ->default($settings->can_see_deposits),
                                       
                                       Forms\Components\Toggle::make('can_see_losses')
                                           ->label('Pode ver Perdas')
                                           ->default($settings->can_see_losses),
                                       
                                       Forms\Components\Toggle::make('can_see_reports')
                                           ->label('Pode ver Relatórios')
                                           ->default($settings->can_see_reports),
                                   ]),
                           ]),
                       
                       Forms\Components\Section::make('Status')
                           ->schema([
                               Forms\Components\Toggle::make('is_active')
                                   ->label('Afiliado Ativo')
                                   ->helperText('Desativar suspende todas as comissões')
                                   ->default($settings->is_active),
                           ]),
                   ];
               })
               ->action(function(array $data, $record) {
                   $settings = AffiliateSettings::getOrCreateForUser($record->id);
                   
                   $settings->update([
                       'revshare_percentage' => $data['revshare_percentage'],
                       'revshare_display' => $data['revshare_display'],
                       'cpa_value' => $data['cpa_value'],
                       'ngr_minimum' => $data['ngr_minimum'],
                       'tier' => $data['tier'],
                       'calculation_period' => $data['calculation_period'],
                       'can_see_ngr' => $data['can_see_ngr'],
                       'can_see_deposits' => $data['can_see_deposits'],
                       'can_see_losses' => $data['can_see_losses'],
                       'can_see_reports' => $data['can_see_reports'],
                       'is_active' => $data['is_active'],
                   ]);
                   
                   \Filament\Notifications\Notification::make()
                       ->title('Configurações Atualizadas')
                       ->success()
                       ->body("As configurações do afiliado {$record->name} foram atualizadas com sucesso.")
                       ->send();
               }),
            
        ]);



    
    }

 
}
