<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encaminhamento extends Model
{
    use SoftDeletes;

    protected $table = 'encaminhamento';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'tenant_id',
        'consulta_id',
        'especialidade_id',
        'user_id',
        'usuario_oculos',
        'ultima_avaliacao_em',
        'hipotese',
        'urgencia',
        'observacoes',
        'ativo',
        'location_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'usuario_oculos' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'ultima_avaliacao_em' => 'date',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id', 'id')
            ->where('tenant_id', $this->tenant_id);
    }

    public function especialidade()
    {
        return $this->belongsTo(Especialidade::class, 'especialidade_id', 'id');
    }
}
