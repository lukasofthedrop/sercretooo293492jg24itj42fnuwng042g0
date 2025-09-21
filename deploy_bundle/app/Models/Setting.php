<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'software_name',
        'software_description',

        /// logos e background
        'software_favicon',
        'software_logo_white',
        'software_logo_black',

        'currency_code',
        'decimal_format',
        'currency_position',
        'prefix',
        'storage',
        'min_deposit',
        'max_deposit',
        'min_withdrawal',
        'max_withdrawal',
        'default_gateway',

        // Percent
        'ngr_percent',
        'revshare_percentage',
        'revshare_reverse',

        /// soccer
        'soccer_percentage',
        'turn_on_football',

        'initial_bonus',

        /// campos do rollover
        'rollover',
        'rollover_deposit',
        'disable_rollover',
        'rollover_protection',

        /// campos do pagamento
        'suitpay_is_enable',
        'digito_is_enable',
        'ezzepay_is_enable',

        'stripe_is_enable',
        'bspay_is_enable',
        'saque',
        
        // Percent
        'cpa_value',
        'cpa_baseline',

        /// withdrawal limit
        'withdrawal_limit',
        'withdrawal_period',

        'disable_spin',


        /// sub afiliado
        'perc_sub_lv1',
        'perc_sub_lv2',
        'perc_sub_lv3',



    ];

    protected $hidden = array('updated_at');

    

}
