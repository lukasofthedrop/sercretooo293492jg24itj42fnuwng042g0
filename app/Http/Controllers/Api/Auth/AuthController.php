<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\SpinRuns;
use App\Models\Setting;
use App\Models\Wallet;
use App\Traits\Affiliates\AffiliateHistoryTrait;
use App\Http\Controllers\Controller;
use App\Models\AffiliateHistory;
use App\Models\User;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use AffiliateHistoryTrait;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.jwt', ['except' => ['login', 'register', 'submitForgetPassword', 'submitResetPassword']]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        try {
            $credentials = request(['email', 'password']);

            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => trans('Check credentials')], 400);
            }

            return $this->respondWithToken($token);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token'
            ], 400);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $setting = \Helper::getSetting();
            
            // Regras de validação
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
                'phone' => 'required|string|min:10|max:15',
                'password' => ['required', Rules\Password::min(12)->mixedCase()->numbers()->symbols()],
                'cupom' => 'nullable|string',  // Removido o exists para personalizar a verificação do cupom
                'reference_code' => 'nullable|string', 
            ];
    
            // Valida os dados do request
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
    
            // Verifica o cupom se ele estiver presente
            if (!empty($request->cupom)) {
                $cupom = \App\Models\Cupom::where('codigo', $request->cupom)->first();
                
                // Verifica se o cupom existe
                if (!$cupom) {
                    return response()->json(['error' => 'Esse cupom não existe'], 400);
                }
                
                // Verifica se o cupom está expirado
                if ($cupom->validade < now()) {
                    return response()->json(['error' => 'Esse cupom já está expirado'], 400);
                }
    
                // Verifica o limite de usos
                if ($cupom->usos >= $cupom->quantidade_uso) {
                    return response()->json(['error' => 'Vários usuários resgataram esse cupom hoje, tente novamente amanhã ou tente outro cupom'], 400);
                }
            }
    
            // Adiciona os valores de CPA e Baseline ao array de dados do usuário
            $userData = $request->only(['name', 'password', 'phone','email']);
            $userData['affiliate_cpa'] = $setting->cpa_value;
            $userData['affiliate_baseline'] = $setting->cpa_baseline;
            
            // Gera CPF único automático para o usuário (invisível ao usuário final)
            $userData['cpf'] = \Helper::generateUniqueCPF();
    
            // Cria o usuário
            if ($user = User::create($userData)) {
                // Verifica se existe um código de referência e salva o histórico de afiliados
                if (isset($request->reference_code) && !empty($request->reference_code)) {
                    $checkAffiliate = User::where('inviter_code', $request->reference_code)->first();
                    if (!empty($checkAffiliate)) {
                        $user->update(['inviter' => $checkAffiliate->id]);
                    }
                    $this->saveAffiliateHistory($user);
                }
    
                // Cria a carteira do usuário
                $this->createWallet($user);
    
                // Aplica o cupom de bônus, se houver
                if (!empty($request->cupom)) {
                    // Aplica o valor do bônus ao saldo do usuário
                    $bonus = $cupom->valor_bonus;
                    Wallet::where('user_id', $user->id)->increment('balance_bonus', $bonus);
    
                    // Aplica o rollover do cupom
                    $rolloverAmount = $bonus * $setting->rollover;
                    Wallet::where('user_id', $user->id)->increment('balance_bonus_rollover', $rolloverAmount);
    
                    // Incrementa os usos do cupom
                    $cupom->increment('usos');
                }
    
    
                // Autentica o usuário e retorna o token
                $credentials = $request->only(['email', 'password']);
                $token = auth('api')->attempt($credentials);
                if (!$token) {
                    return response()->json(['error' => 'Faça login ou cadastre-se para continuar'], 401);
                }
    
                // Retorna o token e os dados do usuário
                return $this->respondWithToken($token);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    
        // Return a default response if user creation fails
        return response()->json(['error' => 'User registration failed'], 400);
    }
    
    
    
    

    /**
     * @param $user
     * @return void
     */
    private function createWallet($user)
    {
        $setting = \Helper::getSetting();

        Wallet::create([
            'user_id'   => $user->id,
            'currency'  => $setting->currency_code,
            'symbol'    => $setting->prefix,
            'active'    => 1
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
    private function generateAffiliateCode()
    {
        do {
            $code = \Helper::generateCode(8); // Gerar código de 8 caracteres
            $checkCode = User::where('inviter_code', $code)->first();
        } while ($checkCode);

        return $code;
    }
    public function submitForgetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::table('users')->where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Email not found.'], 404);
        }

        $token = Str::random(60);
        $expiresAt = Carbon::now()->addMinutes(5);

        DB::transaction(function () use ($request, $token, $expiresAt) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
                'expires_at' => $expiresAt
            ]);

            Mail::send('emails.forget-password', ['token' => $token, 'resetLink' => url('/reset-password/' . $token)], function ($message) use ($request) {
                $message->to($request->email)->subject('Reset Password');
            });
        });

        return response()->json(['status' => true, 'message' => 'We have emailed your password reset link!'], 200);
    }

    public function submitResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => ['required', 'string', 'min:12', 'confirmed', 'regex:/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).*$/']
        ]);

        $tokenData = DB::table('password_reset_tokens')
                       ->where('token', $request->token)
                       ->where('expires_at', '>', Carbon::now())
                       ->first();

        if (!$tokenData || $tokenData->email !== $request->email) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['status' => true, 'message' => 'Your password has been changed!'], 200);
    }

    protected function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth('api')->user(),
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
