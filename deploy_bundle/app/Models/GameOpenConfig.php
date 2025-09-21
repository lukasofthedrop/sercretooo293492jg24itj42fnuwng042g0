<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameOpenConfig extends Model
{
    protected $table = 'game_open_configs';

    protected $fillable = [
        'requires_deposit_today',
    ];

    protected $casts = [
        'requires_deposit_today' => 'boolean',
    ];
}
