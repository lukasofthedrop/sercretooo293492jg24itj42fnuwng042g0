<?php

namespace App\Traits\Affiliates;

use App\Models\AffiliateHistory;
use App\Models\User;
use App\Models\Wallet;
use App\Models\AffiliateSettings;

trait EarningTrait
{
    /**
     * @param User $user // ID do afiliado
     * @return void
     */
    public static function affiliateRevshare(User $user)
    {
        $affiliateHistories = AffiliateHistory::where('inviter', $user->id)
            ->where('commission_type', 'revshare')
            ->where('status', 0)
            ->with('invited.wallet') // Eager loading para evitar N+1
            ->get();
        if(count($affiliateHistories) > 0) {
            foreach ($affiliateHistories as $affiliateHistory) {

                /// o valor de perda é maior que o valor depositado
                if($affiliateHistory->losses_amount >= $affiliateHistory->deposited_amount) {

                    /// pega a porcentagem do ganho
                    $gains = \Helper::porcentagem_xn($affiliateHistory->commission, $affiliateHistory->losses_amount);
                    // Cache wallet para evitar múltiplas queries
                    $wallet = $wallet ?? Wallet::where('user_id', $user->id)->first();
                    $wallet->increment('refer_rewards', $gains);
                }
            }
        }
    }

    /**
     * @param User $user // ID do afiliado
     * @return void
     */
    public static function affiliateCpa(User $user)
    {
        $affiliateHistories = AffiliateHistory::where('inviter', $user->id)
            ->where('commission_type', 'cpa')
            ->where('status', 0)
            ->with('invited.wallet') // Eager loading para evitar N+1
            ->get();
        if(count($affiliateHistories) > 0) {
            // Usa configurações seguras do AffiliateSettings
            $settings = AffiliateSettings::getOrCreateForUser($user->id);
            
            foreach ($affiliateHistories as $affiliateHistory) {
                /// o valor de perda é maior que o valor depositado
                if($affiliateHistory->losses_amount >= $affiliateHistory->deposited_amount) {

                    /// usa o valor CPA das configurações seguras
                    // Cache wallet para evitar múltiplas queries
                    $wallet = $wallet ?? Wallet::where('user_id', $user->id)->first();
                    $wallet->increment('refer_rewards', $settings->cpa_value);
                }
            }
        }
    }
}
