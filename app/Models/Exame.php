<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exame extends Model
{
    use SoftDeletes;

    protected $table = 'exame';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'tenant_id',
        'consulta_id',
        'user_id',
        'anamnese',
        'acuidade_od',
        'acuidade_oe',
        'pressao_od',
        'pressao_oe',
        'observacoes',
        'fundoscopia',
        'ativo',
        'location_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id', 'id')
            ->where('tenant_id', $this->tenant_id);
    }
}
