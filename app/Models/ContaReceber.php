<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaReceber extends Model
{
    use SoftDeletes;

    protected $table = 'conta_receber';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'pedido_venda_id',
        'pessoa_cliente_id',
        'numero_parcela',
        'total_parcelas',
        'valor_parcela',
        'valor_total_venda',
        'data_vencimento',
        'data_pagamento',
        'forma_pagamento',
        'status',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'valor_parcela' => 'decimal:2',
        'valor_total_venda' => 'decimal:2',
        'data_vencimento' => 'date',
        'data_pagamento' => 'datetime',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pedidoVenda()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_venda_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_cliente_id');
    }
}
