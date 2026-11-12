<?php

namespace App\Models;

use App\Models\Concerns\HasUuidV4;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasUuidV4, SoftDeletes;

    protected $table = 'categoria_produto';

    // Tipo constants
    const TIPO_PRODUTO = 'produto';
    const TIPO_SERVICO = 'servico';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'descricao',
        'tipo',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Accessors
    public function getTipoLabelAttribute()
    {
        return $this->tipo === self::TIPO_SERVICO ? 'Serviço' : 'Produto';
    }

    public function getStatusAttribute()
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    public function getStatusClassAttribute()
    {
        return $this->ativo ? 'success' : 'danger';
    }

    // Mutators
    public function setDescricaoAttribute($value)
    {
        $this->attributes['descricao'] = mb_strtoupper($value, 'UTF-8');
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'categoria_id');
    }
}
