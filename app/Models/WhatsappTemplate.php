<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'whatsapp_template';

    protected $fillable = [
        'tenant_id',
        'location_id',
        'tipo',
        'titulo',
        'mensagem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

