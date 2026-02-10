<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidade extends Model
{
    protected $table = 'especialidade';

    protected $fillable = [
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // Accessors
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
    public function profissionais()
    {
        return $this->hasMany(Profissional::class, 'especialidade_id');
    }
}
