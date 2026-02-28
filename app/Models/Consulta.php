<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consulta extends Model
{
    use SoftDeletes;

    protected $table = 'consulta';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    // Constantes para status
    const STATUS_AGENDADO = 1;
    const STATUS_AGUARDANDO = 2;
    const STATUS_EM_ATENDIMENTO = 3;
    const STATUS_ATENDIDO = 4;
    const STATUS_CANCELADO = 5;
    const STATUS_ENCAMINHADO = 6;
    const STATUS_FALTOU = 7;

    // Constantes para tipo
    const TIPO_CONSULTA = 1;
    const TIPO_RETORNO = 2;
    const TIPO_CONFERENCIA = 3;

    // Constantes para prioridade
    const PRIORIDADE_NORMAL = 0;
    const PRIORIDADE = 1;
    const PRIORIDADE_EMERGENCIA = 2;

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'pessoa_paciente_id',
        'profissional_id',
        'agendado_em',
        'chegada_em',
        'atendido_em',
        'retorno_em',
        'status',
        'tipo',
        'prioridade',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'status' => 'integer',
        'tipo' => 'integer',
        'prioridade' => 'integer',
        'agendado_em' => 'datetime',
        'chegada_em' => 'datetime',
        'atendido_em' => 'datetime',
        'retorno_em' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

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

    // Accessors
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case self::STATUS_AGENDADO:
                return 'Agendado';
            case self::STATUS_AGUARDANDO:
                return 'Aguardando';
            case self::STATUS_EM_ATENDIMENTO:
                return 'Em Atendimento';
            case self::STATUS_ATENDIDO:
                return 'Atendido';
            case self::STATUS_CANCELADO:
                return 'Cancelado';
            case self::STATUS_ENCAMINHADO:
                return 'Encaminhado';
            case self::STATUS_FALTOU:
                return 'Faltou';
            default:
                return 'Não informado';
        }
    }

    public function getTipoLabelAttribute()
    {
        switch ($this->tipo) {
            case self::TIPO_CONSULTA:
                return 'Consulta';
            case self::TIPO_RETORNO:
                return 'Retorno';
            case self::TIPO_CONFERENCIA:
                return 'Conferência';
            default:
                return 'Não informado';
        }
    }

    public function getPrioridadeLabelAttribute()
    {
        switch ($this->prioridade) {
            case self::PRIORIDADE_NORMAL:
                return 'Normal';
            case self::PRIORIDADE:
                return 'Prioridade';
            case self::PRIORIDADE_EMERGENCIA:
                return 'Emergência';
            default:
                return 'Não informado';
        }
    }

    // Relationships
    public function paciente()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_paciente_id', 'id');
    }

    public function profissional()
    {
        return $this->belongsTo(Profissional::class, 'profissional_id', 'id');
    }

    public function prescricao()
    {
        return $this->hasOne(Prescricao::class, 'consulta_id', 'id');
    }

    public function exame()
    {
        return $this->hasOne(Exame::class, 'consulta_id', 'id');
    }

    public function encaminhamento()
    {
        return $this->hasOne(Encaminhamento::class, 'consulta_id', 'id');
    }

    /**
     * Retorna a data atendido_em da última consulta com status = 4 (Atendido) para o paciente informado
     */
    public static function getUltimaConsulta($pessoa_paciente_id)
    {
        $consulta = self::where('pessoa_paciente_id', $pessoa_paciente_id)
            ->where('status', self::STATUS_ATENDIDO)
            ->whereNotNull('atendido_em')
            ->orderByDesc('atendido_em')
            ->first();

        return $consulta ? $consulta->atendido_em : null;
    }
}
