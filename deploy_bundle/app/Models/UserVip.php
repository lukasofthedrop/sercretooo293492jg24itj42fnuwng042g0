<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vip_id',
        'last_reward_claimed_at',
    ];

    /**
     * Relacionamento com o usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com o nível VIP.
     */
    public function vip()
    {
        return $this->belongsTo(Vip::class);
    }
}
