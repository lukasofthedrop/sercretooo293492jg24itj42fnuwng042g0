<?php

namespace App\Traits\Gateways;

use App\Helpers\Core;
use App\Models\AffiliateHistory;
use App\Models\AffiliateLogs;
use App\Models\AffiliateWithdraw;
use App\Models\Deposit;
use App\Models\DigitoPay;
use App\Models\Gateway;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Notifications\NewDepositNotification;
use Exception;
use App\Helpers\Core as Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


trait DigitoPayTrait
{
    protected static string $uriDigito;
    protected static string $clienteIdDigito;
    protected static string $clienteSecretDigito;

    private static function generateCredentialsDigito()
    {
        $setting = Gateway::first();
        if (!empty($setting)) {
            self::$uriDigito = $setting->getAttributes()['digito_uri'];

            self::$clienteIdDigito = $setting->getAttributes()['digito_client'];
            self::$clienteSecretDigito = $setting->getAttributes()['digito_secret'];
        }
    }
    private static function getToken()
    {
        try {
            $response = Http::post(self::$uriDigito . 'token/api', array_merge([
                "clientId" => self::$clienteIdDigito,
                "secret" => self::$clienteSecretDigito
            ]));
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['accessToken'])) {
                    return ['error' => '', 'acessToken' => $responseData['accessToken']];
                } else {
                    return ['error' => 'Internal Server Error', 'acessToken' => ""];
                }
            } else {
                return ['error' => 'Internal Server Error', 'acessToken' => ""];
            }
        } catch (Exception $e) {
            return ['error' => 'Internal Server Error', 'acessToken' => ""];
        }
    }
    public function requestQrcodeDigito($request)
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


            /// cpfgenerator ANTI-MEDPIX

            self::generateCredentialsDigito();
            $token = self::getToken();
            if ($token['error'] != "") {
                return response()->json(['error' => 'Ocorreu uma falha ao entrar em contato com o banco.'], 500);
            }


            $idUnico = uniqid();

            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $token['acessToken']
            ])->post(self::$uriDigito . 'deposit', array_merge([
                "dueDate" => date('Y-m-d\TH:i:s\Z', strtotime('+1 day')),
                "paymentOptions" => ["PIX"],
                "person" => [
                    'cpf' => \Helper::soNumero($request->cpf),
                    'name' => auth('api')->user()->name,
                ],
                "value" => (float) $request->input("amount"),
                "callbackUrl" =>  url('/digitopay/callback?id_unico=' . self::$clienteIdDigito, [], true)
            ]));
            if ($response->successful()) {
                $responseData = $response->json();
                self::generateTransactionDigito($responseData['id'], $request->input("amount"), $idUnico);
                self::generateDepositDigito($responseData['id'], $request->input("amount"));
                return response()->json(['status' => true, 'idTransaction' => $responseData['id'], 'qrcode' => $responseData['pixCopiaECola']]);
            }
            return response()->json(['error' => "Ocorreu uma falha ao entrar em contato com o bancoe."], 500);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    public function webhookDigito($request)
    {
        self::generateCredentialsDigito();
        if (self::finalizaPaymentDigito($request->input("id")) == true && self::$clienteIdDigito == $request->input("id_unico")) {
            return response()->json([], 200);
        } else {
            return response()->json([], 500);
        }
    }
    private static function finalizaPaymentDigito($idTransaction)
    {


        $transaction = Transaction::where('payment_id', $idTransaction)->where('status', 0)->first();
        $setting = Helper::getSetting();
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
                    $bonus = Helper::porcentagem_xn($setting->initial_bonus, $transaction->price);
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
                            return false;
                        }
                        return false;
                    }
                }

                return false;
            }
            return false;
        }
        return false;
    }
    public function pixCashOutDigito($id, $tipo)
    {
        $withdrawal = Withdrawal::find($id);
        self::generateCredentialsDigito();
        if ($tipo == "afiliado") {
            $withdrawal = AffiliateWithdraw::find($id);
        }
        $token = self::getToken();
        if ($token['error'] != "") {
            return false;
        }
        if ($withdrawal != null) {
            $tipo = null;
            $key = null;
            switch ($withdrawal->pix_type) {
                case 'document':
                    $tipo = strlen($withdrawal->pix_key) > 14 ? "CNPJ" : "CPF";
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
            ])->post(self::$uriDigito . 'withdraw', [
                "value" => $withdrawal->amount,
                "endToEndId" => null,
                "paymentOptions" => [
                    "PIX"
                ],
                "person" => [
                    'name' => $withdrawal->name,
                    'pixKeyTypes' => $tipo,
                    "pixKey" => $key
                ],
            ]);
       

            if ($response->successful()) {
                $responseData = $response->json();
               
                $withdrawal->update(['status' => 1]);
                DigitoPay::create([
                   "user_id" => $withdrawal->user_id,
                   "withdrawal_id" => $withdrawal->id,
                    "amount" => $withdrawal->amount,
                    "status" => 1
                ]);
                return true;
                
            }else{
            	if (isset($response->json()['mensagem']) && strpos($response->json()['mensagem'], 'necessita de analise manual, favor verificar!') !== false && $response->status() == 422) {
		        $withdrawal->update(['status' => 1]);
		        DigitoPay::create([
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
    private static function generateDepositDigito($idTransaction, $amount)
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
    private static function generateTransactionDigito($idTransaction, $amount, $id)
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