<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Traits\Gateways\EzzepayTrait;
use Illuminate\Http\Request;

class EzzePayController extends Controller
{
    use EzzepayTrait;
    public function webhook(Request $request){
        return self::webhookEzze($request);
    }

}