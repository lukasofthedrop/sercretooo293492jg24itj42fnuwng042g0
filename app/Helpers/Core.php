<?php


namespace App\Helpers;

use App\Helpers\Core as Helper;
use App\Models\AffiliateHistory;
use App\Models\CustomLayout;
use App\Models\SpinConfigs;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SubAffiliate;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use NumberFormatter;
use Illuminate\Support\Facades\Http;

class Core
{
    public static function GetDefaultGateway()
    {
        // Verificar se AureoLink está ativo
        $aureolinkConfig = \DB::table('gateway_aureolink_config')->first();
        if ($aureolinkConfig && $aureolinkConfig->is_enabled) {
            return 'aureolink';
        }

        // Caso contrário, usar o gateway padrão das configurações
        $setting = Setting::first();
        return $setting->default_gateway ?? 'suitpay';
    }


    /*** @param Wallet $wallet
     * @param $bet
     * @return string
     */
    public static function DiscountBalance(Wallet $wallet, $bet)
    {

        $changeBonus = 'no_balance';

        if ($wallet->balance >= $bet) {
            $wallet->decrement('balance', $bet);
            return 'balance';
        } elseif ($wallet->balance_withdrawal >= $bet) {
            $wallet->decrement('balance_withdrawal', $bet);
            return 'balance_withdrawal';
        } elseif ($wallet->total_balance_without_bonus >= $bet) {
            $balanceBet = 0;
            $remainingBet = 0;

            if ($wallet->balance > 0) {
                $balanceBet = $wallet->balance;
                $wallet->decrement('balance', $wallet->balance);
            }

            if ($wallet->balance_withdrawal > 0) {
                $remainingBet = ($bet - $balanceBet);
                if ($wallet->balance_withdrawal >= $remainingBet) {
                    $wallet->decrement('balance_withdrawal', $remainingBet);
                }
            }

            return 'balance_withdrawal';
        }

        if (($wallet->balance > 0.50 || $wallet->balance_withdrawal > 0.50) && $wallet->total_balance_without_bonus < $bet) {
            return 'no_balance';
        } else {
            if ($wallet->balance_bonus >= $bet) {
                $wallet->decrement('balance_bonus', $bet); // retira do bônus
                return 'balance_bonus';
            }
        }

        return $changeBonus;
    }



    /**
     * @param $userId
     * @param $changeBonus
     * @param $WinAmount
     * @return void
     */
    public static function payWithRollover($userId, $changeBonus, $win, $bet, $type): void
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $setting = Setting::first();

        if (!empty($wallet)) {
            if ($setting->disable_rollover) {
                $wallet->increment('balance_withdrawal', $win);
            } else {
                if ($type === 'bet') {
                    
                    if($wallet->balance_bonus_rollover != 0){
                        //Rollover Bonus
                        if ($wallet->balance_bonus_rollover >= $bet) {
                            $wallet->decrement('balance_bonus_rollover', $bet); /// reduzindo o valor
                           
                        } else {
                            $wallet->update(['balance_bonus_rollover' => 0]); /// zerou o balance
                        }
                    }else{
                        //Rollover normal
                        if ($wallet->balance_deposit_rollover >= $bet) {
                            $wallet->decrement('balance_deposit_rollover', $bet); /// reduzindo o valor
    
                           
                        } else {
                            $wallet->update(['balance_deposit_rollover' => 0]); /// zerou o balance
    
                           
                        }
                    }
                   
                    
                    
                }

                if ($type === 'win') {
                    if ($wallet->balance_bonus_rollover === 0) {
                        /// zerando balance
                        $wallet->increment('balance_withdrawal', $wallet->balance_bonus); /// colocou toda essa soma para carteira de saque
                        $wallet->update(['balance_bonus' => 0]); /// zerou o balance
                    }
                    if ($wallet->balance_deposit_rollover === 0) {
                        /// zerando balance
                        $wallet->increment('balance_withdrawal', $wallet->balance); /// colocou toda essa soma para carteira de saque
                        $wallet->update(['balance' => 0]); /// zerou o balance
                    } 
                    if($wallet->balance_bonus_rollover != 0 && $wallet->balance_deposit_rollover <= 0 ){
                        if (empty($wallet->balance_bonus_rollover) || $wallet->balance_bonus_rollover <= 0) {
                            /// pagando o ganhos na carteira de saque
                            $wallet->increment('balance_withdrawal', $win);
                        } else {
                            if ($wallet->balance_bonus_rollover >= $bet) {
                                $wallet->decrement('balance_bonus_rollover', $bet); /// reduzindo o valor
                                $wallet->increment('balance_bonus', $win);
                                
                            } else {
                                /// caso contrario define como zero.
                                $wallet->update(['balance_bonus_rollover' => 0]);

                                $totalPay = ($wallet->balance_bonus + $win); /// pegou o saldo guardado o balance, pegou o win e somou
                                $wallet->increment('balance_withdrawal', $totalPay); /// colocou toda essa soma para carteira de saque
                                $wallet->update(['balance_bonus' => 0]); /// zerou o balance
                            }
                        }
                    }else{
                        if (empty($wallet->balance_deposit_rollover) || $wallet->balance_deposit_rollover <= 0) {
                            /// pagando o ganhos na carteira de saque
                            $wallet->increment('balance_withdrawal', $win);
                        } else {
                            if ($wallet->balance_deposit_rollover >= $bet) {
                                $wallet->decrement('balance_deposit_rollover', $bet); /// reduzindo o valor
                                $wallet->increment('balance', $win);
                                
                            } else {
                                    /// caso contrario define como zero.
                                $wallet->update(['balance_deposit_rollover' => 0]);

                                $totalPay = ($wallet->balance + $win); /// pegou o saldo guardado o balance, pegou o win e somou
                                $wallet->increment('balance_withdrawal', $totalPay); /// colocou toda essa soma para carteira de saque
                                $wallet->update(['balance' => 0]); /// zerou o balance
                            }
                            
                        }
                }
                }
            }
        }
    }

    /*** Distribuições
     *
     * Pega todas as distribuições
     
     * @return string[]
     */
    public static function getDistribution(): array
    {
        return [
            "play_fiver" => "Play Fiver"
        ];
    }


    /*** @param $order
     * @return string
     */
    public static function getTypeTransactionOrder($order)
    {
        switch ($order) {
            case 'balance_bonus':
                return 'Saldo Bônus';

            case 'balance':
                return 'Saldo Depósito';

            case 'balance_withdrawal':
                return 'Saldo de Saque';
        }
    }

    /*** @param $order
     * @return string
     */
    public static function getTypeOrder($order)
    {
        if($order == 'win') {
            return 'Ganho';
        }
    
        return 'Perda';
    }

    /*** Get Ative Wallet
     * Pegar uma carteira ativa
     
     * @return null
     */
    public static function getActiveWallet()
    {
        if (auth('api')->check()) {
            return Wallet::where('user_id', auth('api')->id())->where('active', 1)->first();
        }

        return null;
    }

    /**
     * @return void
     */
    public static function getGoogleFonts()
    {
        $response = Http::get('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyDQCZFgODu0jw7Ez00jgQU04SUsuncY3yQ');
        if ($response->successful()) {
        }
    }

    /**
     * @param $tamanhoCodigo
     * @return string
     */
    public static function generateCode($tamanhoCodigo)
    {
        $caracteresPermitidos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $codigo = '';

        for ($i = 0; $i < $tamanhoCodigo; $i++) {
            $codigo .= $caracteresPermitidos[rand(0, strlen($caracteresPermitidos) - 1)];
        }

        return $codigo;
    }

    /**
     * @param $odds
     * @return float|int
     */
    public static function calculateProbability($odds)
    {
        $odds = abs($odds);

        $probabilidade = 1 / $odds;
        return $probabilidade;
    }

    /**
     * @param $golsCasa
     * @param $golsVisitante
     * @param $oddsCasa
     * @param $oddsVisitante
     * @param float $multi
     * @return float|int
     */
    public static function calculateResultOdds($golsCasa, $golsVisitante, $oddsCasa, $oddsVisitante, float $multi = 2): float|int
    {
        // Calcula a média de gols esperada usando uma média ponderada
        $mediaGols = ($golsCasa * $oddsCasa + $golsVisitante * $oddsVisitante) / ($oddsCasa + $oddsVisitante);

        // Calcula a probabilidade de não haver gols
        $probabilidadeZeroGols = exp(-$mediaGols);

        // Calcula o novo odds para 0 gols
        $novoOdds = 1 / $probabilidadeZeroGols;

        return $novoOdds;
    }

    /**
     * @param $data
     * @return string|void
     */
    public static function getMatcheResult($data)
    {
        switch ($data) {
            case 0:
                return 'Pendente';
            case 1:
                return 'Finalizado';
        }
    }

    /**
     * @param $key
     * @return string
     */
    public static function checkPixKeyTypeSharkPay($key)
    {
        switch ($key) {
            case self::isCPF($key):
                return 'CPF';
            case self::isCNPJ($key):
                return 'CNPJ';
            case self::isMail($key):
                return 'EMAIL';
            case self::isTelefone($key):
                return 'PHONE';
            default:
                return 'EVP';
        }
    }

    private static function isMail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * @param $key
     * @return string
     */
    public static function checkPixKeyTypeSharkConnect($key)
    {
        switch ($key) {
            case self::isCPF($key):
            case self::isCNPJ($key):
                return 'document';
            case self::isTelefone($key):
                return 'phoneNumber';
            default:
                return 'randomKey';
        }
    }

    /**
     * @param $key
     * @return string|void
     */
    public static function formatPixType($key)
    {
        switch ($key) {
            case 'document':
                return 'Documento';
            case 'phoneNumber':
                return 'Telefone';
            case 'email':
                return 'E-mail';
            case 'randomKey':
                return 'Chave Aleatória';
            default:
                return $key;
        }
    }

    /**
     * @param $string
     * @return bool
     */
    private static function isTelefone($valor)
    {
        //processa a string mantendo apenas números no valor de entrada.
        $valor = preg_replace("/[^0-9]/", "", $valor);

        $lenValor = strlen($valor);

        //validando a quantidade de caracteres de telefone fixo ou celular.
        if ($lenValor != 10 && $lenValor != 11) {
            return false;
        }


        //DD e número de telefone não podem começar com zero.
        if ($valor[0] == "0" || $valor[2] == "0") {
            return false;
        }


        return true;
    }

    /**
     * @param $string
     * @return bool
     */
    private static function isCNPJ($string)
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $string);

        // Verifica se a string tem 14 caracteres numéricos
        if (strlen($cnpj) !== 14) {
            return false;
        }

        return true; // Retorne true se for um CNPJ válido
    }

    /**
     * @param $string
     * @return bool
     */
    private static function isCPF($cpf)
    {
        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    /**
     * Gera um CPF válido aleatório usando o algoritmo brasileiro
     * @return string
     */
    public static function generateValidCPF()
    {
        // Gerar 9 dígitos aleatórios
        $cpf = '';
        for ($i = 0; $i < 9; $i++) {
            $cpf .= rand(0, 9);
        }

        // Calcular primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : (11 - $remainder);
        $cpf .= $digit1;

        // Calcular segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : (11 - $remainder);
        $cpf .= $digit2;

        return $cpf;
    }

    /**
     * Gera um CPF válido único (não existente no banco de dados)
     * @return string
     */
    public static function generateUniqueCPF()
    {
        do {
            $cpf = self::generateValidCPF();
            $exists = \App\Models\User::where('cpf', $cpf)->exists();
        } while ($exists);

        return $cpf;
    }

    /**
     * @param $nomeCompleto
     * @return string
     *
     */
    public static function hideString($nomeCompleto)
    {
        $primeiraParteNome = substr($nomeCompleto, 0, 2);
        $asteriscos = str_repeat('*', 4);
        $nomeOculto = $primeiraParteNome . $asteriscos;  // Neste caso, "Vic******"

        return $nomeOculto;
    }


    /**
     * @param $controllerName
     * @return mixed
     * @throws \Exception
     */
    public static function createController($controllerName)
    {
        $fullControllerName = "App\Http\Controllers\Games\\" . ucfirst($controllerName) . "Controller";

        if (class_exists($fullControllerName)) {
            return new $fullControllerName();
        } else {
            // Caso a classe não exista, você pode lidar com isso aqui
            throw new \Exception("Controller não encontrado: $fullControllerName");
        }
    }

    /*** Generate Game History
     * Metodo responsavel pelo historico dos jogos, e também controle de  ganhos de afiliados
     * Como revshare e CPA.
     *
     
     * @param $userId
     * @param $type
     * @param $amount
     * @param $nameGame
     * @param $gameId
     * @param $changeBonus
     * @return mixed
     */
    public static function generateGameHistory($userId, $type, $win, $bet, $changeBonus, $tx)
    {

        $user = User::find($userId);


        if ($changeBonus != 'source') {
            /// pagar afiliado
            if ($type == 'bet' && !empty($user->inviter)) {
                $affiliate = User::find($user->inviter);
                self::PayLoss($user, $affiliate, $bet, $changeBonus);
            }

            if ($type == 'win' && !empty($user->inviter)) {
                $affiliate  = User::find($user->inviter);
                self::PayWin($user, $affiliate, $type, $win, $changeBonus);
            }

            if ($type == 'win') {
                $transaction = Order::where('transaction_id', $tx)->where('type', 'check')->where('status', 0)->first();
                if (!empty($transaction)) {
                    $transaction->update(['status' => 1, 'type' => $type, 'amount' => $win]);
                }
            }

            if ($type == 'bet') {
                $transaction = Order::where('transaction_id', $tx)->where('type', 'check')->where('status', 0)->first();
                if (!empty($transaction)) {
                    $transaction->update(['status' => 1, 'type' => $type, 'amount' => $bet]);
                }
            }
        } else {

            $transaction = Order::where('transaction_id', $tx)->where('type', 'check')->where('status', 0)->first();

            if (!empty($transaction) && $user->is_demo_agent == false) {

                if (!empty($user->inviter)) {
                    $affiliate = User::find($user->inviter);
                    self::PayLoss($user, $affiliate, $bet, $transaction->type_money);
                }

                if (!empty($user->inviter)) {
                    $affiliate  = User::find($user->inviter);
                    self::PayWin($user, $affiliate, $type, $win, $transaction->type_money);
                }

                if ($type == 'win') {
                    $transaction->update(['status' => 1, 'type' => $type, 'amount' => $win]);
                }

                if ($type == 'bet') {
                    $transaction->update(['status' => 1, 'type' => $type, 'amount' => $bet]);
                }
            }
        }

        /// pagando o premio
        if ($user->is_demo_agent == false && $changeBonus != 'source') {
            self::payWithRollover($userId, $changeBonus, $win, $bet, $type); /// verifica o rollover
            return true;
        }

        return false;
    }

    /*** PayLoss
     *
     * @param $user
     * @param $affiliate
     * @param $bet
     * @param $changeBonus
     * @return float|int
     */
    private static function PayLoss($user, $affiliate, $bet, $changeBonus)
    {
        $setting = self::getSetting();
        if (!empty($affiliate)) {
            $affHistoryRevshare = AffiliateHistory::where('user_id', $user->id)
                ->where('commission_type', 'revshare')
                //->where('deposited', 1)
                //->where('status', 0)
                ->first();

            if (!empty($affHistoryRevshare) && in_array($changeBonus, ['balance', 'balance_withdrawal'])) {

                /// verificar qual revshare pagar, o fake ou o padrão
                $revshare = $affiliate->affiliate_revenue_share;

                $lossPercentage = self::porcentagem_xn($revshare, $bet); /// calcula a porcentagem da perda, com o valor apostado na variavel $bet
                $ngr = self::porcentagem_xn($setting->ngr_percent, $lossPercentage); /// calcula a porcentagem do ngr

                $commissionPay = ($lossPercentage - $ngr); /// subtrai o ngr

                /// verificar a arvore de subafiliados

                /// level 1
                if (!empty($affiliate->inviter)) {
                    $affiliatelv1 = User::find($affiliate->inviter);
                    if (!empty($affiliatelv1)) {

                        /// pagando lvl1
                        $percentageLvl1 = self::porcentagem_xn($setting->perc_sub_lv1, $commissionPay); /// calcula a porcentagem da perda, com o valor apostado na variavel $bet
                        $affiliatelv1->wallet->increment('refer_rewards', $percentageLvl1);


                        $commissionPay = ($commissionPay - $percentageLvl1);

                        /// level 2
                        if (!empty($affiliatelv1->inviter)) {
                            $affiliatelv2 = User::find($affiliatelv1->inviter);
                            if (!empty($affiliatelv2)) {
                                /// pagando lvl2
                                $percentageLvl2 = self::porcentagem_xn($setting->perc_sub_lv2, $commissionPay); /// calcula a porcentagem da perda, com o valor apostado na variavel $bet
                                $affiliatelv2->wallet->increment('refer_rewards', $percentageLvl2);

                                $commissionPay = ($commissionPay - $percentageLvl2);

                                /// level 3
                                $affiliatelv3 = User::find($affiliatelv2->inviter);
                                if (!empty($affiliatelv3)) {
                                    /// pagando lvl3
                                    $percentageLvl3 = self::porcentagem_xn($setting->perc_sub_lv3, $commissionPay); /// calcula a porcentagem da perda, com o valor apostado na variavel $bet
                                    $affiliatelv3->wallet->increment('refer_rewards', $percentageLvl3);

                                    $commissionPay = ($commissionPay - $percentageLvl3);
                                }
                            }
                        }
                    }
                }

                /// Pagando o afiliado principal
                $affiliate->wallet->increment('refer_rewards', $commissionPay);
                $affHistoryRevshare->increment('commission', $commissionPay);
                $affHistoryRevshare->increment('commission_paid', $commissionPay);
                $affHistoryRevshare->increment('losses', 1);
                $affHistoryRevshare->increment('losses_amount', $bet);
                //$affHistoryRevshare->update(['receita' => $affHistoryRevshare->losses_amount - $affHistoryRevshare->deposited_amount]);

                return ($bet - $commissionPay);
            }
        }
    }

    /*** @param $user
     * @param $affiliate
     * @param $type
     * @param $amount
     * @param $changeBonus
     * @return float|int|void
     */
    private static function PayWin($user, $affiliate, $type, $amount, $changeBonus)
    {

        $wallet     = Wallet::where('user_id', $user->inviter)->where('active', 1)->first();
        $setting    = self::getSetting();

        /// subtrair afiliado ganhos
        if ($setting->revshare_reverse) {
            if ($type == 'win' && !empty($user->inviter) && in_array($changeBonus, ['balance', 'balance_withdrawal'])) {
                if (!empty($affiliate)) {
                    $affHistoryRevshare = AffiliateHistory::where('user_id', $user->id)
                        ->where('commission_type', 'revshare')
                        // ->where('deposited', 1)
                        ->where('status', 0)
                        ->first();


                    if (!empty($affHistoryRevshare)) {
                        /// verificar qual revshare pagar, o fake ou o padrão
                        $revshare = $affiliate->affiliate_revenue_share_fake ?? $affiliate->affiliate_revenue_share;

                        $commissionSub = self::porcentagem_xn($revshare, $amount); /// calcula a porcentagem da perda, com o valor ganhado na variavel $amount

                        $wallet->decrement('refer_rewards', $commissionSub); /// remove do afiliado o valor perdido pelo apostador

                        $affHistoryRevshare->decrement('commission', $commissionSub); /// valor pago de comissão
                        $affHistoryRevshare->decrement('commission_paid', $commissionSub); /// valor pago de comissão historico
                        $affHistoryRevshare->increment('deposited', 1);
                        $affHistoryRevshare->increment('deposited_amount', $amount);

                        //$affHistoryRevshare->update(['receita' => $affHistoryRevshare->losses_amount - $affHistoryRevshare->deposited_amount]);
                        return $amount;
                    }
                    return $amount;
                }
                return $amount;
            }
            return $amount;
        }
        return $amount;
    }

    /**
     * @param $arr
     * @return int|mixed
     */
    public static function CountScatter($arr)
    {
        $count_scarter = array_count_values($arr);

        if (isset($count_scarter['Symbol_1'])) {
            return $count_scarter['Symbol_1'];
        }

        return 0;
    }

    /**
     * @param $drops
     * @return int[]
     */
    public static function MultiplyCount($drops)
    {
        global $multiples;
        if ($drops > 3) {
            $drops = 3;
        }
        return $multiples[$drops] ?? null;
    }

    /**
     * @param $val
     * @param $digits
     * @return float
     */
    public static function ToFloat($val, $digits = 2)
    {
        return (float)number_format($val, $digits, '.', '');
    }

    /**
     * @param $lines
     * @return float|int|mixed
     */
    public static function CalcWinActiveLine($lines)
    {
        $aux = 0;

        if (sizeof($lines) > 0) {
            foreach ($lines as $line) {
                $aux = $aux + ($line['payout'] * $line['multiply']);
            }
        }

        return $aux;
    }

    /**
     * @param $drops
     * @param $mult
     * @return array
     */
    public static function CalcWinDropLine($drops, $mult)
    {
        $total = 0;
        foreach ($drops as $drop) {
            $amout = self::CalcWinActiveLine($drop['ActiveLines']);
            $total = $total + $amout;
            // $drop['ActiveLines']['win_amount'] = $amout;
        }
        $total = $total * $mult;
        return compact(['drops', 'total']);
    }


    /**
     * @param $data
     */
    public static function arrayToObject($data)
    {
        $collection = collect($data);

        $objects = $collection->map(function ($item) {
            return array_combine(range(1, count($item)), $item);
        });

        return $objects;
    }


    /**
     * @return null
     */
    public static function getToken()
    {
        if (auth()->check()) {
            $token = \Helper::MakeToken([
                'id' => auth()->id()
            ]);

            return $token;
        }

        return null;
    }

    /**
     * @return float
     */
    public static function getBalance()
    {
        if (auth()->check()) {
            return self::amountFormatDecimal(auth()->user()->wallet->balance + auth()->user()->wallet->balance_bonus);
        } else {
            return self::amountFormatDecimal(0.00);
        }
    }

    /**
     * Get Settings
     * @return \Illuminate\Cache\
     */
    public static function getCustom()
    {
        $custom = null;
        if (Cache::has('custom')) {
            $custom = Cache::get('custom');
        } else {
            $custom = CustomLayout::first();
            Cache::put('custom', $custom);
        }

        return $custom;
    }

    /**
     * Get Settings
     * @return \Illuminate\Cache\
     */
    public static function getSetting()
    {
        $setting = Setting::select(
            'software_name',
            'software_description',
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
            'initial_bonus',
            'suitpay_is_enable',
            'bspay_is_enable',
            'stripe_is_enable',
            'digito_is_enable',
            'ezzepay_is_enable',
            'disable_spin',
            'cpa_baseline',
            'cpa_value',
            'saque',
            'rollover',
            'rollover_deposit',
            'disable_rollover',
            'rollover_protection'
            
        )->first();
        
        $layout = CustomLayout::select(

            'link_app',
            'link_telegram',
            'link_facebook',
            'link_whatsapp',
            'link_instagram',
            'link_siderbar_1',
            'link_siderbar_2',
            'link_siderbar_3',
            'link_lincenca',

            'navbar_imglogin',
            'menucell_inicio',
            'menucell_carteira',
            'menucell_suporte',
            'menucell_afiliado',
            'menucell_saque',
            'menucell_sair',
            'footer_imagen1',
            'footer_imagen2',
            'footer_imagen3',
            'footer_telegram',
            'footer_facebook',
            'footer_whatsapp',
            'footer_instagram',
            'footer_mais18',
            'navebar_app',
            
            'homepage_jogos_em_destaque',
            'vip_titulo',
            'vip_descriçao',
            'vip_sub_texto',
            'vip_sub_titulo',

            'image_Jackpot',
            'image_navbar',
            'image_hot1',
            'image_hot2',
            'image_hot3',
            'image_hot4',
            'image_hot5',
            'banner_deposito1',
            'banner_registro',
            'banner_login',
            'banner_deposito2',
            'popup_fluatuante',
            'link_fluatuante',
            'link_suporte',
            'token_jivochat',
            'popup2_fluatuante',
            'link_fluatuante2',
            'idPixelFC',
            'custom_modal_pop',
            'idPixelGoogle'
        )->first();
        $setting->custom = $layout;
                
        return $setting;
    }

    /**
     * @param $bytes
     * @return string
     */
    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /*** Amount Format Decimal
     
     *
     * @param $value
     * @return string
     */
    public static function amountFormatDecimalAdmin($value)
    {
        if (auth()->check()) {
            $wallet = Wallet::whereUserId(auth()->user()->id)->first();

            $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
            return $formatter->formatCurrency(floatval($value), $wallet->currency);
        }

        return 0;
    }

    /**
     * Amount Format Decimal
     *
     * @param $value
     * @return string
     */
    public static function amountFormatApi($value)
    {
        if (auth('api')->check()) {
            $wallet = Wallet::whereUserId(auth('api')->user()->id)->first();

            $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
            return $formatter->formatCurrency(floatval($value), $wallet->currency);
        }

        return 0;
    }

    /**
     * Amount Format Decimal
     *
     * @param $value
     * @return string
     */
    public static function amountFormatDecimal($value)
    {
        $settings = self::getSetting();

        if ($settings->currency_code == 'JPY') {
            return $settings->currency_symbol . number_format($value);
        }

        if ($settings->decimal_format == 'dot') {
            $decimalDot = ',';
            $decimalComma = '.';
        } else {
            $decimalDot = '.';
            $decimalComma = ',';
        }

        if ($settings->currency_position == 'left') {
            $amount = ($settings->prefix ?? 'R$') . number_format(floatval($value), 2, $decimalDot, $decimalComma);
        } elseif ($settings->currency_position == 'right') {
            $amount = number_format(floatval($value), 2, $decimalDot, $decimalComma) . ($settings->prefix ?? 'R$');
        } else {
            $amount = $settings->prefix . number_format(floatval($value), 2, $decimalDot, $decimalComma);
        }

        return $amount;
    }

    /*** Days In Month
     
     *
     * @param $month
     * @param $year
     * @return int
     */
    public static function daysInMonth($month, $year)
    {
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    /**
     * @param $date
     * @return array|string|string[]
     */
    public static function formatDateToHumman($date)
    {
        $created_at = Carbon::parse($date)->diffForHumans();
        $created_at = str_replace([' seconds', ' second'], ' sec', $created_at);
        $created_at = str_replace([' minutes', ' minute'], ' min', $created_at);
        $created_at = str_replace([' hours', ' hour'], ' h', $created_at);
        $created_at = str_replace([' months', ' month'], ' m', $created_at);

        if (preg_match('(years|year)', $created_at)) {
            $created_at = Carbon::parse($date)->toFormattedDateString();
        }

        return $created_at;
    }

    /**
     * @param $string
     * @return mixed
     */
    public static function getFirstUrl($string)
    {
        preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $string, $_matches);
        $firstURL = $_matches[0][0] ?? false;
        if ($firstURL) {
            return $firstURL;
        }
    }

    /**
     * @param $url
     * @return string
     */
    public static function videoUrl($url)
    {
        $urlValid = filter_var($url, FILTER_VALIDATE_URL) ? true : false;

        if ($urlValid) {
            $parse = parse_url($url);
            $host  = strtolower($parse['host']);

            if ($host) {
                if (in_array($host, array(
                    'youtube.com',
                    'www.youtube.com',
                    'youtu.be',
                    'www.youtu.be',
                    'vimeo.com',
                    'player.vimeo.com'
                ))) {
                    return $host;
                }
            }
        }
    }


    /**
     * Upload
     *
     * @param $file
     * @return array
     */
    public static function upload($file)
    {
        // Validação de tipo de arquivo
        $allowedMimes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $extension = strtolower($file->extension());
        
        if (!in_array($extension, $allowedMimes)) {
            throw new \Exception('Tipo de arquivo não permitido. Apenas: ' . implode(', ', $allowedMimes));
        }
        
        // Validação de tamanho (máx 5MB = 5242880 bytes)
        $maxSize = 5242880;
        $size = $file->getSize();
        
        if ($size > $maxSize) {
            throw new \Exception('Arquivo muito grande. Tamanho máximo: 5MB');
        }
        
        // Validação MIME type real (previne renomeação de arquivos)
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png', 
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new \Exception('Tipo MIME inválido: ' . $mimeType);
        }
        
        // Gerar nome único para evitar sobrescrita
        $fileName = Str::uuid() . '.' . $extension;
        $path = Storage::disk('public')->putFileAs('uploads', $file, $fileName);

        if ($path) {
            return [
                'path'      => $path,
                'name'      => $fileName,
                'extension' => $extension,
                'size'      => $size
            ];
        } else {
            return false;
        }
    }

    /**
     * Format Number
     *
     * @param $number
     * @return mixed|string
     */
    public static function formatNumber($number)
    {
        if ($number >= 1000 &&  $number < 1000000) {
            return number_format($number / 1000, 1) . "k";
        } else if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . "M";
        } else {
            return $number;
        }
    }

    /**
     * Check Text
     */
    public static function checkText($str, $url = null)
    {
        if (mb_strlen($str, 'utf8') < 1) {
            return false;
        }

        $str = str_replace($url, '', $str);

        $str = trim($str);
        $str = nl2br(e($str));
        $str = str_replace(array(chr(10), chr(13)), '', $str);
        $url = preg_replace('#^https?://#', '', url('') . '/');

        $regex = "~([@])([^\s@!\"\$\%&\'\(\)\*\+\,\-./\:\;\<\=\>?\[/\/\/\\]\^\`\{\|\}\~]+)~";
        $str = preg_replace($regex, '<a href="//' . $url . '$2">$0</a>', $str);

        $str = stripslashes($str);
        return $str;
    }

    /**
     * @param $path
     * @return string
     */
    public static function getFile($path)
    {
        return url($path);
    }

    /**
     * Prepare Fields Array
     *
     * @param $data
     * @return array
     */
    public static function prepareFieldsArray($data)
    {
        return array_filter($data);
    }

    /**
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * @param $extension
     * @return string|null
     */
    public static function fileTypeUpload($extension)
    {
        switch ($extension) {
            case 'jpeg':
            case 'bmp':
            case 'png':
            case 'gif':
            case 'jfif':
            case 'jpg':
            case 'svg':
                return 'image';
                break;

            case 'doc':
            case 'pdf':
            case 'docx':
            case 'txt':
                return 'document';
                break;

            case 'mp3':
            case 'wav':
                return 'audio';
                break;

            case 'rar':
            case 'zip':
                return 'file';
                break;

            case 'mov':
            case 'ts':
            case 'm3u8':
            case 'flv':
            case '3gp':
            case 'ogg':
            case 'mp4':
            case 'avi':
                return 'video';
                break;
            default:
                return 'image';
                break;
        }
    }

    /**
     * @param $country
     * @return bool
     */
    public static function getCountry($country)
    {
        if (!is_null($country)) {
            $country = \DB::table('countries')->where('iso', strtoupper($country))->first();
            if (!is_null($country)) {
                return $country->name;
            }

            return $country;
        }
        return 'US';
    }

    /**
     * @param $country
     * @return bool
     */
    public static function getCountryByCode($country)
    {
        if (!is_null($country)) {
            $country = \DB::table('countries')->where('iso', strtoupper($country))->first();
            if (!is_null($country)) {
                return $country->name;
            }

            return $country;
        }
        return 'US';
    }


    /**
     * Format Checkbox
     * @param $value
     * @return int
     */
    public static function formatCheckBox($value)
    {
        return ($value == 'yes' ? 1 : 0);
    }

    /***
     * Função de porcentagem: Quanto é X% de N?
     * // Utilização
    echo "Quanto é 11% de 22: <b>" . porcentagem_xn(11, 22) . "</b> rn <br>";
    echo "Quanto é 22% de 11: <b>" . porcentagem_xn(22, 11) . "</b> rn <br>";
    echo "Quanto é 99% de 100: <b>" . porcentagem_xn(99, 100) . "</b> rn <br>";
    echo "Quanto é 99% de 105: <b>" . porcentagem_xn(99, 105) . "</b> rn <br>";
    echo "Quanto é 201% de 105: <b>" . porcentagem_xn(201, 105) . "</b> rn <br>";
     * @param $porcentagem
     * @param $total
     * @return float|int
     */
    public static function porcentagem_xn($porcentagem, $total)
    {
        return ($porcentagem / 100) * $total;
    }

    /*** Função de porcentagem: N é X% de N
     *
    echo "2.42 é <b>" . porcentagem_nx(2.42, 22) . "%</b> de 22.rn <br>";
    echo "2.42 é <b>" . porcentagem_nx(2.42, 11) . "%</b> de 11.rn <br>";
    echo "99 é <b>" . porcentagem_nx(99, 100) . "%</b> de 100.rn <br>";
    echo "103.95 é <b>" . porcentagem_nx(103.95, 105) . "%</b> de 105.rn <br>";
    echo "211.05 é <b>" . porcentagem_nx(211.05, 105) . "%</b> de 105.rn <br>";
     * @param $parcial
     * @param $total
     * @return float|int
     */
    public static function porcentagem_nx($parcial, $total)
    {
        if (!empty($parcial) && !empty($total)) {
            return ($parcial * 100) / $total;
        } else {
            return 0;
        }
    }

    /*** Função de porcentagem: N é N% de X
     * // Utilização
    echo "2.42 é 11% de <b>" . porcentagem_nnx ( 2.42, 11 ) . "</b></b>.rn <br>";
    echo "2.42 é  22% de <b>" . porcentagem_nnx ( 2.42, 22 ) . "</b></b>.rn <br>";
    echo "99 é 100% de <b>" . porcentagem_nnx ( 99, 100 ) . "</b></b>.rn <br>";
    echo "103.95 é  99% de <b>" . porcentagem_nnx ( 103.95, 99 ) . "</b></b>.rn <br>";
    echo "2.42 é 11% de <b>" . porcentagem_nnx ( 211.05, 201 ) . "</b></b>.rn <br>";
    echo "337799 é 70% de <b>" . porcentagem_nnx ( 337799, 70 ) . "</b></b>.rn <br>";
     * @param $parcial
     * @param $porcentagem
     * @return float|int
     */
    function  porcentagem_nnx($parcial, $porcentagem)
    {
        return ($parcial / $porcentagem) * 100;
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function formatCurrencyByRegion($amount, $currency = 'BRL'): mixed
    {
        $locale = str_replace('_', '-', app()->getLocale()); // Substitua pelo código do país/região desejado

        // Crie um objeto NumberFormatter para a região desejada
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Formate o valor da moeda usando o NumberFormatter
        $formattedCurrency = $formatter->formatCurrency($amount, $currency); // Substitua 'BRL' pelo código da moeda desejada

        // Retorne o valor formatado
        return $formattedCurrency;
    }

    /**
     * @param $str
     * @return null|string|string[]
     */
    public static function soNumero($str)
    {
        return preg_replace("/[^0-9]/", "", $str);
    }

    /**
     * Amount Prepare
     * @param $float_dollar_amount
     * @return string
     */
    public static function amountPrepare($float_dollar_amount)
    {
        $separators_only = preg_filter('/[^,\.]/i', '', $float_dollar_amount);

        if (strlen($separators_only) > 1) {
            if (substr($separators_only, 0, 1) == '.') {
                $float_dollar_amount = str_replace('.', '', $float_dollar_amount);
                $float_dollar_amount = str_replace(',', '.', $float_dollar_amount);
            } else if (substr($separators_only, 0, 1) == ',') {
                $float_dollar_amount = str_replace(',', '', $float_dollar_amount);
            }
        } else if (strlen($separators_only) == 1 && $separators_only == ',') {
            $float_dollar_amount = str_replace(',', '.', $float_dollar_amount);
        }

        return $float_dollar_amount;
    }

    /**
     * @param $currency
     * @return string
     */
    public static function checkPrefixCurrency($currency)
    {
        switch ($currency) {
            case '$':
                return 'USD';
                break;
            case 'R$':
                return 'BRL';
                break;
            default:
                return 'USD';
        }
    }


    /**
     * @param $array
     * @return mixed
     */
    public static function MakeToken($array)
    {
        if (is_array($array)) {
            $output =  '{"status": true';
            $interacao = 0;
            foreach ($array as $key => $value) {
                $output .=  ',"' . $key . '"' . ': "' . $value . '"';
            }
            $output .= "}";
        } else {
            $er_txt = self::Decode('QVakfW0DwcOie2aD9kog9oRx81VtX73oY1Vn91o7YVamZVa2eVaxYkwofGadZGadfGope2aB9zJgbVapYXJgX5R6YWJgeGgg9h');
            $output = str_replace('_', '&nbsp;', $er_txt);
            exit($output);
        }
        return self::Encode($output);
    }


    /**
     * @param $token
     * @return mixed|string
     */
    public static function DecToken($token)
    {
        $json = self::Decode($token);
        if (is_numeric($json)) {
            return $token;
        } else if (self::isJson($json)) {
            $json = str_replace("{\"email", "{\"status\":true ,\"email", $json);
            return json_decode($json, true);
        } else {
            return array("status" => false, "messase" => "invalid token");
        }
    }

    /**
     * @param $string
     * @return bool
     */
    private static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $texto
     * @return string
     */
    public static function Encode($texto)
    {
        $retorno = "";
        $saidaSubs = "";
        $texto = base64_encode($texto);
        $busca0 = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "w", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "=");
        $subti0 = array("8", "e", "9", "f", "b", "d", "h", "g", "j", "i", "m", "o", "k", "z", "l", "w", "4", "s", "r", "u", "t", "x", "v", "p", "6", "n", "7", "2", "1", "5", "q", "3", "y", "0", "c", "a", "");

        for ($i = 0; $i < strlen($texto); $i++) {
            $ti = array_search($texto[$i], $busca0);
            if ($busca0[$ti] == $texto[$i]) {
                $saidaSubs .= $subti0[$ti];
            } else {
                $saidaSubs .= $texto[$i];
            }
        }
        $retorno = $saidaSubs;

        return $retorno;
    }

    /**
     * @param $texto
     * @return string
     */
    public static function Decode($texto)
    {
        $retorno = "";
        $saidaSubs = "";
        $busca0 = array("8", "e", "9", "f", "b", "d", "h", "g", "j", "i", "m", "o", "k", "z", "l", "w", "4", "s", "r", "u", "t", "x", "v", "p", "6", "n", "7", "2", "1", "5", "q", "3", "y", "0", "c", "a");
        $subti0 = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "x", "w", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

        for ($i = 0; $i < strlen($texto); $i++) {
            $ti = array_search($texto[$i], $busca0);
            if ($busca0[$ti] == $texto[$i]) {
                $saidaSubs .= $subti0[$ti];
            } else {
                $saidaSubs .= $texto[$i];
            }
        }

        $retorno = base64_decode($saidaSubs);

        return $retorno;
    }

    /**
     * @return mixed
     */
    public static function WheelPrizes()
    {
        $key = 'spin:config:prizes';
        $cached = Cache::get($key);
        $config = NULL;

        if (!$cached) {
            $c = SpinConfigs::latest()->first();
            $str = $c->prizes;
            Cache::set($key, $str);
            $config = json_decode($str);
        } else {
            $config = json_decode($cached);
        }

        return $config;
    }

    /***
     * @param $length
     * @return string
     */
    public static function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}

