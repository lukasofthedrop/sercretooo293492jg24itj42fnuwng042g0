<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    use HasFactory;

    /**
     * A tabela do banco de dados usada pelo modelo.
     *
     * @var string
     */
    protected $table = 'games';

    /**
     * A chave primária associada à tabela.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'game_server_url',
        'game_id',
        'game_name',
        'game_code',
        'game_type',
        'description',
        'cover',
        'status',
        'technology',
        'has_lobby',
        'is_mobile',
        'has_freespins',
        'has_tables',
        'only_demo',
        'rtp',
        'distribution',
        'views',
        'is_featured',
        'show_home',
        'original'
    ];

    /**
     * Relacionamento com a tabela providers.
     *
     * @return BelongsTo
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    /**
     * Relacionamento com a tabela categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Retorna a data de criação formatada.
     *
     * @return string
     */
    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y-m-d');
    }

    /**
     * Retorna a data de criação de forma legível.
     *
     * @return string
     */
    public function getDateHumanReadableAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
