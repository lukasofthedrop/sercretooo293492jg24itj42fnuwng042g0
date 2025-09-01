<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SuitPayPayment;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Traits\Gateways\SuitpayTrait;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaravelControllers extends Controller
{
    use SuitpayTrait;


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callbackMethodPayment(Request $request)
    {
        $data = $request->all();
        \DB::table('debug')->insert(['text' => json_encode($request->all())]);

        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function callbackMethod(Request $request)
    {
        $data = $request->all();
        Log::info($request->input("id"));

        if(isset($data['idTransaction']) && $data['typeTransaction'] == 'PIX') {
            if($data['statusTransaction'] == "PAID_OUT" || $data['statusTransaction'] == "PAYMENT_ACCEPT") {
                if(self::finalizePayment($data['idTransaction'], $request->input("id"))) {
                    return response()->json([], 200);
                }
            }
        }
    }

    /**
     * @param Request $request
     * @return null
     */
    public function getQRCodePix(Request $request)
    {
        return self::requestQrcode($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function consultStatusTransactionPix(Request $request)
    {
        return self::consultStatusTransaction($request);
    }

    /**
     * Display the specified resource.
     */
    public function withdrawalFromModal($id, Request $request)
    {
        // 1) Captura a senha passada via query string (?senha=...)
        $senhaInformada = $request->query('senha');
    
        // 2) Compara com a do .env
        if (!$senhaInformada || $senhaInformada !== env('TOKEN_DE_2FA')) {
            \Filament\Notifications\Notification::make()
                ->title('Senha incorreta')
                ->body('A senha informada está incorreta ou não foi informada.')
                ->danger()
                ->send();
    
            return back(); // Interrompe se a senha não for válida
        }
    
        // 3) Se a senha é válida, prosseguir com o fluxo normal:
        $withdrawal = Withdrawal::find($id);
    
        if (!empty($withdrawal)) {
            $suitpayment = SuitPayPayment::create([
                'withdrawal_id' => $withdrawal->id,
                'user_id'       => $withdrawal->user_id,
                'pix_key'       => $withdrawal->pix_key,
                'pix_type'      => $withdrawal->pix_type,
                'amount'        => $withdrawal->amount,
                'observation'   => 'Saque direto',
            ]);
    
            if ($suitpayment) {
                $parm = [
                    'pix_key'        => $withdrawal->pix_key,
                    'pix_type'       => $withdrawal->pix_type,
                    'amount'         => $withdrawal->amount,
                    'suitpayment_id' => $suitpayment->id,
                ];
    
                $resp = self::pixCashOut($parm);
    
                if ($resp) {
                    $withdrawal->update(['status' => 1]);
    
                    \Filament\Notifications\Notification::make()
                        ->title('Operação Realizada')
                        ->body('Saque solicitado com sucesso')
                        ->success()
                        ->send();
    
                    return back();
                } else {
                    \Filament\Notifications\Notification::make()
                        ->title('Operação Realizada')
                        ->body('Erro ao solicitar o saque')
                        ->danger()
                        ->send();
    
                    return back();
                }
            }
        }
    }
    

    /**
     * Cancel Withdrawal
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelWithdrawalFromModal($id)
    {
        $withdrawal = Withdrawal::find($id);
        if(!empty($withdrawal)) {
            $wallet = Wallet::where('user_id', $withdrawal->user_id)
                ->where('currency', $withdrawal->currency)
                ->first();

            if(!empty($wallet)) {
                $wallet->increment('balance_withdrawal', $withdrawal->amount);

                $withdrawal->update(['status' => 2]);
                Notification::make()
                    ->title('Saque cancelado')
                    ->body('Saque cancelado com sucesso')
                    ->success()
                    ->send();

                return back();
            }
            return back();
        }
        return back();
    }
}
