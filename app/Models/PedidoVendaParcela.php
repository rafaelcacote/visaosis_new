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
        'status',
        'forma_pagamento',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'vencimento_em' => 'date',
        'pago_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_venda_id');
    }
}
