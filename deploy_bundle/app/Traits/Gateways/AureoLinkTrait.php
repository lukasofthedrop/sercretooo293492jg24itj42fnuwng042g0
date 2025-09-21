<?php

namespace App\Traits\Gateways;

use App\Models\Gateway;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

trait AureoLinkTrait
{
    protected $aureolinkBaseUrl = 'https://api.aureolink.com.br/v1';

    /**
     * Gerar QR Code para depósito via AureoLink
     */
    public function requestQrcodeAureoLink($request)
    {
        try {
            // Verificar se o gateway está ativo
            $config = \DB::table('gateway_aureolink_config')->first();
            
            if (!$config || !$config->is_enabled) {
                return response()->json([
                    'error' => true,
                    'message' => 'AureoLink gateway não está ativado'
                ], 400);
            }

            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'error' => true,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Validar dados
            $amount = floatval($request->amount);
            if ($amount <= 0) {
                return response()->json([
                    'error' => true,
                    'message' => 'Valor inválido'
                ], 400);
            }

            // Criar transação/depósito no banco
            $deposit = Deposit::create([
                'payment_id' => 'aureolink_' . time() . '_' . $user->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'pix',
                'proof' => '',
                'status' => 'pending',
                'currency' => 'BRL',
                'gateway' => 'aureolink'
            ]);

            // Preparar dados para a API AureoLink
            $payload = [
                'amount' => (int)($amount * 100), // Converter para centavos
                'paymentMethod' => 'pix',
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'document' => [
                        'type' => 'cpf',
                        'number' => $user->cpf ?? '00000000000',
                    ]
                ],
                'items' => [
                    [
                        'title' => 'Depósito na plataforma',
                        'unitPrice' => (int)($amount * 100),
                        'quantity' => 1,
                        'tangible' => false
                    ]
                ],
                'externalRef' => $deposit->payment_id
            ];

            // Fazer requisição para a API AureoLink
            $response = \Http::withBasicAuth(
                $config->client_id,
                $config->client_secret
            )->post($this->aureolinkBaseUrl . '/transactions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Atualizar depósito com dados da AureoLink
                $deposit->update([
                    'payment_id' => $data['id'] ?? $deposit->payment_id,
                    'proof' => json_encode($data)
                ]);

                \Log::info("AureoLink deposit created successfully", [
                    'deposit_id' => $deposit->id,
                    'aureolink_id' => $data['id'] ?? null,
                    'user_id' => $user->id,
                    'amount' => $amount
                ]);

                return response()->json([
                    'success' => true,
                    'qrcode' => $data['pix']['qrCode'] ?? null,
                    'qrcode_text' => $data['pix']['qrCodeText'] ?? null,
                    'qrcode_base64' => $data['pix']['qrCodeImage'] ?? null,
                    'expires_date' => $data['expiresAt'] ?? null,
                    'idTransaction' => $data['id'] ?? $deposit->payment_id,
                    'original_amount' => $amount,
                    'fee_amount' => 0,
                    'final_amount' => $amount
                ]);
            } else {
                \Log::error('AureoLink API error', [
                    'response' => $response->json(),
                    'status' => $response->status(),
                    'user_id' => $user->id,
                    'amount' => $amount
                ]);

                // Deletar depósito em caso de erro
                $deposit->delete();

                return response()->json([
                    'error' => true,
                    'message' => 'Erro ao processar pagamento via AureoLink'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('AureoLink request error: ' . $e->getMessage());
            
            if (isset($deposit)) {
                $deposit->delete();
            }

            return response()->json([
                'error' => true,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Processar depósito via AureoLink
     */
    public function processAureoLinkDeposit($transaction)
    {
        try {
            $gateway = Gateway::first();
            
            if (!$gateway || !$gateway->aureolink_enabled) {
                Log::error('AureoLink gateway not configured or disabled');
                return false;
            }

            $user = User::find($transaction->user_id);
            if (!$user) {
                Log::error('User not found for AureoLink deposit: ' . $transaction->user_id);
                return false;
            }

            // Preparar dados para a API AureoLink
            $payload = [
                'amount' => (int)($transaction->amount * 100), // Converter para centavos
                'paymentMethod' => 'pix',
                'customer' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'document' => [
                        'type' => 'cpf',
                        'number' => $user->cpf ?? '00000000000',
                    ]
                ],
                'items' => [
                    [
                        'title' => 'Depósito na plataforma',
                        'unitPrice' => (int)($transaction->amount * 100),
                        'quantity' => 1,
                        'tangible' => false
                    ]
                ]
            ];

            // Fazer requisição para a API AureoLink
            $response = Http::withBasicAuth(
                $gateway->aureolink_client_id,
                $gateway->aureolink_client_secret
            )->post($this->aureolinkBaseUrl . '/transactions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Atualizar transação com dados da AureoLink
                $transaction->update([
                    'external_id' => $data['id'],
                    'gateway_data' => json_encode($data),
                    'status' => 'pending'
                ]);

                Log::info("AureoLink deposit created successfully", [
                    'transaction_id' => $transaction->id,
                    'aureolink_id' => $data['id'],
                    'user_id' => $user->id
                ]);

                return [
                    'success' => true,
                    'pix_data' => $data['pix'] ?? null,
                    'qr_code' => $data['pix']['qrCode'] ?? null,
                    'qr_code_text' => $data['pix']['qrCodeText'] ?? null,
                ];
            } else {
                Log::error('AureoLink API error', [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('AureoLink deposit error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Processar saque via AureoLink
     */
    public function processAureoLinkWithdrawal($transaction)
    {
        try {
            $gateway = Gateway::first();
            
            if (!$gateway || !$gateway->aureolink_enabled) {
                Log::error('AureoLink gateway not configured or disabled');
                return false;
            }

            $user = User::find($transaction->user_id);
            if (!$user) {
                Log::error('User not found for AureoLink withdrawal: ' . $transaction->user_id);
                return false;
            }

            // Verificar saldo
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $transaction->amount) {
                Log::error('Insufficient balance for AureoLink withdrawal', [
                    'user_id' => $user->id,
                    'requested_amount' => $transaction->amount,
                    'available_balance' => $wallet ? $wallet->balance : 0
                ]);
                return false;
            }

            // Preparar dados para a API AureoLink
            $payload = [
                'method' => 'pix',
                'amount' => (int)($transaction->amount * 100), // Converter para centavos
                'pixKey' => $transaction->pix_key ?? $user->cpf ?? '00000000000',
                'pixKeyType' => 'cpf'
            ];

            // Fazer requisição para a API AureoLink
            $response = Http::withBasicAuth(
                $gateway->aureolink_client_id,
                $gateway->aureolink_client_secret
            )->withHeaders([
                'x-withdraw-key' => $gateway->aureolink_withdraw_key ?? ''
            ])->post($this->aureolinkBaseUrl . '/transfers', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Atualizar transação com dados da AureoLink
                $transaction->update([
                    'external_id' => $data['id'],
                    'gateway_data' => json_encode($data),
                    'status' => 'pending'
                ]);

                // Debitar saldo do usuário
                $wallet->decrement('balance', $transaction->amount);

                Log::info("AureoLink withdrawal created successfully", [
                    'transaction_id' => $transaction->id,
                    'aureolink_id' => $data['id'],
                    'user_id' => $user->id,
                    'amount' => $transaction->amount
                ]);

                return [
                    'success' => true,
                    'transfer_data' => $data
                ];
            } else {
                Log::error('AureoLink API error for withdrawal', [
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('AureoLink withdrawal error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar status de transação na AureoLink
     */
    public function checkAureoLinkTransactionStatus($externalId)
    {
        try {
            $gateway = Gateway::first();
            
            if (!$gateway || !$gateway->aureolink_enabled) {
                return false;
            }

            $response = Http::withBasicAuth(
                $gateway->aureolink_client_id,
                $gateway->aureolink_client_secret
            )->get($this->aureolinkBaseUrl . '/transactions/' . $externalId);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Failed to check AureoLink transaction status', [
                    'external_id' => $externalId,
                    'response' => $response->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('AureoLink check status error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Processar webhook da AureoLink
     */
    public function processAureoLinkWebhook($data)
    {
        try {
            Log::info('Processing AureoLink webhook', $data);

            if (isset($data['transaction'])) {
                $transaction = $data['transaction'];
                
                // Buscar depósito no nosso sistema
                $deposit = Deposit::where('external_id', $transaction['id'])->first();
                
                if ($deposit) {
                    $this->updateAureoLinkDepositStatus($deposit, $transaction);
                }

                // Buscar saque no nosso sistema
                $withdrawal = Withdrawal::where('external_id', $transaction['id'])->first();
                
                if ($withdrawal) {
                    $this->updateAureoLinkWithdrawalStatus($withdrawal, $transaction);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('AureoLink webhook processing error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualizar status de depósito
     */
    protected function updateAureoLinkDepositStatus($deposit, $transaction)
    {
        $status = $transaction['status'] ?? 'pending';
        
        switch ($status) {
            case 'approved':
                if ($deposit->status !== 'approved') {
                    $deposit->update(['status' => 'approved']);
                    $this->creditUserWallet($deposit);
                    
                    // Notificar admin
                    $this->notifyAdmin('Depósito AureoLink Aprovado', 
                        "Depósito de R$ {$deposit->amount} aprovado para usuário ID: {$deposit->user_id}");
                }
                break;
                
            case 'cancelled':
                $deposit->update(['status' => 'cancelled']);
                break;
                
            case 'pending':
                $deposit->update(['status' => 'pending']);
                break;
        }
    }

    /**
     * Atualizar status de saque
     */
    protected function updateAureoLinkWithdrawalStatus($withdrawal, $transaction)
    {
        $status = $transaction['status'] ?? 'pending';
        
        switch ($status) {
            case 'approved':
                $withdrawal->update(['status' => 'approved']);
                
                // Notificar admin
                $this->notifyAdmin('Saque AureoLink Aprovado', 
                    "Saque de R$ {$withdrawal->amount} aprovado para usuário ID: {$withdrawal->user_id}");
                break;
                
            case 'cancelled':
                $withdrawal->update(['status' => 'cancelled']);
                
                // Restaurar saldo se cancelado
                $this->restoreUserBalance($withdrawal);
                break;
                
            case 'pending':
                $withdrawal->update(['status' => 'pending']);
                break;
        }
    }

    /**
     * Creditar saldo do usuário
     */
    protected function creditUserWallet($deposit)
    {
        $user = User::find($deposit->user_id);
        if ($user) {
            $wallet = Wallet::where('user_id', $user->id)->first();
            
            if ($wallet) {
                $wallet->increment('balance', $deposit->amount);
                
                Log::info("User {$user->id} credited with R$ {$deposit->amount} via AureoLink");
            }
        }
    }

    /**
     * Restaurar saldo do usuário (quando saque é cancelado)
     */
    protected function restoreUserBalance($withdrawal)
    {
        $user = User::find($withdrawal->user_id);
        if ($user) {
            $wallet = Wallet::where('user_id', $user->id)->first();
            
            if ($wallet) {
                $wallet->increment('balance', $withdrawal->amount);
                
                Log::info("User {$user->id} balance restored with R$ {$withdrawal->amount} (AureoLink withdrawal cancelled)");
            }
        }
    }

    /**
     * Notificar admin
     */
    protected function notifyAdmin($title, $message)
    {
        try {
            $admins = User::where('role_id', 0)->get();
            
            foreach ($admins as $admin) {
                Notification::make()
                    ->title($title)
                    ->body($message)
                    ->success()
                    ->sendToDatabase($admin);
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admin: ' . $e->getMessage());
        }
    }
} 