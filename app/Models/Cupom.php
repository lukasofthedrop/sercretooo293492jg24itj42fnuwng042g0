<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cupons'; // Nome correto da tabela

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'codigo',
        'valor_bonus',
        'validade',
        'quantidade_uso',
        'usos',
    ];

    /**
     * Check if the coupon is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->validade >= now() && $this->usos < $this->quantidade_uso;
    }
}
