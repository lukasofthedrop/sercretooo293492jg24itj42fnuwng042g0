<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionUser extends Model
{
    use HasFactory;

    /**
     * Atributos atribuíveis em massa.
     */
    protected $fillable = [
        'user_id',
        'mission_id',
        'reward',
        'redeemed',
    ];

    /**
     * Relação com o modelo Mission.
     */
    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    /**
     * Relação com o modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
