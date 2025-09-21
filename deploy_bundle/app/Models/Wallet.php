<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wallets';
    protected $appends = ['total_balance'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'currency',
        'symbol',
        'balance',
        'balance_withdrawal',
        'balance_deposit_rollover',
        'balance_bonus',
        'balance_bonus_rollover',
        'balance_cryptocurrency',
        'balance_demo',
        'refer_rewards',
        'total_bet',
        'total_won',
        'total_lose',
        'last_won',
        'last_lose',
        'hide_balance',
        'active',
        'getaway'
    ];

    /**
     * Get the total balance.
     *
     * @return float
     */
    public function getTotalBalanceAttribute(): float
    {
        return $this->balance + $this->balance_bonus + $this->balance_withdrawal;
    }

    /**
     * Get the user that owns the wallet.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
