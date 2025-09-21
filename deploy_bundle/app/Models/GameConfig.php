<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameConfig extends Model
{
    protected $table = 'game_configs';

    protected $fillable = [
        'meta_arrecadacao',
        'percentual_distribuicao',
        'modo_atual',
        'total_arrecadado',
        'total_distribuido',
        'minas_distribuicao',
        'minas_arrecadacao',
        'x_por_mina',
        'x_a_cada_5',
        'bet_loss',
        'modo_influenciador',
        'modo_perdedor',
        'start_cycle_at',
    ];
}
