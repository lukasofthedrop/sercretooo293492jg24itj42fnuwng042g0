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
use App\Models\Deposit;
use App\Models\Withdrawal;
use Carbon\Carbon;

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

            // IMPORTANTE: Campo affiliate_revenue_share não é mais usado - removido por segurança
            if(auth('api')->user()->update(['inviter_code' => $code])) {
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
        // IMPORTANTE: Mostra o revshare_display (fake) ao invés do real
        $response['revshare_percentage'] = $metrics['revshare_display'];
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

    /**
     * Dashboard do Afiliado - Mostra o RevShare FAKE
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Se não tem código de afiliado, gera um
        if (!$user->inviter_code) {
            $code = $this->gencode();
            if (!empty($code)) {
                $user->inviter_code = $code;
                $user->save();
                
                // Adiciona role de afiliado
                \DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id' => 2,
                        'model_type' => 'App\Models\User',
                        'model_id' => $user->id,
                    ],
                );
            }
        }
        
        return view('affiliate.dashboard');
    }
    
    /**
     * Painel do Afiliado - Versão Web com Dashboard Completa
     */
    public function painelAfiliado()
    {
        $user = auth()->user();
        $settings = AffiliateSettings::getOrCreateForUser($user->id);
        
        // Se não tem código de afiliado, gera um
        if (!$user->inviter_code) {
            $code = $this->gencode();
            if (!empty($code)) {
                $user->inviter_code = $code;
                $user->save();
                
                // Adiciona role de afiliado
                \DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id' => 2,
                        'model_type' => 'App\Models\User',
                        'model_id' => $user->id,
                    ],
                );
            }
        }
        
        // Busca indicados
        $referred = User::where('inviter', $user->id)->get();
        $referredCount = $referred->count();
        
        // Calcula indicados ativos (fizeram depósito nos últimos 30 dias)
        $activeReferred = $referred->filter(function($ref) {
            return Deposit::where('user_id', $ref->id)
                ->where('status', 1)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->exists();
        })->count();
        
        // Calcula NGR do mês atual
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        
        $referredIds = $referred->pluck('id');
        
        $monthDeposits = Deposit::whereIn('user_id', $referredIds)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 1)
            ->sum('amount');
            
        $monthWithdrawals = Withdrawal::whereIn('user_id', $referredIds)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 1)
            ->sum('amount');
            
        $monthNGR = $monthDeposits - $monthWithdrawals;
        
        // Calcula total ganho (baseado no FAKE 40%)
        $totalEarned = $user->wallet->refer_rewards ?? 0;
        
        // Dados dos últimos 6 meses para gráfico
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            
            $deposits = Deposit::whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $withdrawals = Withdrawal::whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $ngr = $deposits - $withdrawals;
            
            $monthlyData[] = [
                'month' => $date->format('M/Y'),
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'ngr' => $ngr,
                // Mostra comissão FAKE de 40%
                'commission' => $ngr * 0.40
            ];
        }
        
        // Lista de indicados recentes
        $recentReferred = $referred->take(10)->map(function($ref) {
            $totalDeposited = Deposit::where('user_id', $ref->id)
                ->where('status', 1)
                ->sum('amount');
                
            $isActive = Deposit::where('user_id', $ref->id)
                ->where('status', 1)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->exists();
                
            return [
                'name' => $ref->name,
                'email' => $ref->email,
                'created_at' => $ref->created_at->format('d/m/Y'),
                'is_active' => $isActive,
                'total_deposited' => $totalDeposited,
                // Mostra comissão FAKE de 40%
                'commission_generated' => $totalDeposited * 0.40
            ];
        });
        
        return view('affiliate.painel-dashboard', [
            'user' => $user,
            'affiliate_code' => $user->inviter_code,
            'invite_link' => url('/register?code=' . $user->inviter_code),
            'total_referred' => $referredCount,
            'active_referred' => $activeReferred,
            'available_balance' => $user->wallet->refer_rewards ?? 0,
            'total_earned' => $totalEarned,
            'month_ngr' => $monthNGR,
            // SEMPRE mostra 40% como RevShare (FAKE)
            'revshare_percentage' => 40,
            'monthly_data' => $monthlyData,
            'recent_referred' => $recentReferred,
            'settings' => $settings
        ]);
    }
}
