<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

/**
 * Chave primária UUID versão 4 aleatoria, consistente com o default
 * gen_random_uuid() das colunas uuid no PostgreSQL.
 */
trait HasUuidV4
{
    use HasUuids;

    public function newUniqueId()
    {
        return (string) Str::uuid();
    }
}
