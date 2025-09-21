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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
            'url'           => url('/register?code=' . auth('api')->user()->inviter_code),
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
     * Painel do Afiliado - Versão Web com Dashboard Completa - Optimized for performance
     */
    public function painelAfiliado()
    {
        $user = auth()->user();
        
        // Cache whole dashboard for 15 minutes
        $cacheKey = 'affiliate_dashboard_' . $user->id;
        $dashboardData = Cache::remember($cacheKey, 15 * 60, function () use ($user) {
            $settings = AffiliateSettings::getOrCreateForUser($user->id);
            
            // Generate code if not exists (cached)
            if (!$user->inviter_code) {
                $code = $this->gencode();
                if ($code) {
                    $user->inviter_code = $code;
                    $user->save();
                    \DB::table('model_has_roles')->updateOrInsert([
                        'role_id' => 2,
                        'model_type' => 'App\Models\User',
                        'model_id' => $user->id,
                    ]);
                }
            }
            
            // Get referred IDs first
            $referredIds = DB::table('users')->where('inviter', $user->id)->pluck('id');
            if ($referredIds->isEmpty()) {
                $referredIds = collect([]); // Empty collection for queries
            }
            
            // Total referred (cached)
            $totalReferred = User::where('inviter', $user->id)->count();
            
            // Active referred (optimized with subquery)
            $activeReferred = User::where('inviter', $user->id)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                         ->from('deposits')
                         ->whereColumn('deposits.user_id', 'users.id')
                         ->where('deposits.status', 1)
                         ->where('deposits.created_at', '>=', Carbon::now()->subDays(30));
                })
                ->count();
            
            // Month NGR (optimized with single queries)
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();
            
            $monthDeposits = DB::table('deposits')
                ->whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 1)
                ->sum('amount');
                
            $monthWithdrawals = DB::table('withdrawals')
                ->whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 1)
                ->sum('amount');
                
            $monthNGR = $monthDeposits - $monthWithdrawals;
            
            // Monthly data for 6 months (optimized with single query)
            $monthlyData = DB::table('deposits')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(amount) as deposits')
                )
                ->whereIn('user_id', $referredIds)
                ->where('status', 1)
                ->whereBetween('created_at', [Carbon::now()->subMonths(5)->startOfMonth(), Carbon::now()])
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get()
                ->map(function ($item) use ($referredIds, $monthStart) {
                    $withdrawals = DB::table('withdrawals')
                        ->whereIn('user_id', $referredIds)
                        ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$item->month])
                        ->where('status', 1)
                        ->sum('amount');
                    $ngr = $item->deposits - $withdrawals;
                    return [
                        'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M/Y'),
                        'ngr' => $ngr,
                        'commission' => $ngr * 0.40 // FAKE 40%
                    ];
                })
                ->values();
            
            // Recent referred with optimized joins (limit 10)
            $recentReferred = DB::table('users')
                ->select(
                    'users.name',
                    'users.email',
                    'users.created_at',
                    DB::raw('SUM(deposits.amount) as total_deposited'),
                    DB::raw('IF(EXISTS (SELECT 1 FROM deposits WHERE deposits.user_id = users.id AND deposits.status = 1 AND deposits.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)), 1, 0) as has_recent_deposit')
                )
                ->leftJoin('deposits', function ($join) {
                    $join->on('deposits.user_id', '=', 'users.id')
                         ->where('deposits.status', 1);
                })
                ->where('users.inviter', $user->id)
                ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
                ->orderBy('users.created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    $user->created_at = Carbon::parse($user->created_at)->format('d/m/Y');
                    $user->is_active = (bool) $user->has_recent_deposit;
                    $user->commission_generated = $user->total_deposited * 0.40; // FAKE 40%
                    unset($user->has_recent_deposit);
                    return (array) $user;
                });
            
            return [
                'user' => $user,
                'affiliate_code' => $user->inviter_code,
                'invite_link' => url('/register?code=' . $user->inviter_code),
                'total_referred' => $totalReferred,
                'active_referred' => $activeReferred,
                'available_balance' => $user->wallet->refer_rewards ?? 0,
                'month_ngr' => $monthNGR,
                'revshare_percentage' => 40, // FAKE 40%
                'monthly_data' => $monthlyData,
                'recent_referred' => $recentReferred,
                'settings' => $settings
            ];
        });
        
        return view('affiliate.painel-dashboard', $dashboardData);
    }
}
