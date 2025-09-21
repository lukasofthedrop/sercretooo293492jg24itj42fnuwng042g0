<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BsPay extends Model
{
    use HasFactory;

    protected $table = 'bs_pays';

    protected $fillable = [
        'user_id',
        'withdrawal_id',
        'amount',
        'status'
    ];
}
