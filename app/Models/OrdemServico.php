<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdemServico extends Model
{
    use SoftDeletes;

    protected $table = 'ordem_servico';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tenant_id',
        'pedido_id',
        'prescricao_id',
        'user_id',
        'fornecedor_id',
        'quantidade',
        'preco_unit',
        'desconto',
        'total_linha',
        'atributos',
        'entrega_em',
        'prioridade',
        'status',
        'observacoes',
        'ativo',
        'location_id'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unit' => 'decimal:2',
        'desconto' => 'decimal:2',
        'total_linha' => 'decimal:2',
        'atributos' => 'array',
        'entrega_em' => 'datetime',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constantes para status
    const STATUS_PENDENTE = 'pendente';
    const STATUS_ENVIADO = 'enviado';
    const STATUS_EM_PRODUCAO = 'em_producao';
    const STATUS_PRONTO = 'pronto';
    const STATUS_ENTREGUE = 'entregue';
    const STATUS_CANCELADO = 'cancelado';

    // Constantes para prioridade
    const PRIORIDADE_NORMAL = 'normal';
    const PRIORIDADE_URGENTE = 'urgente';
    const PRIORIDADE_EXPRESSA = 'expressa';

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_ENVIADO => 'Enviado',
            self::STATUS_EM_PRODUCAO => 'Em Produção',
            self::STATUS_PRONTO => 'Pronto',
            self::STATUS_ENTREGUE => 'Entregue',
            self::STATUS_CANCELADO => 'Cancelado',
            default => 'Não informado'
        };
    }

    public function getPrioridadeLabelAttribute()
    {
        return match ($this->prioridade) {
            self::PRIORIDADE_NORMAL => 'Normal',
            self::PRIORIDADE_URGENTE => 'Urgente',
            self::PRIORIDADE_EXPRESSA => 'Expressa',
            default => 'Normal'
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDENTE => 'warning',
            self::STATUS_ENVIADO => 'info',
            self::STATUS_EM_PRODUCAO => 'primary',
            self::STATUS_PRONTO => 'success',
            self::STATUS_ENTREGUE => 'dark',
            self::STATUS_CANCELADO => 'danger',
            default => 'secondary'
        };
    }

    public function getPrioridadeClassAttribute()
    {
        return match ($this->prioridade) {
            self::PRIORIDADE_NORMAL => 'primary',
            self::PRIORIDADE_URGENTE => 'warning',
            self::PRIORIDADE_EXPRESSA => 'danger',
            default => 'primary'
        };
    }

    public function getTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->total_linha, 2, ',', '.');
    }

    public function getPrecoUnitFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_unit, 2, ',', '.');
    }

    public function getDescontoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->desconto, 2, ',', '.');
    }

    // Relacionamentos
    public function pedido()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_id', 'id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Laboratorio::class, 'fornecedor_id', 'id');
    }

    public function prescricao()
    {
        return $this->belongsTo(Prescricao::class, 'prescricao_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function itensOrdem()
    {
        return $this->hasMany(ItemOrdem::class, 'ordem_id', 'id');
    }

    public function itens()
    {
        return $this->hasManyThrough(
            ItemPedido::class,
            ItemOrdem::class,
            'ordem_id',  // Foreign key em ItemOrdem
            'id',        // Foreign key em ItemPedido
            'id',        // Local key em OrdemServico
            'item_id'    // Local key em ItemOrdem
        );
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorPrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    public function scopePorFornecedor($query, $fornecedorId)
    {
        return $query->where('fornecedor_id', $fornecedorId);
    }

    // Métodos auxiliares
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_ENVIADO => 'Enviado',
            self::STATUS_EM_PRODUCAO => 'Em Produção',
            self::STATUS_PRONTO => 'Pronto',
            self::STATUS_ENTREGUE => 'Entregue',
            self::STATUS_CANCELADO => 'Cancelado'
        ];
    }

    public static function getPrioridadeOptions()
    {
        return [
            self::PRIORIDADE_NORMAL => 'Normal',
            self::PRIORIDADE_URGENTE => 'Urgente',
            self::PRIORIDADE_EXPRESSA => 'Expressa'
        ];
    }
}
