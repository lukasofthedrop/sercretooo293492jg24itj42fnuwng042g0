<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action as TableAction;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Carbon\Carbon;

class AureoLinkGatewayPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'AureoLink Gateway';
    protected static ?string $title = 'AureoLink Gateway';
    protected static ?string $slug = 'aureolink-gateway';
    protected static ?string $navigationGroup = 'GATEWAYS PAGAMENTO';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.aureolink-gateway';

    public $clientId = '';
    public $clientSecret = '';
    public $webhookUrl = '';
    public $isEnabled = false;
    public $withdrawKey = '';

    public function mount()
    {
        $this->loadConfig();
    }

    public function loadConfig()
    {
        $config = DB::table('gateway_aureolink_config')->first();
        \Log::info('AureoLink loadConfig', ['config' => $config]);
        
        if ($config) {
            $this->clientId = $config->client_id ?? '';
            $this->clientSecret = $config->client_secret ?? '';
            $this->webhookUrl = $config->webhook_url ?? '';
            $this->isEnabled = (bool) $config->is_enabled;
            $this->withdrawKey = $config->withdraw_key ?? '';
        } else {
            // Valores padrão se não houver configuração
            $this->clientId = '';
            $this->clientSecret = '';
            $this->webhookUrl = 'https://bet.sorte365.fun/aureolink/webhook';
            $this->isEnabled = false;
            $this->withdrawKey = '';
        }
    }

    public function saveConfig()
    {
        try {
            // Debug: verificar dados recebidos
            \Log::info('AureoLink saveConfig chamado', [
                'clientId' => $this->clientId,
                'clientSecret' => substr($this->clientSecret, 0, 10) . '...',
                'webhookUrl' => $this->webhookUrl,
                'isEnabled' => $this->isEnabled,
                'withdrawKey' => substr($this->withdrawKey, 0, 10) . '...'
            ]);

            $result = DB::table('gateway_aureolink_config')->updateOrInsert(
                ['id' => 1],
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'webhook_url' => $this->webhookUrl,
                    'is_enabled' => $this->isEnabled ? 1 : 0,
                    'withdraw_key' => $this->withdrawKey,
                    'updated_at' => now(),
                ]
            );

            \Log::info('AureoLink saveConfig resultado', ['result' => $result]);

            // Se AureoLink foi ativado, desativar outros gateways
            if ($this->isEnabled) {
                $this->disableOtherGateways();
            }

            // Recarregar configurações
            $this->loadConfig();

            Notification::make()
                ->title('CONFIGURAÇÃO SALVA!')
                ->body('Configurações do AureoLink salvas com sucesso!')
                ->success()
                ->send();

        } catch (\Exception $e) {
            \Log::error('Erro ao salvar configuração AureoLink', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('ERRO!')
                ->body('Erro ao salvar configurações: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function disableOtherGateways()
    {
        try {
            // Atualizar a tabela gateways para desabilitar outros métodos
            DB::table('gateways')->update([
                'suitpay_production' => 0,
                'stripe_production' => 0,
                'ezze_production' => 0,
                'digito_production' => 0,
                'bspay_production' => 0,
                'mercadopago_production' => 0,
            ]);

            // Atualizar configurações específicas se existirem
            $tables = ['suitpay_configs', 'stripe_configs', 'ezze_configs', 'digito_configs', 'bspay_configs'];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->update(['is_enabled' => 0]);
                }
            }

            Notification::make()
                ->title('Outros Gateways Desativados')
                ->body('Todos os outros gateways foram automaticamente desativados.')
                ->info()
                ->send();

        } catch (\Exception $e) {
            \Log::error('Erro ao desabilitar outros gateways: ' . $e->getMessage());
        }
    }

    public function testConnection()
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->get('https://api.aureolink.com.br/v1/company');

            if ($response->successful()) {
                Notification::make()
                    ->title('SISTEMA ATIVADO')
                    ->body('Conexão com AureoLink estabelecida com sucesso!')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('SISTEMA ATIVADO')
                    ->body('Erro na conexão: ' . $response->body())
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('SISTEMA ATIVADO')
                ->body('Erro na conexão: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getTransactions()
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->get('https://api.aureolink.com.br/v1/transactions', [
                    'page' => 1,
                    'limit' => 50
                ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function getStats()
    {
        try {
            // Verificar se a coluna gateway existe
            $hasGatewayColumn = \Schema::hasColumn('deposits', 'gateway');
            
            if ($hasGatewayColumn) {
                $totalDeposits = Deposit::where('gateway', 'aureolink')->count();
                $totalWithdrawals = Withdrawal::where('gateway', 'aureolink')->count();
                $totalAmount = Deposit::where('gateway', 'aureolink')->sum('amount');
                $totalWithdrawalAmount = Withdrawal::where('gateway', 'aureolink')->sum('amount');
                $totalUsers = User::whereHas('deposits', function($query) {
                    $query->where('gateway', 'aureolink');
                })->count();
                
                // Estatísticas adicionais
                $pendingDeposits = Deposit::where('gateway', 'aureolink')->where('status', 'pending')->count();
                $approvedDeposits = Deposit::where('gateway', 'aureolink')->where('status', 'paid')->count();
                $failedDeposits = Deposit::where('gateway', 'aureolink')->where('status', 'cancel')->count();
                
                $pendingWithdrawals = Withdrawal::where('gateway', 'aureolink')->where('status', 'pending')->count();
                $approvedWithdrawals = Withdrawal::where('gateway', 'aureolink')->where('status', 'paid')->count();
                $failedWithdrawals = Withdrawal::where('gateway', 'aureolink')->where('status', 'cancel')->count();
            } else {
                // Se não existe a coluna, retornar zeros temporariamente
                $totalDeposits = 0;
                $totalWithdrawals = 0;
                $totalAmount = 0;
                $totalWithdrawalAmount = 0;
                $totalUsers = 0;
                $pendingDeposits = 0;
                $approvedDeposits = 0;
                $failedDeposits = 0;
                $pendingWithdrawals = 0;
                $approvedWithdrawals = 0;
                $failedWithdrawals = 0;
            }

            return [
                'total_deposits' => $totalDeposits,
                'total_withdrawals' => $totalWithdrawals,
                'total_amount' => $totalAmount,
                'total_withdrawal_amount' => $totalWithdrawalAmount,
                'total_users' => $totalUsers,
                'pending_deposits' => $pendingDeposits,
                'approved_deposits' => $approvedDeposits,
                'failed_deposits' => $failedDeposits,
                'pending_withdrawals' => $pendingWithdrawals,
                'approved_withdrawals' => $approvedWithdrawals,
                'failed_withdrawals' => $failedWithdrawals,
            ];
        } catch (\Exception $e) {
            return [
                'total_deposits' => 0,
                'total_withdrawals' => 0,
                'total_amount' => 0,
                'total_withdrawal_amount' => 0,
                'total_users' => 0,
                'pending_deposits' => 0,
                'approved_deposits' => 0,
                'failed_deposits' => 0,
                'pending_withdrawals' => 0,
                'approved_withdrawals' => 0,
                'failed_withdrawals' => 0,
            ];
        }
    }

    public function getDetailedTransactions()
    {
        try {
            $hasGatewayColumn = \Schema::hasColumn('deposits', 'gateway');
            
            if ($hasGatewayColumn) {
                // Buscar depósitos
                $deposits = Deposit::where('gateway', 'aureolink')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function($deposit) {
                        return [
                            'id' => $deposit->id,
                            'type' => 'deposit',
                            'user_name' => $deposit->user->name ?? 'N/A',
                            'user_email' => $deposit->user->email ?? 'N/A',
                            'amount' => $deposit->amount,
                            'status' => $deposit->status,
                            'created_at' => $deposit->created_at,
                            'payment_id' => $deposit->payment_id ?? 'N/A',
                        ];
                    });

                // Buscar saques
                $withdrawals = Withdrawal::where('gateway', 'aureolink')
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function($withdrawal) {
                        return [
                            'id' => $withdrawal->id,
                            'type' => 'withdrawal',
                            'user_name' => $withdrawal->user->name ?? 'N/A',
                            'user_email' => $withdrawal->user->email ?? 'N/A',
                            'amount' => $withdrawal->amount,
                            'status' => $withdrawal->status,
                            'created_at' => $withdrawal->created_at,
                            'payment_id' => $withdrawal->payment_id ?? 'N/A',
                        ];
                    });

                // Combinar e ordenar por data
                $allTransactions = $deposits->concat($withdrawals)
                    ->sortByDesc('created_at')
                    ->take(100)
                    ->values();

                return $allTransactions;
            }

            return collect([]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar transações detalhadas: ' . $e->getMessage());
            return collect([]);
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}

 