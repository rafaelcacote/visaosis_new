<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoVendaParcela extends Model
{
    use SoftDeletes;

    protected $table = 'pedido_venda_parcela';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'pedido_venda_id',
        'numero_parcela',
        'total_parcelas',
        'valor',
        'vencimento_em',
        'pago_em',
        'vencimento_em',
        'status',
        'forma_pagamento',
        'valor_recebido',
        'valor_desconto',
        'observacoes'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'vencimento_em' => 'date',
        'pago_em' => 'datetime',
        'valor_recebido' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_venda_id');
    }
}
