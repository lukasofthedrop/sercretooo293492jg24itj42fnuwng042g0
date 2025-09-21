<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinConfigs extends Model
{
    protected $appends = ['prizesArray'];

    protected $table = 'ggds_spin_config';

    protected $fillable = [
        'prizes'
    ];

    /**
     * Obtém o array de prêmios decodificado de JSON.
     * 
     * @return array
     */
    public function getPrizesArrayAttribute()
    {
        return json_decode($this->attributes['prizes'], true);
    }
}
