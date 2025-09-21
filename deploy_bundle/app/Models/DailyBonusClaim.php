<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Certifique-se de usar o namespace correto para o model User

class DailyBonusClaim extends Model
{
    protected $table = 'daily_bonus_claims';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'claimed_at',
    ];

    /**
     * Relacionamento para buscar o usuário que fez o resgate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
