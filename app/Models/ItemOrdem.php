<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemOrdem extends Model
{
    use SoftDeletes;

    protected $table = 'item_ordem';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'ordem_id',
        'item_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relacionamento com a ordem de serviço
     */
    public function ordem()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_id', 'id');
    }

    /**
     * Relacionamento com o item do pedido
     */
    public function item()
    {
        return $this->belongsTo(ItemPedido::class, 'item_id', 'id');
    }
}
