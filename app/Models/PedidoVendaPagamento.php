<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoVendaPagamento extends Model
{
    use SoftDeletes;

    protected $table = 'pedido_venda_pagamento';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'pedido_venda_id',
        'forma_pagamento',
        'valor',
        'parcelas',
    ];

    protected $casts = [
        'valor'      => 'decimal:2',
        'parcelas'   => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_venda_id');
    }
}
