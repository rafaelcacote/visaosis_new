<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPedido extends Model
{
    use SoftDeletes;

    protected $table = 'item_pedido';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tenant_id',
        'pedido_id',
        'produto_id',
        'user_id',
        'location_id',
        'quantidade',
        'preco_unit',
        'desconto',
        'total_linha',
        'ativo'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unit' => 'decimal:2',
        'desconto' => 'decimal:2',
        'total_linha' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento com o pedido de venda
     */
    public function pedido()
    {
        return $this->belongsTo(PedidoVenda::class, 'pedido_id');
    }

    /**
     * Relacionamento com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
