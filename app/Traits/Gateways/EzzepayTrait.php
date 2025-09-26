<?php

namespace App\Traits\Gateways;

use App\Helpers\Core;
use App\Models\AffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\Deposit;
use App\Models\EzzePay;
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
trait EzzepayTrait
{
    protected static string $uriEzze;
    protected static string $clienteIdEzze;
    protected static string $clienteSecretEzze;
    protected static string $userEzze;
    protected static string $senhaEzze;

    private static function generateCredentialsEzze()
    {
        $setting = Gateway::first();
        if (!empty($setting)) {
            self::$uriEzze = $setting->getAttributes()['ezze_uri'];

            self::$clienteIdEzze = $setting->getAttributes()['ezze_client'];
            self::$clienteSecretEzze = $setting->getAttributes()['ezze_secret'];
            self::$userEzze = $setting->getAttributes()['ezze_user'];
            self::$senhaEzze = $setting->getAttributes()['ezze_senha'];
        }
    }
    private static function getTokenEzze()
    {
        $string = self::$clienteIdEzze . ":" . self::$clienteSecretEzze;
        $basic = base64_encode($string);
        $response = Http::asMultipart()
            ->withHeaders([
                'Authorization' => 'Basic ' . $basic,
            ])
            ->post(self::$uriEzze . 'oauth/token', [
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
    public function requestQrcodeEzze($request)
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
            self::generateCredentialsEzze();
            $token = self::getTokenEzze();
            if ($token['error'] != "") {
                return response()->json(['error' => "Ocorreu uma falha ao entrar em contato com o banco."], 500);
            }
            $idUnico = uniqid();


            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $token['acessToken']
            ])->post(self::$uriEzze . 'pix/qrcode', [
                "payerQuestion " => "Depósito via PIX ",
                "external_id" => $idUnico,
                "payer" => [
                    'document' => \Helper::soNumero($request->cpf),
                    'name' => auth('api')->user()->name,
                ],
                "amount" => (float) $request->input("amount")

            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                self::generateTransactionEzze($responseData['transactionId'], $request->input("amount"), $idUnico);
                self::generateDepositEzze($responseData['transactionId'], $request->input("amount"));
                return response()->json(['status' => true, 'idTransaction' => $responseData['transactionId'], 'qrcode' => $responseData['emvqrcps']]);
            }

            return response()->json(['error' => "Ocorreu uma falha ao entrar em contato com o banco."], 500);
        } catch (Exception $e) {
            Log::info($e);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }
    private static function pixCashOutEzze($id)
    {
        $withdrawal = Withdrawal::find($id);
        self::generateCredentialsEzze();

        $token = self::getTokenEzze();
        if ($token['error'] != "") {
            return false;
        }
        if ($withdrawal != null) {
            $idUnico = uniqid();
            $tipo = null;
            $key = null;
            switch ($withdrawal->pix_type) {
                case 'document':
                    if (strlen($withdrawal->pix_key) == 11) {
                        $tipo = "CPF";
                    } else {
                        $tipo = "CNPJ";
                    }
                    $key = $withdrawal->pix_key;
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
            ])->post(self::$uriEzze . 'pix/payment', [
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

                if (isset($responseData['transactionId'])) {
                    $withdrawal->update(['status' => 1]);

                    EzzePay::create([
                        "user_id" => $withdrawal->user_id,
                        "withdrawal_id" => $withdrawal->id,
                        "amount" => $withdrawal->amount,
                        "status" => 1
                    ]);
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }
    private static function finalizePaymentEzze(Request $request)
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
    private static function webhookEzze(Request $request)
    {
        if ($request->has('test') && $request->input('test') == true) {
            return response()->json([], 200);
        }
        self::generateCredentialsEzze();
        $auth = explode(" ", $request->header("Authorization"));
        $users = explode(":", base64_decode($auth[1]));
        if ($users[0] == self::$userEzze && $users[1] == self::$senhaEzze) {
            $payment = self::finalizePaymentEzze($request);
            if ($payment == true) {
                return response()->json([], 200);
            } else {
                return response()->json([], 500);
            }
        } else {
            return response()->json([], 401);
        }
    }

    private static function generateDepositEzze($idTransaction, $amount)
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

    private static function generateTransactionEzze($idTransaction, $amount, $id)
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
