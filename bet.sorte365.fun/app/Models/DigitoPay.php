<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitoPay extends Model
{
    use HasFactory;

    protected $table = 'digito_pay';

    protected $fillable = [
        'user_id',
        'withdrawal_id',
        'amount',
        'status'
    ];
}
