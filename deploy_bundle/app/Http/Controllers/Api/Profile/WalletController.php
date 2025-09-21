<?php
 
namespace App\Http\Controllers\Api\Profile;

use App\Helpers\Core;
use App\Http\Controllers\Controller;
use App\Models\AffiliateWithdraw;
use App\Models\Setting;
use App\Models\SuitPayPayment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\Withdrawal;
use App\Notifications\NewWithdrawalNotification;
use App\Traits\Gateways\DigitoPayTrait;
use App\Traits\Gateways\BsPayTrait;
use App\Traits\Gateways\EzzepayTrait;
use App\Traits\Gateways\SuitpayTrait;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    use DigitoPayTrait, EzzepayTrait, BsPayTrait, SuitpayTrait;

    public function index()
    {
        $wallet = Wallet::whereUserId(auth('api')->id())->where('active', 1)->first();
        return response()->json(['wallet' => $wallet], 200);
    }

    public function myWallet()
    {
        $wallets = Wallet::whereUserId(auth('api')->id())->get();
        return response()->json(['wallets' => $wallets], 200);
    }

    public function withdrawalFromModal($id, Request $request)
    {

        // 1) Captura a senha enviada pela query string
        $senhaInformada = $request->query('senha');

        // 2) Verifica se a senha coincide com a do .env
        if (!$senhaInformada || $senhaInformada !== env('TOKEN_DE_2FA')) {
            // Se for diferente ou inexistente, rejeita e mostra aviso
            Notification::make()
                ->title('Senha incorreta')
                ->body('A senha informada está incorreta ou não foi informada.')
                ->danger()
                ->send();

            return back();
        }

        // *** Se a senha está OK, prosseguimos com o saque ***


        $setting = Core::getSetting();
        $resultado = null;
        $tipo = $request->input("tipo");
        switch ($setting->saque) {
            case 'suitpay':
                $withdrawal = Withdrawal::find($id);
                if ($tipo == "afiliado") {
                    $withdrawal = AffiliateWithdraw::find($id);
                }
                $withdrawal->update(['status' => 1]);

                $suitpayment = SuitPayPayment::create([
                    'withdrawal_id' => $withdrawal->id,
                    'user_id'       => $withdrawal->user_id,
                    'pix_key'       => $withdrawal->pix_key,
                    'pix_type'      => $withdrawal->pix_type,
                    'amount'        => $withdrawal->amount,
                    'observation'   => 'Saque direto',
                ]);
                $parm = [
                    'pix_key'           => $withdrawal->pix_key,
                    'pix_type'          => $withdrawal->pix_type,
                    'amount'            => $withdrawal->amount,
                    'suitpayment_id'    => $suitpayment->id
                ];
                $resultado = self::pixCashOut($parm);
                break;
            case 'digitopay':
                $resultado = self::pixCashOutDigito($id, $tipo);
                break;
            case 'bspay':
                $resultado = self::pixCashOutBsPay($id, $tipo);
                break;
            case 'ezzepay':
                $resultado = self::pixCashOutEzze($id, $tipo);
                break;
        }

        if ($resultado == true) {
            Notification::make()
                ->title('Saque solicitado')
                ->body('Saque solicitado com sucesso')
                ->success()
                ->send();

            return back();
        } else {
            Notification::make()
                ->title('Erro no saque')
                ->body('Erro ao solicitar o saque')
                ->danger()
                ->send();

            return back();
        }
    }

    public function setWalletActive($id)
    {
        // Primeiro, desativa a carteira ativa do usuário autenticado
        $checkWallet = Wallet::where('user_id', auth('api')->id())
                             ->where('active', 1)
                             ->first();
        if ($checkWallet) {
            $checkWallet->update(['active' => 0]);
        }
    
        // Busca a carteira garantindo que ela pertença ao usuário autenticado
        $wallet = Wallet::where('id', $id)
                        ->where('user_id', auth('api')->id())
                        ->first();
    
        if (!$wallet) {
            return response()->json([
                'error' => 'Carteira não encontrada ou acesso não autorizado'
            ], 403);
        }
    
        $wallet->update(['active' => 1]);
        return response()->json(['wallet' => $wallet], 200);
    }
    

    public function requestWithdrawal(Request $request)
    {
        $setting = Setting::first();

        if (auth('api')->check()) {

            // Verificar a última aposta do usuário
            $lastOrder = Order::where('user_id', auth('api')->id())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastOrder) {
                $lastBetTime = $lastOrder->created_at;
                $currentTime = now();

                // Verificar se já passaram 5 minutos desde a última aposta
                if ($currentTime->diffInMinutes($lastBetTime) < 5) {
                    return response()->json([
                        'error' => 'Você só pode sacar após 5 minutos da última aposta.'
                    ], 400);
                }
            }
            
            if ($request->type === 'pix') {
                $rules = [
                    'amount'        => ['required', 'numeric', 'min:' . $setting->min_withdrawal, 'max:' . $setting->max_withdrawal],
                    'pix_type'      => 'required',
                ];

                switch ($request->pix_type) {
                    case 'document':
                        $rules['pix_key'] = 'required|cpf_ou_cnpj';
                        break;
                    case 'email':
                        $rules['pix_key'] = 'required|email';
                        break;
                    case 'phoneNumber':
                        $rules['pix_key'] = ['required', 'regex:/^\d{10,11}$/'];
                        break;
                    default:
                        $rules['pix_key'] = 'required';
                        break;
                }
            }

            if ($request->type === 'bank') {
                $rules = [
                    'amount'        => ['required', 'numeric', 'min:' . $setting->min_withdrawal, 'max:' . $setting->max_withdrawal],
                ];
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            /// verificar o limite de saque
            if (!empty($setting->withdrawal_limit)) {
                switch ($setting->withdrawal_period) {
                    case 'daily':
                        $registrosDiarios = Withdrawal::whereDate('created_at', now()->toDateString())->count();
                        if ($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the daily withdrawal limit')], 400);
                        }
                        break;
                    case 'weekly':
                        $registrosDiarios = Withdrawal::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                        if ($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the weekly withdrawal limit')], 400);
                        }
                        break;
                    case 'monthly':
                        $registrosDiarios = Withdrawal::whereYear('created_at', now()->year)->whereMonth('data', now()->month)->count();
                        if ($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the monthly withdrawal limit')], 400);
                        }
                        break;
                    case 'yearly':
                        $registrosDiarios = Withdrawal::whereYear('created_at', now()->year)->count();
                        if ($registrosDiarios >= $setting->withdrawal_limit) {
                            return response()->json(['error' => trans('You have already reached the yearly withdrawal limit')], 400);
                        }
                        break;
                }
            }

            if ($request->amount > $setting->max_withdrawal) {
                return response()->json(['error' => 'Você excedeu o limite máximo permitido de: ' . $setting->max_withdrawal], 400);
            }

            if (floatval($request->amount) > floatval(auth('api')->user()->wallet->balance_withdrawal)) {
                return response()->json(['error' => 'Você não tem saldo suficiente'], 400);
            }

            $data = [];
            if ($request->type === 'pix') {
                $data = [
                    'user_id'   => auth('api')->user()->id,
                    'amount'    => \Helper::amountPrepare($request->amount),
                    'type'      => $request->type,
                    'pix_key'   => $request->pix_key,
                    'pix_type'  => $request->pix_type,
                    'currency'  => $request->currency,
                    'symbol'    => $request->symbol,
                    'status'    => 0,
                    'cpf' => $request->cpf,
                ];
            }

            if ($request->type === 'bank') {
                $data = [
                    'user_id'   => auth('api')->user()->id,
                    'amount'    => \Helper::amountPrepare($request->amount),
                    'type'      => $request->type,
                    'currency'  => $request->currency,
                    'symbol'    => $request->symbol,
                    'status'    => 0,
                    'cpf' => $request->cpf,
                ];
            }

            $withdrawal = Withdrawal::create($data);

            if ($withdrawal) {
                $wallet = Wallet::where('user_id', auth('api')->id())->first();
                $wallet->decrement('balance_withdrawal', floatval($request->amount));

                $admins = User::where('role_id', 0)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new NewWithdrawalNotification(auth()->user()->name, $request->amount));
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Saque realizado com sucesso',
                ], 200);
            }

            return response()->json(['error' => 'Erro ao realizar o saque'], 400);
        }

        return response()->json(['error' => 'Erro ao realizar o saque'], 400);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
