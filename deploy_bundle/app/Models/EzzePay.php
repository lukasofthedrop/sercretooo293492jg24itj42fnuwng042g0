<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EzzePay extends Model
{
    use HasFactory;
    protected $table = 'ezzepay';

    protected $fillable = [
        'user_id',
        'withdrawal_id',
        'amount',
        'status'
    ];

}
