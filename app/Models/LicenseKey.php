<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseKey extends Model
{
    use HasFactory;

    protected $table = 'license_keys';

    protected $fillable = [
        'token',
        'client_id',
        'endpoint_base',
        'timeout',
    ];

    protected $hidden = ['updated_at'];
} 