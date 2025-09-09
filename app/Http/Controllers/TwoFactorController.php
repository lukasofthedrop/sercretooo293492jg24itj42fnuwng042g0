<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Mostrar página de configuração do 2FA
     */
    public function show()
    {
        $user = Auth::user();
        
        if ($user->two_factor_secret) {
            return view('auth.2fa-already-enabled');
        }

        return view('auth.2fa-setup', [
            'qrCode' => $this->generateQrCode($user),
            'secret' => $user->two_factor_secret
        ]);
    }

    /**
     * Ativar 2FA para o usuário
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6'
        ]);

        $user = Auth::user();
        
        // Gerar secret se não existir
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        // Verificar o código
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Código inválido. Por favor, tente novamente.']);
        }

        // Gerar códigos de recuperação
        $recoveryCodes = collect(range(1, 8))->map(function () {
            return Str::random(10) . '-' . Str::random(10);
        });

        $user->two_factor_recovery_codes = encrypt($recoveryCodes->toJson());
        $user->two_factor_confirmed_at = now();
        $user->save();

        return redirect()->route('dashboard')->with('success', '2FA ativado com sucesso! Guarde seus códigos de recuperação em local seguro.');
    }

    /**
     * Desativar 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('dashboard')->with('success', '2FA desativado com sucesso.');
    }

    /**
     * Mostrar página de verificação 2FA
     */
    public function showVerify()
    {
        return view('auth.2fa-verify');
    }

    /**
     * Verificar código 2FA
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6'
        ]);

        $user = Auth::user();
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            // Tentar código de recuperação
            if ($this->tryRecoveryCode($request->code)) {
                session(['2fa_verified' => true]);
                return redirect()->intended('dashboard');
            }

            return back()->withErrors(['code' => 'Código inválido.']);
        }

        session(['2fa_verified' => true]);
        return redirect()->intended('dashboard');
    }

    /**
     * Gerar QR Code
     */
    private function generateQrCode($user)
    {
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        $companyName = config('app.name');
        $companyEmail = $user->email;
        $secretKey = $user->two_factor_secret;

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secretKey
        );

        // Retornar QR Code usando serviço online temporariamente
        $qrServiceUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);
        return '<img src="' . $qrServiceUrl . '" alt="QR Code 2FA" class="mx-auto">';
    }

    /**
     * Tentar usar código de recuperação
     */
    private function tryRecoveryCode($code)
    {
        $user = Auth::user();
        
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
        
        if (in_array($code, $recoveryCodes)) {
            // Remover código usado
            $recoveryCodes = array_diff($recoveryCodes, [$code]);
            $user->two_factor_recovery_codes = encrypt(json_encode(array_values($recoveryCodes)));
            $user->save();
            
            return true;
        }

        return false;
    }
}