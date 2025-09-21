<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vip extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'required_missions',
        'weekly_reward',
        'image',
    ];

    /**
     * Relacionamento com usuários que possuem este nível VIP.
     */
    public function users()
    {
        return $this->hasMany(UserVip::class);
    }
}
