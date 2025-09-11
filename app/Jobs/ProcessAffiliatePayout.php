<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AffiliateHistory;

class ProcessAffiliatePayout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;
    
    protected $affiliateId;

    public function __construct($affiliateId)
    {
        $this->affiliateId = $affiliateId;
    }

    public function handle()
    {
        // Processar pagamento de afiliado de forma assíncrona
        $histories = AffiliateHistory::where('inviter', $this->affiliateId)
            ->where('status', 0)
            ->chunk(100, function($histories) {
                foreach($histories as $history) {
                    // Processar cada histórico
                    $history->process();
                }
            });
    }
}
