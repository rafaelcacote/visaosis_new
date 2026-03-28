<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescricao extends Model
{
    use SoftDeletes;

    protected $table = 'prescricao';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'tenant_id',
        'consulta_id',
        'user_id',
        'pessoa_paciente_id',
        'especialista_externo',
        'esfera_od',
        'cilindro_od',
        'eixo_od',
        'esfera_oe',
        'cilindro_oe',
        'eixo_oe',
        'dnp_od',
        'dnp_oe',
        'altura_od',
        'altura_oe',
        'adicao_od',
        'adicao_oe',
        'validade_dias',
        'diagnostico',
        'observacoes',
        'recomendacoes',
        'ativo',
        'location_id',
        'tipo_lente',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'validade_dias' => 'integer',
        'eixo_od' => 'integer',
        'eixo_oe' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id', 'id')->withTrashed();
    }

    public function paciente()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_paciente_id', 'id');
    }
}
