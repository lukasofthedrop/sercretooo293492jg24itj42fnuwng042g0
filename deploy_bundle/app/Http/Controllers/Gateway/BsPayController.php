<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Traits\Affiliates\AffiliateHistoryTrait;
use App\Traits\Gateways\BsPayTrait;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BsPayController extends Controller
{
    use BsPayTrait, AffiliateHistoryTrait;


    /**
     * @dev  
     * @param Request $request
     * @return null
     */
    public function getQRCodePix(Request $request)
    {
        return self::requestQrcodeBsPay($request);
    }

    /**
     * Store a newly created resource in storage.
     * @dev  
     */
   
     public function callbackMethodPayment(Request $request)
    {
        $data = $request->all();
        DB::table('debug')->insert(['text' => json_encode($request->all())]);

        return response()->json([], 200);
    }

    /*** @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function callbackMethod(Request $request)
    {
        $data = $request->all();

        self::webhookBsPay($request);
    }
}
