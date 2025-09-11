<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Gateway;
use App\Traits\Gateways\AureoLinkTrait;
use Illuminate\Support\Facades\Http;

class TestRealGateway extends Command
{
    use AureoLinkTrait;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:test-real-gateway 
                            {user_id : ID do usuário para teste}
                            {amount : Valor para testar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o gateway de pagamento REAL - CIRURGIÃO DEV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = $this->argument('amount');
        
        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║            TESTE DE GATEWAY REAL - AUREOLINK                  ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();
        
        // Verificar configuração
        $gateway = Gateway::first();
        
        if (!$gateway->aureolink_enabled || !$gateway->aureolink_production) {
            $this->error('❌ AureoLink não está ativo para produção!');
            $this->info('Ativando agora...');
            
            $gateway->aureolink_enabled = 1;
            $gateway->aureolink_production = 1;
            $gateway->save();
            
            $this->info('✅ AureoLink ativado para produção!');
        }
        
        $this->info('🔑 Credenciais AureoLink:');
        $this->info('   Client ID: ' . substr($gateway->aureolink_client_id, 0, 10) . '...');
        $this->info('   Production: ' . ($gateway->aureolink_production ? 'SIM ✅' : 'NÃO ❌'));
        $this->info('   Enabled: ' . ($gateway->aureolink_enabled ? 'SIM ✅' : 'NÃO ❌'));
        $this->newLine();
        
        // Buscar usuário
        $user = User::find($userId);
        if (!$user) {
            $this->error('❌ Usuário não encontrado!');
            return 1;
        }
        
        $this->info('👤 Usuário: ' . $user->name);
        $this->info('💰 Valor de teste: R$ ' . number_format($amount, 2, ',', '.'));
        $this->newLine();
        
        // Simular requisição como seria feita pelo frontend
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'amount' => $amount,
            'gateway' => 'aureolink',
            'user_id' => $userId
        ]);
        
        $this->info('📡 Conectando com AureoLink API...');
        
        try {
            // Usar o método real do trait
            $response = self::requestQrcodeAureoLink($request);
            
            // Converter JsonResponse para array
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);
            } else {
                $responseData = $response;
            }
            
            if (isset($responseData['status']) && $responseData['status']) {
                $this->info('✅ GATEWAY FUNCIONANDO!');
                $this->newLine();
                
                if (isset($responseData['qrcode'])) {
                    $this->info('📱 QR Code PIX gerado com sucesso!');
                    $this->info('   Código: ' . substr($responseData['qrcode'], 0, 50) . '...');
                }
                
                if (isset($responseData['payment_id'])) {
                    $this->info('   ID Pagamento: ' . $responseData['payment_id']);
                }
                
                $this->newLine();
                $this->info('🎉 GATEWAY AUREOLINK ESTÁ 100% FUNCIONAL!');
                
            } else {
                $this->error('❌ Erro na resposta do gateway');
                $this->error('Resposta: ' . json_encode($responseData));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao conectar com gateway: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}