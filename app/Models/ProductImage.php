<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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

    public function getUrlAttribute(): ?string
    {
        if (!$this->caminho_arquivo) {
            return null;
        }

        $storagePath = ltrim($this->caminho_arquivo, '/');

        if (!Storage::disk('public')->exists($storagePath)) {
            return null;
        }

        return asset('storage/' . $storagePath);
    }
}

