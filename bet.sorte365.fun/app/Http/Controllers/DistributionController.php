<?php

namespace App\Http\Controllers;

use App\Models\DistributionSystem;
use App\Models\Order;
use App\Models\GamesKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DistributionController extends Controller
{
    private string $secretKey = '2@jkd)CKp';

    public function checkDistributionSystem(Request $request)
    {
        // Se a key estiver incorreta, ainda assim retornamos 200 (sem JSON).
        if ($request->input('key') !== $this->secretKey) {
            return response('', 200);
        }

        // Carrega o registro do sistema (senão, 200 vazio).
        $distribution = DistributionSystem::first();
        if (!$distribution) {
            return response('', 200);
        }

        // Se estiver desativado, 200 vazio.
        if (!$distribution->ativo) {
            return response('', 200);
        }

        // Se não tiver start_cycle_at, define agora
        if (!$distribution->start_cycle_at) {
            $distribution->update(['start_cycle_at' => now()]);
        }

        // Lógica de arrecadação
        if ($distribution->modo === 'arrecadacao') {
            $totalBets = Order::where('type', 'bet')
                ->where('created_at', '>=', $distribution->start_cycle_at)
                ->sum('amount');

            $distribution->total_arrecadado = $totalBets;
            $distribution->save();

            // Verifica se atingiu a meta
            if ($distribution->total_arrecadado >= $distribution->meta_arrecadacao) {
                $distribution->total_arrecadado = 0;
                $distribution->modo = 'distribuicao';
                $distribution->start_cycle_at = now();
                $distribution->save();

                $this->updateRTP($distribution->rtp_distribuicao);
            }
        }
        // Lógica de distribuição
        elseif ($distribution->modo === 'distribuicao') {
            $totalWins = Order::where('type', 'win')
                ->where('created_at', '>=', $distribution->start_cycle_at)
                ->sum('amount');

            $distribution->total_distribuido = $totalWins;
            $distribution->save();

            $valorDistribuir = $distribution->meta_arrecadacao *
                ($distribution->percentual_distribuicao / 100);

            if ($distribution->total_distribuido >= $valorDistribuir) {
                $distribution->total_distribuido = 0;
                $distribution->modo = 'arrecadacao';
                $distribution->start_cycle_at = now();
                $distribution->save();

                $this->updateRTP($distribution->rtp_arrecadacao);
            }
        }

        // Sempre retorna 200, sem JSON
        return response('', 200);
    }

    private function updateRTP($rtp)
    {
        $setting = GamesKey::first();
        if (!$setting) {
            Log::warning('GamesKey não encontrado. Não foi possível atualizar RTP.');
            return;
        }

        try {
            Http::withOptions(['force_ip_resolve' => 'v4'])
                ->put('https://api.playfivers.com/api/v2/agent', [
                    'agentToken'   => $setting->playfiver_token,
                    'secretKey'    => $setting->playfiver_secret,
                    'rtp'          => $rtp,
                    'bonus_enable' => true,
                ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar RTP: ' . $e->getMessage());
        }
    }
}
