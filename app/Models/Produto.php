<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use SoftDeletes;

    protected $table = 'produto';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'nome',
        'categoria_id',
        'marca',
        'atributos',
        'preco_custo',
        'preco_venda',
        'ativo',
    ];

    protected $casts = [
        'atributos' => 'array',
        'preco_custo' => 'decimal:2',
        'preco_venda' => 'decimal:2',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Chave primária composta (tenant_id, id). Para rotas usamos apenas id.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Resolve model pela URL considerando tenant (chave composta).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $tenantId = session('tenant_id');
        if (!$tenantId) {
            return null;
        }
        return static::where('tenant_id', $tenantId)->where('id', $value)->first();
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function getStatusAttribute()
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    public function getStatusClassAttribute()
    {
        return $this->ativo ? 'success' : 'danger';
    }

    /** Preço de custo formatado. */
    public function getPrecoCustoFormatadoAttribute()
    {
        return $this->preco_custo !== null
            ? 'R$ ' . number_format($this->preco_custo, 2, ',', '.')
            : '—';
    }

    /** Preço de venda formatado. */
    public function getPrecoVendaFormatadoAttribute()
    {
        return $this->preco_venda !== null
            ? 'R$ ' . number_format($this->preco_venda, 2, ',', '.')
            : '—';
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'produto_id');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'produto_id')
            ->whereNull('deleted_at')
            ->orderByDesc('principal')
            ->orderBy('ordem')
            ->orderBy('id');
    }

    public function getImagemPrincipalUrlAttribute(): ?string
    {
        if ($this->relationLoaded('primaryImage')) {
            return $this->primaryImage?->url;
        }

        if ($this->relationLoaded('images')) {
            return $this->images->first()?->url;
        }

        $image = $this->images()
            ->whereNull('deleted_at')
            ->orderByDesc('principal')
            ->orderBy('ordem')
            ->orderBy('id')
            ->first();

        return $image?->url;
    }
}
