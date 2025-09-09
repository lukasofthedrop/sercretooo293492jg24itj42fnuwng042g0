<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class AureoLinkController extends Controller
{
    protected $gateway;
    protected $baseUrl = 'https://api.aureolink.com.br/v1';

    public function __construct()
    {
        $this->gateway = Gateway::first();
    }

    /**
     * Webhook para receber notificações da AureoLink
     */
    public function webhook(Request $request)
    {
        try {
            // Log apenas dados não sensíveis
            Log::info('AureoLink Webhook received', [
                'transaction_id' => $request->input('transaction.id'),
                'status' => $request->input('transaction.status'),
                'timestamp' => now()
            ]);

            $data = $request->all();
            
            // Verificar se é uma notificação de transação
            if (isset($data['transaction'])) {
                $transaction = $data['transaction'];
                
                // Buscar a transação no nosso sistema
                $deposit = Deposit::where('external_id', $transaction['id'])->first();
                
                if ($deposit) {
                    $this->processDepositStatus($deposit, $transaction);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('AureoLink Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Processar mudança de status de depósito
     */
    protected function processDepositStatus($deposit, $transaction)
    {
        $status = $transaction['status'] ?? 'pending';
        
        switch ($status) {
            case 'approved':
                if ($deposit->status !== 'approved') {
                    $deposit->update(['status' => 'approved']);
                    $this->creditUserWallet($deposit);
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
     * Creditar saldo do usuário
     */
    protected function creditUserWallet($deposit)
    {
        $user = User::find($deposit->user_id);
        if ($user) {
            $wallet = Wallet::where('user_id', $user->id)->first();
            
            if ($wallet) {
                $wallet->increment('balance', $deposit->amount);
                
                // Log da transação
                Log::info("User {$user->id} credited with R$ {$deposit->amount} via AureoLink");
            }
        }
    }

    /**
     * Criar depósito PIX
     */
    public function createDeposit(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'user_id' => 'required|exists:users,id',
                'customer_name' => 'required|string',
                'customer_email' => 'required|email',
                'customer_cpf' => 'required|string',
            ]);

            if (!$this->gateway || !$this->gateway->aureolink_enabled) {
                return response()->json(['error' => 'AureoLink gateway not configured'], 400);
            }

            $user = User::find($request->user_id);
            
            // Criar depósito no nosso sistema
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'gateway' => 'aureolink',
                'external_id' => null, // Será preenchido após resposta da API
            ]);

            // Preparar dados para a API AureoLink
            $payload = [
                'amount' => (int)($request->amount * 100), // Converter para centavos
                'paymentMethod' => 'pix',
                'customer' => [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'document' => [
                        'type' => 'cpf',
                        'number' => $request->customer_cpf,
                    ]
                ],
                'items' => [
                    [
                        'title' => 'Depósito na plataforma',
                        'unitPrice' => (int)($request->amount * 100),
                        'quantity' => 1,
                        'tangible' => false
                    ]
                ]
            ];

            // Fazer requisição para a API AureoLink
            $response = Http::withBasicAuth(
                $this->gateway->aureolink_client_id,
                $this->gateway->aureolink_client_secret
            )->post($this->baseUrl . '/transactions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Atualizar depósito com ID externo
                $deposit->update([
                    'external_id' => $data['id'],
                    'gateway_data' => json_encode($data)
                ]);

                return response()->json([
                    'success' => true,
                    'deposit_id' => $deposit->id,
                    'pix_data' => $data['pix'] ?? null,
                    'qr_code' => $data['pix']['qrCode'] ?? null,
                    'qr_code_text' => $data['pix']['qrCodeText'] ?? null,
                ]);
            } else {
                $deposit->delete(); // Remover depósito se falhou
                return response()->json([
                    'error' => 'Failed to create transaction',
                    'details' => $response->json()
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('AureoLink Create Deposit Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Criar saque PIX
     */
    public function createWithdrawal(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'user_id' => 'required|exists:users,id',
                'pix_key' => 'required|string',
                'pix_key_type' => 'required|in:cpf,email,phone,random',
            ]);

            if (!$this->gateway || !$this->gateway->aureolink_enabled) {
                return response()->json(['error' => 'AureoLink gateway not configured'], 400);
            }

            $user = User::find($request->user_id);
            
            // Verificar saldo
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $request->amount) {
                return response()->json(['error' => 'Insufficient balance'], 400);
            }

            // Criar saque no nosso sistema
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'gateway' => 'aureolink',
                'external_id' => null,
            ]);

            // Preparar dados para a API AureoLink
            $payload = [
                'method' => 'pix',
                'amount' => (int)($request->amount * 100), // Converter para centavos
                'pixKey' => $request->pix_key,
                'pixKeyType' => $request->pix_key_type,
            ];

            // Fazer requisição para a API AureoLink
            $response = Http::withBasicAuth(
                $this->gateway->aureolink_client_id,
                $this->gateway->aureolink_client_secret
            )->withHeaders([
                'x-withdraw-key' => $this->gateway->aureolink_withdraw_key ?? ''
            ])->post($this->baseUrl . '/transfers', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Atualizar saque com ID externo
                $withdrawal->update([
                    'external_id' => $data['id'],
                    'gateway_data' => json_encode($data)
                ]);

                // Debitar saldo do usuário
                $wallet->decrement('balance', $request->amount);

                return response()->json([
                    'success' => true,
                    'withdrawal_id' => $withdrawal->id,
                    'transfer_data' => $data
                ]);
            } else {
                $withdrawal->delete(); // Remover saque se falhou
                return response()->json([
                    'error' => 'Failed to create transfer',
                    'details' => $response->json()
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('AureoLink Create Withdrawal Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verificar status de transação
     */
    public function checkTransactionStatus($externalId)
    {
        try {
            if (!$this->gateway || !$this->gateway->aureolink_enabled) {
                return response()->json(['error' => 'AureoLink gateway not configured'], 400);
            }

            $response = Http::withBasicAuth(
                $this->gateway->aureolink_client_id,
                $this->gateway->aureolink_client_secret
            )->get($this->baseUrl . '/transactions/' . $externalId);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Failed to get transaction status',
                    'details' => $response->json()
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('AureoLink Check Status Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar transações
     */
    public function listTransactions(Request $request)
    {
        try {
            if (!$this->gateway || !$this->gateway->aureolink_enabled) {
                return response()->json(['error' => 'AureoLink gateway not configured'], 400);
            }

            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);

            $response = Http::withBasicAuth(
                $this->gateway->aureolink_client_id,
                $this->gateway->aureolink_client_secret
            )->get($this->baseUrl . '/transactions', [
                'page' => $page,
                'limit' => $limit
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Failed to list transactions',
                    'details' => $response->json()
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('AureoLink List Transactions Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 