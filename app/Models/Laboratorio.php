<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laboratorio extends Model
{
    use SoftDeletes;

    protected $table = 'fornecedor';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'telefone',
        'email',
        'chave_pix',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
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

    /** Nome para exibição (usado em listas e modais). */
    public function getNomeAttribute()
    {
        return $this->nome_fantasia ?: $this->razao_social;
    }

    /** CNPJ formatado (99.999.999/9999-99). */
    public function getCnpjFormatadoAttribute()
    {
        if (!$this->cnpj || strlen($this->cnpj) !== 14) {
            return $this->cnpj ?? '';
        }
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $this->cnpj);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
