<?php

namespace App\Traits\Affiliates;

use App\Models\AffiliateHistory;
use App\Models\User;
use App\Models\AffiliateSettings;

trait AffiliateHistoryTrait
{
    /**
     * @param User $user
     * @param $commission
     * @return mixed
     */
    public static function saveAffiliateHistory(User $user)
    {
        $sponsor = User::find($user->inviter);

        if(!empty($sponsor)) {
            $affiliate = AffiliateHistory::where('user_id', $user->id)->first();
            if(empty($affiliate)) {
                // Usa as configurações personalizadas do afiliado
                $settings = AffiliateSettings::getOrCreateForUser($sponsor->id);
                
                // IMPORTANTE: Usa revshare_percentage (valor REAL) para cálculos
                if($settings->revshare_percentage > 0) {
                    AffiliateHistory::create([
                        'user_id' => $user->id,
                        'inviter' => $sponsor->id,
                        'commission' => $settings->revshare_percentage,
                        'commission_type' => 'revshare',
                        'deposited' => 0,
                        'losses' => 0,
                        'status' => 0
                    ]);
                }

                if($settings->cpa_value > 0) {
                    AffiliateHistory::create([
                        'user_id' => $user->id,
                        'inviter' => $sponsor->id,
                        'commission' => $settings->cpa_value,
                        'commission_type' => 'cpa',
                        'deposited' => 0,
                        'losses' => 0,
                        'status' => 0
                    ]);
                }
                return true;
            }
            return false;
        }
        return false;
    }
}
