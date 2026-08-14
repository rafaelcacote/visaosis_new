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
        'esfera_od_perto',
        'cilindro_od_perto',
        'eixo_od_perto',
        'esfera_oe_perto',
        'cilindro_oe_perto',
        'eixo_oe_perto',
        'dnp_od_perto',
        'dnp_oe_perto',
        'altura_od_perto',
        'altura_oe_perto',
        'adicao_od_perto',
        'adicao_oe_perto',
        'acuidade_od_perto',
        'acuidade_oe_perto',
        'data_receita',
        'validade_dias',
        'diagnostico',
        'observacoes',
        'recomendacoes',
        'ativo',
        'location_id',
        'tipo_lente',
        'acuidade_od',
        'acuidade_oe',
        'receita_foto_caminho',
    ];

    protected $appends = [
        'receita_foto_url',
    ];

    public function getReceitaFotoUrlAttribute(): ?string
    {
        $path = (string) ($this->attributes['receita_foto_caminho'] ?? '');
        if ($path === '') {
            return null;
        }

        $storagePath = ltrim($path, '/');

        try {
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        return asset('storage/' . $storagePath);
    }

    protected $casts = [
        'ativo' => 'boolean',
        'validade_dias' => 'integer',
        'eixo_od' => 'integer',
        'eixo_oe' => 'integer',
        'eixo_od_perto' => 'integer',
        'eixo_oe_perto' => 'integer',
        'data_receita' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    private static function normalizeEsfericoToStorage($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = is_string($value) ? trim($value) : (string) $value;
        if ($raw === '') {
            return null;
        }

        $upper = strtoupper(str_replace(' ', '', $raw));
        if ($upper === 'PL') {
            return '0';
        }

        $upper = str_replace(',', '.', $upper);
        if (preg_match('/^([+-]?)(\d+(?:\.\d{1,2})?)$/', $upper, $m)) {
            $num = (float) $m[2];
            if (abs($num) < 0.0000001) {
                return '0';
            }

            $sign = $m[1] === '-' ? '-' : '';
            $formatted = number_format($num, 2, '.', '');
            return $sign . $formatted;
        }

        return $raw;
    }

    private static function formatEsfericoForDisplay($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $raw = is_string($value) ? trim($value) : (string) $value;
        if ($raw === '') {
            return null;
        }

        $upper = strtoupper(str_replace(' ', '', $raw));
        if ($upper === 'PL') {
            return 'PL';
        }

        $normalized = str_replace(',', '.', $upper);
        if (preg_match('/^-?\d+(?:\.\d+)?$/', $normalized)) {
            $num = (float) $normalized;
            if (abs($num) < 0.0000001) {
                return 'PL';
            }

            if ($num > 0) {
                return '+' . number_format($num, 2, '.', '');
            }

            return '-' . number_format(abs($num), 2, '.', '');
        }

        return $raw;
    }

    public function setEsferaOdAttribute($value): void
    {
        $this->attributes['esfera_od'] = self::normalizeEsfericoToStorage($value);
    }

    public function setEsferaOeAttribute($value): void
    {
        $this->attributes['esfera_oe'] = self::normalizeEsfericoToStorage($value);
    }

    public function setEsferaOdPertoAttribute($value): void
    {
        $this->attributes['esfera_od_perto'] = self::normalizeEsfericoToStorage($value);
    }

    public function setEsferaOePertoAttribute($value): void
    {
        $this->attributes['esfera_oe_perto'] = self::normalizeEsfericoToStorage($value);
    }

    public function getEsferaOdAttribute($value): ?string
    {
        return self::formatEsfericoForDisplay($value);
    }

    public function getEsferaOeAttribute($value): ?string
    {
        return self::formatEsfericoForDisplay($value);
    }

    public function getEsferaOdPertoAttribute($value): ?string
    {
        return self::formatEsfericoForDisplay($value);
    }

    public function getEsferaOePertoAttribute($value): ?string
    {
        return self::formatEsfericoForDisplay($value);
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id', 'id')->withTrashed();
    }

    public function paciente()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_paciente_id', 'id');
    }
}
