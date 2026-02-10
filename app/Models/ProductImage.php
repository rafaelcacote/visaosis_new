<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use SoftDeletes;

    // Tabela física: comercial.produto_imagem
    protected $table = 'comercial.produto_imagem';

    protected $fillable = [
        'tenant_id',
        'produto_id',
        'user_id',
        'nome_arquivo',
        'caminho_arquivo',
        'ordem',
        'principal',
        'ativo',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}

