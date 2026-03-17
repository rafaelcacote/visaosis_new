<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pessoa extends Model
{
    use SoftDeletes;

    /**
     * Nome da tabela no banco.
     *
     * Importante: a tabela física é "geral.pessoa", mas o schema
     * é configurado na conexão do banco. Aqui usamos apenas "pessoa",
     * como no projeto original.
     */
    protected $table = 'pessoa';

    /**
     * Chave primária simples (id).
     * No banco existe chave composta (tenant_id, id), mas para o
     * Eloquent usamos somente "id" e filtramos por tenant_id nas queries.
     */
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nome',
        'cpf',
        'nome_mae',
        'nome_pai',
        'sexo',
        'nascimento_em',
        'deficiencia',
        'cep',
        'logradouro',
        'complemento',
        'bairro',
        'localidade',
        'uf',
        'numero',
        'telefone',
        'email',
        'ativo',
        'tenant_id',
        'location_id',
        'user_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'sexo' => 'integer',
        'nascimento_em' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Constantes para sexo
    public const SEXO_MASCULINO = 1;
    public const SEXO_FEMININO = 2;

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

        return static::where('tenant_id', $tenantId)
            ->where('id', $value)
            ->first();
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeMasculino($query)
    {
        return $query->where('sexo', self::SEXO_MASCULINO);
    }

    public function scopeFeminino($query)
    {
        return $query->where('sexo', self::SEXO_FEMININO);
    }

    // Relações
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessors
    public function getSexoLabelAttribute()
    {
        if ($this->sexo === self::SEXO_MASCULINO) {
            return 'Masculino';
        }

        if ($this->sexo === self::SEXO_FEMININO) {
            return 'Feminino';
        }

        return 'Não informado';
    }

    public function getStatusLabelAttribute()
    {
        return $this->ativo ? 'Ativo' : 'Inativo';
    }

    public function getCpfFormatadoAttribute()
    {
        if (!$this->cpf) {
            return null;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    public function getTelefoneFormatadoAttribute()
    {
        if (!$this->telefone) {
            return null;
        }

        $telefone = preg_replace('/\D/', '', $this->telefone);

        if (strlen($telefone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        }

        if (strlen($telefone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }

        return $this->telefone;
    }

    public function getCepFormatadoAttribute()
    {
        if (!$this->cep) {
            return null;
        }

        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->cep);
    }

    public function getEnderecoCompletoAttribute()
    {
        $endereco = $this->logradouro . ', ' . $this->numero;

        if ($this->complemento) {
            $endereco .= ' - ' . $this->complemento;
        }

        $endereco .= ' - ' . $this->bairro . ' - ' . $this->localidade . '/' . $this->uf;

        if ($this->cep) {
            $endereco .= ' - CEP: ' . $this->cep_formatado;
        }

        return $endereco;
    }

    public function getIdadeAttribute()
    {
        if (!$this->nascimento_em) {
            return null;
        }

        return $this->nascimento_em->age;
    }

    // Mutators
    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = mb_strtoupper($value, 'UTF-8');
    }

    public function setCpfAttribute($value)
    {
        $this->attributes['cpf'] = $value !== null
            ? preg_replace('/\D/', '', $value)
            : null;
    }

    public function setTelefoneAttribute($value)
    {
        $this->attributes['telefone'] = $value !== null
            ? preg_replace('/\D/', '', $value)
            : null;
    }

    public function setCepAttribute($value)
    {
        $this->attributes['cep'] = $value !== null
            ? preg_replace('/\D/', '', $value)
            : null;
    }

    public function getUltimaConsultaAttribute()
    {
        $consulta = \App\Models\Consulta::where('pessoa_paciente_id', $this->id)
            ->where('status', \App\Models\Consulta::STATUS_ATENDIDO)
            ->whereNotNull('atendido_em')
            ->with('profissional.especialidade')
            ->orderByDesc('atendido_em')
            ->first();
        return $consulta ? $consulta->atendido_em : null;
    }
}
