<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta_arrecadacao',
        'percentual_distribuicao',
        'rtp_arrecadacao',
        'rtp_distribuicao',
        'total_arrecadado',
        'total_distribuido',
        'modo',
        'ativo',
        'start_cycle_at', // nova coluna
    ];

    protected $casts = [
        'start_cycle_at' => 'datetime',
    ];
}
