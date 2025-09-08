<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateWithdraw extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'affiliate_withdraws';
    protected $appends = ['dateHumanReadable', 'createdAt'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'user_id',
        'amount',
        'amount_display',
        'amount_real',
        'type',
        'bank_info',
        'admin_notes',
        'type',
        'proof',
        'pix_key',
        'pix_type',
        'currency',
        'symbol',
        'status',
        'cpf',
        'name',
        'processed_at'
    ];


    /**
     * @return mixed
     */
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at']);
    }

    /**
     * @return mixed
     */
    public function getDateHumanReadableAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
