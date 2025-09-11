<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class BetHistory extends Model
{
    protected $table = 'bet_histories';

    protected $fillable = [
        'user_id',
        'bet_amount',
        'payout',
        'is_win',
        'stars_revealed',
        'bombs_count',
        'game_data',
        'house_profit',
        'typeWallet'
    ];

    protected $casts = [
        'game_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
