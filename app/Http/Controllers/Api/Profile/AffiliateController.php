<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\AffiliateHistory;
use App\Models\AffiliateWithdraw;
use App\Models\User;
use App\Models\Wallet;
use App\Models\AffiliateSettings;
use App\Services\AffiliateMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $indications    = User::where('inviter', auth('api')->id())->count();
        $walletDefault  = Wallet::where('user_id', auth('api')->id())->first();

        return response()->json([
            'status'        => true,
            'code'          => auth('api')->user()->inviter_code,
            'url'           => config('app.url') . '/register?code='.auth('api')->user()->inviter_code,
            'indications'   => $indications,
            'wallet'        => $walletDefault
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function generateCode()
    {
        $code = $this->gencode();
        $setting = \Helper::getSetting();

        if(!empty($code)) {
            $user = auth('api')->user();
            \DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => 2,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ],
            );

            if(auth('api')->user()->update(['inviter_code' => $code, 'affiliate_revenue_share' => $setting->revshare_percentage])) {
                return response()->json(['status' => true, 'message' => trans('Successfully generated code')]);
            }

            return response()->json(['error' => ''], 400);
        }

        return response()->json(['error' => ''], 400);
    }

    /**
     * @return null
     */
    private function gencode() {
        $code = \Helper::generateCode(10);

        $checkCode = User::where('inviter_code', $code)->first();
        if(empty($checkCode)) {
            return $code;
        }

        return $this->gencode();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function makeRequest(Request $request)
    {
        $rules = [
            'amount'   => 'required',
            'pix_type' => 'required',
        ];

        switch ($request->pix_type) {
            case 'document':
                $rules['pix_key'] = 'required|cpf_ou_cnpj';
                break;
            case 'email':
                $rules['pix_key'] = 'required|email';
                break;
            case 'phoneNumber':
                $rules['pix_key'] = 'required|telefone';
                break;
            default:
                $rules['pix_key'] = 'required';
                break;

        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $comission = auth('api')->user()->wallet->refer_rewards;

        if(floatval($comission) >= floatval($request->amount)) {
            AffiliateWithdraw::create([
                'user_id' => auth('api')->id(),
                'amount' => $request->amount,
                'pix_key' => $request->pix_key,
                'pix_type' => $request->pix_type,
                'currency' => 'BRL',
                'symbol' => 'R$',
            ]);

            auth('api')->user()->wallet->decrement('refer_rewards', $request->amount);
            return response()->json(['message' => trans('Commission withdrawal successfully carried out')], 200);
        }

        return response()->json(['status' => false, 'error' => 'Você não tem saldo suficiente']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Get affiliate metrics respecting visibility permissions
     */
    public function getMetrics()
    {
        $userId = auth('api')->id();
        $settings = AffiliateSettings::getOrCreateForUser($userId);
        $metrics = AffiliateMetricsService::getAffiliateMetrics($userId);
        
        // Filtra métricas baseado nas permissões
        $response = [
            'tier' => $metrics['tier'],
            'is_active' => $metrics['is_active'],
            'total_referred' => $metrics['total_referred'],
            'active_referred' => $metrics['active_referred'],
            'conversion_rate' => $metrics['conversion_rate'],
            'calculation_period' => $metrics['calculation_period']
        ];
        
        // Adiciona métricas condicionalmente baseado nas permissões
        if ($settings->can_see_ngr) {
            $response['ngr'] = $metrics['ngr'];
        }
        
        if ($settings->can_see_deposits) {
            $response['total_deposits'] = $metrics['ngr']['total_deposits'] ?? 0;
        }
        
        if ($settings->can_see_losses) {
            $response['total_losses'] = $metrics['ngr']['total_withdrawals'] ?? 0;
        }
        
        if ($settings->can_see_reports) {
            $response['detailed_metrics'] = $metrics;
        }
        
        // Sempre mostra comissões (são do próprio afiliado)
        $response['total_commissions'] = $metrics['total_commissions'];
        $response['pending_commissions'] = $metrics['pending_commissions'];
        $response['revshare_percentage'] = $metrics['revshare_percentage'];
        $response['cpa_value'] = $metrics['cpa_value'];
        
        return response()->json([
            'status' => true,
            'metrics' => $response,
            'permissions' => [
                'can_see_ngr' => $settings->can_see_ngr,
                'can_see_deposits' => $settings->can_see_deposits,
                'can_see_losses' => $settings->can_see_losses,
                'can_see_reports' => $settings->can_see_reports
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
