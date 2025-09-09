@extends('layouts.app')

@section('title', '2FA Já Configurado')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                2FA Já Configurado
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sua conta já possui autenticação de dois fatores ativada
            </p>
        </div>
        
        <div class="mt-8 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900">
                        Proteção Ativa
                    </h3>
                    
                    <p class="text-sm text-gray-600">
                        Sua conta está protegida com autenticação de dois fatores. 
                        Você precisará do código do seu aplicativo autenticador para fazer login.
                    </p>
                    
                    <div class="mt-6 space-y-3">
                        <a 
                            href="{{ url('/admin') }}" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Ir para Dashboard
                        </a>
                        
                        <form method="POST" action="{{ route('2fa.disable') }}" class="w-full">
                            @csrf
                            <div class="mb-3">
                                <input 
                                    type="password" 
                                    name="password" 
                                    placeholder="Digite sua senha para desativar"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500"
                                    required
                                >
                            </div>
                            <button 
                                type="submit" 
                                onclick="return confirm('Tem certeza que deseja desativar o 2FA? Isso reduzirá a segurança da sua conta.')"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                Desativar 2FA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection