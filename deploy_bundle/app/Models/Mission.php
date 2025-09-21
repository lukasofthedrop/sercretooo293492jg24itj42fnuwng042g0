<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    /**
     * Atributos atribuíveis em massa.
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'game_id',
        'target_amount',
        'reward',
        'image',
        'status',
    ];

    /**
     * Relação com o modelo Game (caso necessário).
     */
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    /**
     * Relação com o modelo MissionUser (Progresso dos usuários na missão).
     */
    public function users()
    {
        return $this->hasMany(MissionUser::class, 'mission_id');
    }

    /**
     * Verifica se a missão é do tipo de aposta.
     */
    public function isBetMission(): bool
    {
        return in_array($this->type, ['game_bet', 'total_bet', 'rounds_played', 'win_amount', 'loss_amount']);
    }

    /**
     * Verifica se a missão é do tipo depósito.
     */
    public function isDepositMission(): bool
    {
        return $this->type === 'deposit';
    }
}
