<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class MeuPlanoController extends Controller
{
    public function index()
    {
        $tenantId = $this->tenantIdOrAbort();

        $plano = DB::connection('cerberus')
            ->table('seguranca.planos_cliente')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('data_inicio')
            ->first();

        $extrato = collect();

        if ($plano) {
            $extrato = DB::connection('cerberus')
                ->table('seguranca.vw_extrato_mensalidades')
                ->where('tenant_id', $tenantId)
                ->where('plano_cliente_id', $plano->id)
                ->orderByDesc('mes_referencia')
                ->get();
        }

        $resumo = $this->buildResumo($extrato);

        return view('admin.meu-plano.index', compact('plano', 'extrato', 'resumo', 'tenantId'));
    }

    private function buildResumo($extrato): array
    {
        $resumo = [
            'total' => $extrato->count(),
            'pagas' => 0,
            'pendentes' => 0,
            'atrasadas' => 0,
            'valor_pago' => 0.0,
            'valor_pendente' => 0.0,
        ];

        foreach ($extrato as $item) {
            $status = strtoupper($item->status_pagamento ?? 'PENDENTE');

            if (in_array($status, ['PAGO', 'PAGA', 'PAGAMENTO CONFIRMADO'], true)) {
                $resumo['pagas']++;
                $resumo['valor_pago'] += (float) ($item->valor_pagamento ?? $item->valor_esperado ?? 0);
            } elseif (in_array($status, ['ATRASADO', 'ATRASADA', 'VENCIDO'], true)) {
                $resumo['atrasadas']++;
                $resumo['valor_pendente'] += (float) ($item->valor_esperado ?? 0);
            } else {
                $resumo['pendentes']++;
                $resumo['valor_pendente'] += (float) ($item->valor_esperado ?? 0);
            }
        }

        return $resumo;
    }

    private function tenantIdOrAbort(): int
    {
        $tenantId = session('tenant_id');

        if (!$tenantId) {
            abort(403, 'Tenant não identificado na sessão.');
        }

        return (int) $tenantId;
    }
}
