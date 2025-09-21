<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPlayFiver extends Model
{
    use HasFactory;
    protected $table = 'configs_playfiver';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rtp',
        'limit_enable',
        'limit_amount',
        'limit_hours',
        'bonus_enable',
        'edit'
    ];

}
