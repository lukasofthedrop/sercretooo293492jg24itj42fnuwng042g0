<?php

namespace App\Traits\Gateways;

use App\Helpers\Core;
use App\Models\AffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Notifications\NewDepositNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Core as Helper;

trait BsPayTrait
{
    protected static string $uriBsPay;
    protected static string $clienteIdBsPay;
    protected static string $clienteSecretBsPay;

    private static function generateCredentialsBsPay()
    {
        $setting = Gateway::first();
        if (!empty($setting)) {
            self::$uriBsPay = $setting->getAttributes()['bspay_uri'];
            self::$clienteIdBsPay = $setting->getAttributes()['bspay_cliente_id'];
            self::$clienteSecretBsPay = $setting->getAttributes()['bspay_cliente_secret'];
        }
    }
    private static function getTokenBsPay()
    {
        $string = self::$clienteIdBsPay . ":" . self::$clienteSecretBsPay;
        $basic = base64_encode($string);
        $response = Http::asMultipart()
            ->withHeaders([
                'Authorization' => 'Basic ' . $basic,
            ])
            ->post(self::$uriBsPay . 'oauth/token', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if (isset($responseData['access_token'])) {
                return ['error' => '', 'acessToken' => $responseData['access_token']];
            } else {
                return ['error' => 'Internal Server Error', 'acessToken' => ""];
            }
        } else {
            return ['error' => $response->status() . $response->body(), 'acessToken' => ""];
        }
    }
    public function requestQrcodeBsPay($request)
    {
        try {
            $setting = Core::getSetting();
            $rules = [
                'amount' => ['required', 'numeric', 'min:' . $setting->min_deposit, 'max:' . $setting->max_deposit],
                'cpf'    => ['required', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            self::generateCredentialsBsPay();
            $token = self::getTokenBsPay();
            if ($token['error'] != "") {
                return response()->json(['error' => "Peça para o banco liberar o Pix de cash in e cash out"], 500);
            }
            $idUnico = uniqid();

            // Lista de documentos CPF
            $response = Http::withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ]
            ])->withHeaders([
                "Authorization" => "Bearer " . $token['acessToken']
            ])->post(self::$uriBsPay . 'pix/qrcode', [
                "payerQuestion " => "Depósito via PIX",
                "external_id" => $idUnico,
                "postbackUrl" => url('/bspay/callback', [], true),
                "payer" => [
                    'document' => \Helper::soNumero($request->cpf),
                    'name' => auth('api')->user()->name,
                    'email' => auth('api')->user()->email
                ],
                "amount" => (float) $request->input("amount")

            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                self::generateTransactionBsPay($responseData['transactionId'], $request->input("amount"), $idUnico);
                self::generateDepositBsPay($responseData['transactionId'], $request->input("amount"));
                return response()->json(['status' => true, 'idTransaction' => $responseData['transactionId'], 'qrcode' => $responseData['qrcode']]);
            }
            return response()->json(['error' => "Ocorreu uma falha ao entrar em contato com o banco."], 500);
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }
    private static function pixCashOutBsPay($id, $tipo)
    {
        $withdrawal = Withdrawal::find($id);
        if ($tipo == "afiliado") {
            $withdrawal = AffiliateWithdraw::find($id);
        }
        self::generateCredentialsBsPay();

        $token = self::getTokenBsPay();
        if ($token['error'] != "") {
            return false;
        }
        if ($withdrawal != null) {
            $idUnico = uniqid();
            $tipo = null;
            $key = null;
            switch ($withdrawal->pix_type) {
                
                case 'document':
                    // Remove pontuações (qualquer caractere que não seja número)
                    $pixKeySemPontuacao = preg_replace('/[^0-9]/', '', $withdrawal->pix_key);
                    if (strlen($withdrawal->pix_key) == 11) {
                        $tipo = "CPF";
                    } else {
                        $tipo = "CNPJ";
                    }
                    $key = $pixKeySemPontuacao;
                    break;
                case 'phoneNumber':
                    $key = "+55" .  $withdrawal->pix_key;
                    $tipo = "TELEFONE";
                    break;
                case 'email':
                    $key = $withdrawal->pix_key;
                    $tipo = "EMAIL";
                    break;
                case 'randomKey':
                    $key = $withdrawal->pix_key;
                    $tipo = "CHAVE_ALEATORIA";
                    break;
            }
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $token['acessToken']
            ])->post(self::$uriBsPay . 'pix/payment', [
                "amount" => $withdrawal->amount,
                "external_id" => $idUnico,
                "description" => "Solicitação de saque",
                "creditParty" => [
                    'taxId' => $withdrawal->cpf,
                    'name' => $withdrawal->name,
                    'keyType' => $tipo,
                    "key" => $key
                ],
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $withdrawal->update(['status' => 1]);
                return true;
            }

            return false;
        } else {
            return false;
        }
    }
    private static function finalizePaymentBsPay(Request $request)
    {
        $requestBody = $request->input("requestBody");
        $idTransaction = $requestBody['transactionId'];
        $transaction = Transaction::where('payment_id', $idTransaction)->where('status', 0)->first();
        $setting = Core::getSetting();

        if (!empty($transaction)) {
            $user = User::find($transaction->user_id);

            $wallet = Wallet::where('user_id', $transaction->user_id)->first();
            if (!empty($wallet)) {
                $setting = Setting::first();

                /// verifica se é o primeiro deposito, verifica as transações, somente se for transações concluidas
                $checkTransactions = Transaction::where('user_id', $transaction->user_id)
                    ->where('status', 1)
                    ->count();

                if ($checkTransactions == 0 || empty($checkTransactions)) {
                    /// pagar o bonus
                    $bonus = Core::porcentagem_xn($setting->initial_bonus, $transaction->price);
                    $wallet->increment('balance_bonus', $bonus);
                    $wallet->update(['balance_bonus_rollover' => $bonus * $setting->rollover]);
                }

                /// rollover deposito
                $wallet->update(['balance_deposit_rollover' => $transaction->price * intval($setting->rollover_deposit)]);



                if ($wallet->increment('balance', $transaction->price)) {
                    if ($transaction->update(['status' => 1])) {
                        $deposit = Deposit::where('payment_id', $idTransaction)->where('status', 0)->first();
                        if (!empty($deposit)) {

                            /// fazer o deposito em cpa
                            $affHistoryCPA = AffiliateHistory::where('user_id', $user->id)
                                ->where('commission_type', 'cpa')
                                //->where('deposited', 1)
                                ->where('status', 0)
                                ->first();

                            if (!empty($affHistoryCPA)) {

                                // Verifica se o CPA pode ser pago com base no baseline do sponsor
                                $sponsorCpa = User::find($user->inviter);
                                if (!empty($sponsorCpa)) {
                                    // Defina o valor do depósito para ser atualizado
                                    $deposited_amount = $transaction->price;

                                    // Verifica se o valor acumulado ou o depósito atual atinge o baseline
                                    if ($affHistoryCPA->deposited_amount >= $sponsorCpa->affiliate_baseline || $deposit->amount >= $sponsorCpa->affiliate_baseline) {
                                        $walletCpa = Wallet::where('user_id', $affHistoryCPA->inviter)->first();
                                        if (!empty($walletCpa)) {

                                            // Paga o valor de CPA
                                            $walletCpa->increment('refer_rewards', $sponsorCpa->affiliate_cpa); // Adiciona a comissão
                                            $affHistoryCPA->update([
                                                'status' => 1,
                                                'deposited' => $deposited_amount,
                                                'commission_paid' => $sponsorCpa->affiliate_cpa
                                            ]); // Atualiza e desativa CPA
                                      
                                        }
                                    } else {
                                        // Atualiza o valor depositado no histórico de afiliados
                                        $affHistoryCPA->update(['deposited_amount' => $transaction->price]);
                                    }
                                }
                            }
                            if ($deposit->update(['status' => 1])) {
                                $admins = User::where('role_id', 0)->get();
                                foreach ($admins as $admin) {
                                    $admin->notify(new NewDepositNotification($user->name, $transaction->price));
                                }

                                return true;
                            }
                            return true;
                        }
                        return true;
                    }
                }

                return true;
            }
            return false;
        }
        return false;
    }
    private static function webhookBsPay(Request $request)
    {
        $requestBody = $request->input("requestBody");
        $idTransaction = $requestBody['transactionId'];
        $transaction = Transaction::where('payment_id', $idTransaction)->where('status', 0)->first();
        if ($transaction != null && $transaction->idUnico == $requestBody['external_id']) {
            $payment = self::finalizePaymentBsPay($request);
            if ($payment == true) {
                return response()->json([], 200);
            } else {
                return response()->json([], 500);
            }
        }
        return response()->json([], 401);
    }

    private static function generateDepositBsPay($idTransaction, $amount)
    {
        $userId = auth('api')->user()->id;
        $wallet = Wallet::where('user_id', $userId)->first();

        Deposit::create([
            'payment_id' => $idTransaction,
            'user_id'   => $userId,
            'amount'    => $amount,
            'type'      => 'pix',
            'currency'  => $wallet->currency,
            'symbol'    => $wallet->symbol,
            'status'    => 0
        ]);
    }

    private static function generateTransactionBsPay($idTransaction, $amount, $id)
    {
        $setting = Core::getSetting();

        Transaction::create([
            'payment_id' => $idTransaction,
            'user_id' => auth('api')->user()->id,
            'payment_method' => 'pix',
            'price' => $amount,
            'currency' => $setting->currency_code,
            'status' => 0,
            "idUnico" => $id
        ]);
    }
}
