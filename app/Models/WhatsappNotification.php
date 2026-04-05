<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappNotification extends Model
{
    use SoftDeletes;

    protected $table = 'whatsapp_notification';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'user_id',
        'pessoa_id',
        'telefone',
        'tipo',
        'mensagem',
        'status',
        'enviado_em',
        'lido_em',
        'respondido_em',
        'referencia_tipo',
        'referencia_id',
        'wa_url',
        'erro',
        'tentativas',
    ];

    protected $casts = [
        'enviado_em' => 'datetime',
        'lido_em' => 'datetime',
        'respondido_em' => 'datetime',
        'tentativas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
