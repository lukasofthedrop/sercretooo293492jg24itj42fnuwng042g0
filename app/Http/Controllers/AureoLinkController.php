<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;

class AureoLinkController extends Controller
{
    protected $clientId;
    protected $clientSecret;
    protected $withdrawKey;

    public function __construct()
    {
        $config = DB::table('gateway_aureolink_config')->first();
        if ($config) {
            $this->clientId = $config->client_id;
            $this->clientSecret = $config->client_secret;
            $this->withdrawKey = $config->withdraw_key;
        }
    }

    /**
     * Webhook para receber notificações da AureoLink
     */
    public function webhook(Request $request)
    {
        // Log apenas dados não sensíveis
        Log::info('AureoLink Webhook received', [
            'id' => $request->input('id'),
            'status' => $request->input('status'),
            'timestamp' => now()
        ]);

        try {
            $data = $request->all();
            $transactionId = $data['id'] ?? null;
            $status = $data['status'] ?? null;
            $amount = $data['amount'] ?? 0;
            $customerEmail = $data['customer']['email'] ?? null;

            if (!$transactionId || !$status) {
                return response()->json(['error' => 'Dados inválidos'], 400);
            }

            // Buscar depósito pelo ID da transação
            $deposit = Deposit::where('transaction_id', $transactionId)
                            ->where('gateway', 'aureolink')
                            ->first();

            if (!$deposit) {
                Log::warning('Deposit not found for transaction', ['transaction_id' => $transactionId]);
                return response()->json(['error' => 'Depósito não encontrado'], 404);
            }

            // Atualizar status do depósito
            $oldStatus = $deposit->status;
            $deposit->status = $this->mapStatus($status);
            $deposit->updated_at = now();
            $deposit->save();

            // Se foi aprovado, creditar o usuário
            if ($status === 'approved' && $oldStatus !== 'approved') {
                $this->creditUser($deposit);
            }

            Log::info('AureoLink webhook processed successfully', [
                'transaction_id' => $transactionId,
                'status' => $status,
                'deposit_id' => $deposit->id
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('AureoLink webhook error', [
                'error' => $e->getMessage(),
                'transaction_id' => $request->input('id', 'unknown')
            ]);

            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Criar depósito via AureoLink
     */
    public function createDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'user_id' => 'required|exists:users,id',
            'description' => 'nullable|string'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $amount = $request->amount * 100; // Converter para centavos

            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->post('https://api.aureolink.com.br/v1/transactions', [
                    'amount' => $amount,
                    'paymentMethod' => 'pix',
                    'customer' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'document' => [
                            'type' => 'cpf',
                            'number' => $user->cpf ?? '00000000000'
                        ]
                    ],
                    'items' => [
                        [
                            'title' => $request->description ?? 'Depósito',
                            'unitPrice' => $amount,
                            'quantity' => 1,
                            'tangible' => false
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $transactionData = $response->json();

                // Criar depósito no sistema
                $deposit = Deposit::create([
                    'user_id' => $user->id,
                    'amount' => $request->amount,
                    'gateway' => 'aureolink',
                    'transaction_id' => $transactionData['id'],
                    'status' => 'pending',
                    'description' => $request->description ?? 'Depósito via AureoLink PIX'
                ]);

                return response()->json([
                    'success' => true,
                    'deposit_id' => $deposit->id,
                    'transaction_id' => $transactionData['id'],
                    'pix_data' => $transactionData['pix'] ?? null,
                    'qr_code' => $transactionData['pix']['qrCode'] ?? null
                ]);

            } else {
                Log::error('AureoLink deposit creation failed', [
                    'response' => $response->body(),
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao criar transação na AureoLink'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('AureoLink deposit error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Criar saque via AureoLink
     */
    public function createWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'user_id' => 'required|exists:users,id',
            'pix_key' => 'required|string',
            'pix_key_type' => 'required|in:cpf,cnpj,email,phone,random'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $amount = $request->amount * 100; // Converter para centavos

            // Verificar saldo
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet || $wallet->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'error' => 'Saldo insuficiente'
                ], 400);
            }

            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->withHeaders([
                    'x-withdraw-key' => $this->withdrawKey
                ])
                ->post('https://api.aureolink.com.br/v1/transfers', [
                    'method' => 'pix',
                    'amount' => $amount,
                    'pixKey' => $request->pix_key,
                    'pixKeyType' => $request->pix_key_type
                ]);

            if ($response->successful()) {
                $transactionData = $response->json();

                // Criar saque no sistema
                $withdrawal = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $request->amount,
                    'gateway' => 'aureolink',
                    'transaction_id' => $transactionData['id'],
                    'status' => 'pending',
                    'pix_key' => $request->pix_key,
                    'pix_key_type' => $request->pix_key_type
                ]);

                // Debitar saldo
                $wallet->decrement('balance', $request->amount);

                return response()->json([
                    'success' => true,
                    'withdrawal_id' => $withdrawal->id,
                    'transaction_id' => $transactionData['id']
                ]);

            } else {
                Log::error('AureoLink withdrawal creation failed', [
                    'response' => $response->body(),
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Erro ao criar saque na AureoLink'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('AureoLink withdrawal error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Verificar status de uma transação
     */
    public function checkTransactionStatus($transactionId)
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->get("https://api.aureolink.com.br/v1/transactions/{$transactionId}");

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Transação não encontrada'
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('AureoLink status check error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Mapear status da AureoLink para o sistema
     */
    protected function mapStatus($aureolinkStatus)
    {
        $statusMap = [
            'approved' => 'approved',
            'pending' => 'pending',
            'cancelled' => 'cancelled',
            'failed' => 'cancelled'
        ];

        return $statusMap[$aureolinkStatus] ?? 'pending';
    }

    /**
     * Creditar usuário após depósito aprovado
     */
    protected function creditUser($deposit)
    {
        try {
            $wallet = Wallet::where('user_id', $deposit->user_id)->first();
            
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $deposit->user_id,
                    'balance' => 0
                ]);
            }

            $wallet->increment('balance', $deposit->amount);

            Log::info('User credited successfully', [
                'user_id' => $deposit->user_id,
                'amount' => $deposit->amount,
                'deposit_id' => $deposit->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error crediting user', [
                'error' => $e->getMessage(),
                'deposit_id' => $deposit->id
            ]);
        }
    }
} 