<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profissional extends Model
{
    use SoftDeletes;

    protected $table = 'profissional';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'especialidade_id',
        'nome',
        'cpf',
        'registro_conselho',
        'sexo',
        'nascimento_em',
        'telefone',
        'email',
        'chave_pix',
        'ativo',
        'pausar_atendimento',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'sexo' => 'integer',
        'nascimento_em' => 'date',
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
    public function getCpfFormatadoAttribute()
    {
        if (!$this->cpf) {
            return '';
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    public function getTelefoneFormatadoAttribute()
    {
        if (!$this->telefone) {
            return '';
        }

        $telefone = preg_replace('/[^0-9]/', '', $this->telefone);

        if (strlen($telefone) == 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        } elseif (strlen($telefone) == 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }

        return $this->telefone;
    }

    public function getSexoTextoAttribute()
    {
        switch ($this->sexo) {
            case 1:
                return 'Masculino';
            case 2:
                return 'Feminino';
            default:
                return 'Não informado';
        }
    }

    public function getIdadeAttribute()
    {
        if (!$this->nascimento_em) {
            return null;
        }

        return $this->nascimento_em->diffInYears(now());
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
    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    public function setTelefoneAttribute($value)
    {
        $this->attributes['telefone'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = mb_strtoupper($value, 'UTF-8');
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? mb_strtolower($value, 'UTF-8') : null;
    }

    // Relacionamentos
    public function especialidade()
    {
        return $this->belongsTo(Especialidade::class, 'especialidade_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
