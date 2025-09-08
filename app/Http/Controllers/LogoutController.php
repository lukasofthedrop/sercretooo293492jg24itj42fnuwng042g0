<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Logout completo do sistema
     * Limpa a sessão e redireciona baseado na origem
     */
    public function logout(Request $request)
    {
        // Verificar de onde veio o logout
        $fromAffiliate = str_contains($request->headers->get('referer'), '/afiliado');
        
        // Fazer logout
        Auth::logout();
        
        // Invalidar a sessão
        $request->session()->invalidate();
        
        // Regenerar o token CSRF
        $request->session()->regenerateToken();
        
        // Redirecionar baseado na origem
        if ($fromAffiliate) {
            return redirect('/afiliado/login');
        }
        
        return redirect('/admin/login');
    }
    
    /**
     * Página para escolher entre admin ou afiliado
     */
    public function escolherPainel()
    {
        // Se já está logado, redireciona baseado no tipo de usuário
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->hasRole('admin')) {
                return redirect('/admin');
            } elseif ($user->inviter_code) {
                return redirect('/afiliado');
            }
        }
        
        // Mostrar página de escolha
        return view('auth.escolher-painel');
    }
}