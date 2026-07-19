<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoVenda extends Model
{
    use SoftDeletes;

    protected $table = 'pedido_venda';

    // Status constants
    const STATUS_ABERTO = 'aberto';
    const STATUS_FATURADO = 'faturado';
    const STATUS_CANCELADO = 'cancelado';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'pessoa_cliente_id',
        'status',
        'data_pedido',
        'valor_total',
        'desconto_valor',
        'desconto_percentual',
        'desconto_autorizado_por',
        'forma_pagamento',
        'observacoes',
        'ativo'
    ];

    protected $casts = [
        'data_pedido' => 'datetime',
        'valor_total' => 'decimal:2',
        'desconto_valor' => 'decimal:2',
        'desconto_percentual' => 'decimal:2',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
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
        return static::where('tenant_id', $tenantId)->where('id', $value)->first();
    }

    /**
     * Relacionamento com o cliente (Pessoa)
     */
    public function cliente()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_cliente_id');
    }

    /**
     * Relacionamento com os itens do pedido
     */
    public function itens()
    {
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }

    /**
     * Relacionamento com as parcelas da venda.
     */
    public function parcelas()
    {
        return $this->hasMany(PedidoVendaParcela::class, 'pedido_venda_id');
    }

    /**
     * Compatibilidade com usos antigos do nome "pagamentos".
     */
    public function pagamentos()
    {
        return $this->parcelas();
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para pedidos abertos
     */
    public function scopeAbertos($query)
    {
        return $query->where('status', 'aberto');
    }

    /**
     * Scope para pedidos faturados
     */
    public function scopeFaturados($query)
    {
        return $query->where('status', 'faturado');
    }

    /**
     * Scope para pedidos cancelados
     */
    public function scopeCancelados($query)
    {
        return $query->where('status', 'cancelado');
    }

    /**
     * Accessor para número do pedido formatado
     */
    public function getNumeroAttribute()
    {
        return str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor para data do pedido formatada
     */
    public function getDataPedidoFormatadaAttribute()
    {
        return $this->data_pedido ? $this->data_pedido->format('d/m/Y H:i') : '';
    }

    /**
     * Accessor para valor total formatado
     */
    public function getValorTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    /**
     * Accessor para status label
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_ABERTO => 'Aberto',
            self::STATUS_FATURADO => 'Faturado',
            self::STATUS_CANCELADO => 'Cancelado',
            default => ucfirst($this->status)
        };
    }
}
