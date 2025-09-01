<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocao extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'promocoes';

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'imagem',
        'titulo',
        'link',
        'regras_html',
    ];

    /**
     * Configuração adicional, caso necessário.
     */
    public $timestamps = true; // Define se timestamps (created_at e updated_at) serão usados

    // Adicione aqui quaisquer métodos ou relacionamentos adicionais
}
