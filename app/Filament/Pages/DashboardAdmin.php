<?php

namespace App\Filament\Pages;


use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopGamesOverview;
use App\Filament\Widgets\TopUsersOverview;
use App\Filament\Widgets\Top5GamesCircularWidget;
use App\Filament\Widgets\UsersRankingColumnWidget;
use App\Filament\Widgets\ApexChartsWidget;

use App\Livewire\WalletOverview;
use Illuminate\Support\HtmlString;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class DashboardAdmin extends \Filament\Pages\Dashboard
{
    use HasFiltersForm, HasFiltersAction;

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable|null
     */
    public function getSubheading(): string| null|\Illuminate\Contracts\Support\Htmlable
    {
        return "Sistema operacional. Controle total da plataforma disponível.";
    }
    

    /**
     * @param Form $form
     * @return Form
     */
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                // Seção removida para evitar texto duplicado no meio da página
            ]);
    }

    /**
     * @return array|\Filament\Actions\Action[]|\Filament\Actions\ActionGroup[]
     */
    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label('Filtro')
                ->form([
                    DatePicker::make('startDate')->label('Data Incial'),
                    DatePicker::make('endDate')->label('Data Final'),
                ]),
            
            // Botões de teste para desenvolvimento - CORREÇÃO DEFINITIVA
            \Filament\Actions\Action::make('generateTestData')
                ->label('Gerar Dados de Teste')
                ->icon('heroicon-o-beaker')
                ->color('success')
                ->button()
                ->requiresConfirmation()
                ->modalHeading('Gerar Dados de Teste')
                ->modalDescription('Isso irá gerar dados fictícios para visualização. Confirma?')
                ->modalSubmitActionLabel('Gerar Dados')
                ->modalCancelActionLabel('Cancelar')
                ->action(function () {
                    try {
                        // Chamar o método do controller diretamente
                        $controller = new \App\Http\Controllers\Api\DashboardMetricsController();
                        $request = new \Illuminate\Http\Request(['period' => 'today']);
                        $response = $controller->generateTestData($request);
                        $data = json_decode($response->getContent(), true);
                        
                        if ($data && isset($data['test_mode'])) {
                            // Limpar cache para forçar atualização dos widgets
                            \Illuminate\Support\Facades\Cache::forget('dashboard_metrics_today');
                            \Illuminate\Support\Facades\Cache::forget('dashboard_metrics_sparkline_deposits');
                            \Illuminate\Support\Facades\Cache::forget('dashboard_metrics_sparkline_users');
                            \Illuminate\Support\Facades\Cache::forget('top5_games_chart_data');
                            \Illuminate\Support\Facades\Cache::forget('users_ranking_data');
                            
                            // Forçar refresh da página para carregar novos dados
                            $this->redirect('/admin');
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Dados de teste gerados com sucesso!')
                                ->body('Dashboard atualizado com dados fictícios.')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Erro ao gerar dados de teste')
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erro: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
                
            \Filament\Actions\Action::make('clearCache')
                ->label('Limpar Cache/Reset')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->button()
                ->requiresConfirmation()
                ->modalHeading('🔄 Sistema de Reset')
                ->modalDescription('Escolha o tipo de reset que deseja realizar:')
                ->modalSubmitActionLabel('Executar')
                ->modalCancelActionLabel('Cancelar')
                ->form([
                    \Filament\Forms\Components\Radio::make('reset_type')
                        ->label('Tipo de Reset')
                        ->options([
                            'cache' => '🧹 Limpar Cache - Remove apenas o cache temporário',
                            'full' => '⚠️ Reset Completo - Remove TODOS os dados de teste (mantém apenas admins)'
                        ])
                        ->default('cache')
                        ->reactive()
                        ->required(),
                    
                    \Filament\Forms\Components\TextInput::make('confirm_password')
                        ->label('Senha de Confirmação')
                        ->password()
                        ->placeholder('Digite sua senha para confirmar')
                        ->helperText('Necessário apenas para Reset Completo')
                        ->requiredIf('reset_type', 'full')
                        ->visible(fn ($get) => $get('reset_type') === 'full'),
                ])
                ->action(function (array $data) {
                    try {
                        $controller = new \App\Http\Controllers\Api\DashboardMetricsController();
                        
                        if ($data['reset_type'] === 'cache') {
                            // Reset simples - apenas cache
                            $response = $controller->clearCache();
                            $result = json_decode($response->getContent(), true);
                            
                            if ($result && $result['success']) {
                                \Illuminate\Support\Facades\Cache::flush();
                                
                                // Gerar dados de teste
                                $request = new \Illuminate\Http\Request(['period' => 'today']);
                                $controller->generateTestData($request);
                                
                                $this->redirect('/admin');
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('✅ Cache Limpo!')
                                    ->body('Cache do dashboard foi limpo com sucesso.')
                                    ->success()
                                    ->send();
                            }
                        } else {
                            // Reset completo do sistema
                            if (empty($data['confirm_password'])) {
                                \Filament\Notifications\Notification::make()
                                    ->title('❌ Senha Necessária')
                                    ->body('Digite sua senha para confirmar o reset completo.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            
                            // Preparar request com senha
                            $request = new \Illuminate\Http\Request([
                                'confirm_password' => $data['confirm_password']
                            ]);
                            
                            // Simular autenticação para o controller
                            auth()->login(auth()->user());
                            
                            $response = $controller->resetSystem($request);
                            $result = json_decode($response->getContent(), true);
                            
                            if ($response->status() === 200 && isset($result['success'])) {
                                // Fazer logout e redirecionar
                                auth()->logout();
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('🎉 Sistema Resetado!')
                                    ->body('Sistema limpo e pronto para operação real. Faça login novamente.')
                                    ->success()
                                    ->persistent()
                                    ->send();
                                
                                // Redirecionar para login
                                return redirect('/admin/login');
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('❌ Erro no Reset')
                                    ->body($result['message'] ?? 'Verifique sua senha e tente novamente.')
                                    ->danger()
                                    ->send();
                            }
                        }
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Erro: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }


    /**
     * @return string[]
     */
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,           // Métricas principais - RESTAURADO
            WalletOverview::class,          // Visão geral carteira - RESTAURADO  
            ApexChartsWidget::class,        // Gráficos profissionais existentes
            Top5GamesCircularWidget::class, // Top 5 Jogos - REATIVADO
            UsersRankingColumnWidget::class, // Ranking Usuários - REATIVADO
        ];
    }
}
